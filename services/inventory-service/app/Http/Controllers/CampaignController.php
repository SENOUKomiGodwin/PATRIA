<?php

namespace App\Http\Controllers;

use App\Models\InventoryCampaign;
use App\Models\InventoryLine;
use App\Services\AssetServiceClient;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryCampaign::withCount('lines')->latest();

        if ($request->filled('status')) {
            $query->where('status', strtoupper($request->status));
        }

        return response()->json($query->paginate(min(max((int) $request->input('per_page', 15), 1), 100)));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $campaign = InventoryCampaign::create($validator->validated() + ['status' => 'OUVERTE']);

        return response()->json($campaign, 201);
    }

    public function show($id)
    {
        return response()->json(
            InventoryCampaign::with(['lines.user'])->findOrFail($id)
        );
    }

    public function addLine(Request $request, $id, AssetServiceClient $assetService)
    {
        $validator = Validator::make($request->all(), [
            'asset_code' => ['required', 'string', 'max:255'],
            'found_location_id' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $campaign = InventoryCampaign::findOrFail($id);

        if ($campaign->status !== 'OUVERTE') {
            return response()->json(['message' => 'La campagne est clôturée.'], 422);
        }

        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        $validated = $validator->validated();
        $assetCode = $validated['asset_code'];
        $foundLocationId = (int) $validated['found_location_id'];

        try {
            $response = $assetService->searchAssets([
                'search' => $assetCode,
                'per_page' => 100,
            ], $token);
        } catch (ConnectionException) {
            return $this->assetServiceUnavailable();
        }

        if (! $response->successful()) {
            return $this->assetServiceUnavailable($response->status());
        }

        $asset = collect($response->json('data') ?? [])->first(function ($item) use ($assetCode) {
            return in_array($assetCode, [$item['code'] ?? null, $item['qr_code'] ?? null], true);
        });

        if (! $asset) {
            return response()->json(['message' => 'Bien introuvable'], 404);
        }

        $expectedLocationId = $asset['location_id'] ?? null;
        $result = $expectedLocationId !== null && (int) $expectedLocationId === $foundLocationId
            ? 'CONFORME'
            : 'ECART';

        $line = InventoryLine::where('campaign_id', $campaign->id)
            ->where('asset_id', $asset['id'])
            ->first();

        $attributes = [
            'expected_location_id' => $expectedLocationId,
            'found_location_id' => $foundLocationId,
            'result' => $result,
            'counted_by' => Auth::guard('api')->id(),
        ];

        if ($line) {
            $line->update($attributes);

            return response()->json($line->load('user'), 200);
        }

        $line = InventoryLine::create($attributes + [
            'campaign_id' => $campaign->id,
            'asset_id' => $asset['id'],
        ]);

        return response()->json($line->load('user'), 201);
    }

    public function close($id, AssetServiceClient $assetService)
    {
        $campaign = InventoryCampaign::findOrFail($id);

        if ($campaign->status !== 'OUVERTE') {
            return response()->json(['message' => 'La campagne est déjà clôturée.'], 422);
        }

        $token = request()->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        try {
            $assets = $assetService->getAllAssets($token);
        } catch (ConnectionException) {
            return $this->assetServiceUnavailable();
        }

        if ($assets === null) {
            return $this->assetServiceUnavailable();
        }

        $countedAssetIds = $campaign->lines()->pluck('asset_id')->all();

        foreach ($assets as $asset) {
            if (in_array($asset['id'] ?? null, $countedAssetIds, true)) {
                continue;
            }

            InventoryLine::firstOrCreate(
                [
                    'campaign_id' => $campaign->id,
                    'asset_id' => $asset['id'],
                ],
                [
                    'expected_location_id' => $asset['location_id'] ?? null,
                    'found_location_id' => null,
                    'result' => 'MANQUANT',
                    'counted_by' => null,
                ]
            );
        }

        $campaign->update(['status' => 'CLOTUREE']);

        return response()->json($campaign->load('lines.user'));
    }

    public function discrepancies($id)
    {
        InventoryCampaign::findOrFail($id);

        return response()->json(
            InventoryLine::with('user')
                ->where('campaign_id', $id)
                ->whereIn('result', ['ECART', 'MANQUANT'])
                ->latest()
                ->get()
        );
    }

    private function assetServiceUnavailable(?int $status = null)
    {
        $message = 'Le service asset-service est indisponible.';

        if ($status !== null) {
            $message .= " Réponse reçue : {$status}.";
        }

        return response()->json(['message' => $message], 502);
    }
}
