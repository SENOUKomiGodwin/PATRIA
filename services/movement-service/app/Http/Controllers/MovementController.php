<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Services\AssetServiceClient;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MovementController extends Controller
{
    public function index(Request $request)
    {
        $query = Movement::with('user')->latest();

        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->asset_id);
        }

        if ($request->filled('type')) {
            $query->where('type', strtoupper($request->type));
        }

        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);

        return response()->json($query->paginate($perPage));
    }

    public function show($id)
    {
        return response()->json(Movement::with('user')->findOrFail($id));
    }

    public function store(Request $request, AssetServiceClient $assetService)
    {
        $validator = Validator::make($request->all(), [
            'asset_id' => ['required', 'uuid'],
            'type' => ['required', Rule::in(['AFFECTATION', 'TRANSFERT', 'CESSION', 'REFORME'])],
            'to_service_id' => ['nullable', 'string', 'max:255'],
            'to_location_id' => ['nullable', 'integer'],
            'movement_date' => ['nullable', 'date'],
            'reason' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $type = $data['type'];

        if ($type === 'TRANSFERT' && empty($data['to_service_id'])) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => [
                    'to_service_id' => ['Le champ to_service_id est requis pour un transfert.'],
                ],
            ], 422);
        }

        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        try {
            $assetResponse = $assetService->getAsset($data['asset_id'], $token);
        } catch (ConnectionException) {
            return $this->assetServiceUnavailable();
        }

        if ($assetResponse->status() === 404) {
            return response()->json(['message' => 'Bien introuvable'], 404);
        }

        if (! $assetResponse->successful()) {
            return $this->assetServiceUnavailable($assetResponse->status());
        }

        $asset = $assetResponse->json();
        $currentServiceId = $asset['service_id'] ?? null;

        if (in_array($asset['status'] ?? null, ['REFORME', 'CEDE'], true)) {
            return response()->json([
                'message' => 'Ce bien ne peut plus être déplacé.',
            ], 422);
        }

        if ($type === 'TRANSFERT' && $data['to_service_id'] === $currentServiceId) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => [
                    'to_service_id' => ['Le service de destination doit être différent du service actuel.'],
                ],
            ], 422);
        }

        try {
            $updateResponse = match ($type) {
                'CESSION' => $assetService->updateStatus($data['asset_id'], 'CEDE', $token),
                'REFORME' => $assetService->updateStatus($data['asset_id'], 'REFORME', $token),
                default => $assetService->updateAssignment($data['asset_id'], array_filter([
                    'service_id' => $data['to_service_id'] ?? null,
                    'location_id' => $data['to_location_id'] ?? null,
                ], fn ($value) => $value !== null), $token),
            };
        } catch (ConnectionException) {
            return $this->assetServiceUnavailable();
        }

        if (! $updateResponse->successful()) {
            return $this->assetServiceUnavailable($updateResponse->status());
        }

        $isAssignment = in_array($type, ['AFFECTATION', 'TRANSFERT'], true);

        $movement = new Movement([
            'asset_id' => $data['asset_id'],
            'type' => $type,
            'from_service_id' => $currentServiceId,
            'to_service_id' => $isAssignment ? ($data['to_service_id'] ?? null) : null,
            'to_location_id' => $isAssignment ? ($data['to_location_id'] ?? null) : null,
            'movement_date' => $data['movement_date'] ?? now()->toDateString(),
            'reason' => $data['reason'] ?? null,
            'user_id' => Auth::guard('api')->id(),
        ]);
        $movement->save();

        return response()->json($movement->load('user'), 201);
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
