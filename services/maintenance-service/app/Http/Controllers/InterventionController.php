<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceIntervention;
use App\Models\MaintenancePlan;
use App\Services\AssetServiceClient;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InterventionController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceIntervention::with(['plan', 'reporter'])->latest('scheduled_date')->latest();

        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->asset_id);
        }

        if ($request->filled('status')) {
            $query->where('status', strtoupper($request->status));
        }

        if ($request->filled('type')) {
            $query->where('type', strtoupper($request->type));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_date', '<=', $request->date_to);
        }

        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);

        return response()->json($query->paginate($perPage));
    }

    public function stats()
    {
        $byStatus = MaintenanceIntervention::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $costByType = MaintenanceIntervention::query()
            ->selectRaw('type, coalesce(sum(cost), 0) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $durations = MaintenanceIntervention::query()
            ->whereNotNull('started_at')
            ->whereNotNull('ended_at')
            ->get(['started_at', 'ended_at'])
            ->map(fn ($intervention) => $intervention->started_at->diffInMinutes($intervention->ended_at));

        return response()->json([
            'by_status' => $byStatus,
            'total_cost' => (float) MaintenanceIntervention::sum('cost'),
            'average_duration_hours' => $durations->isEmpty() ? null : round($durations->avg() / 60, 2),
            'cost_by_type' => $costByType,
        ]);
    }

    public function show($id)
    {
        return response()->json(
            MaintenanceIntervention::with(['plan', 'reporter'])->findOrFail($id)
        );
    }

    public function store(Request $request, AssetServiceClient $assetService)
    {
        $validator = Validator::make($request->all(), [
            'asset_id' => ['required', 'uuid'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'scheduled_date' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
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

        if (in_array($asset['status'] ?? null, ['REFORME', 'CEDE'], true)) {
            return response()->json([
                'message' => 'Ce bien ne peut plus faire l\'objet d\'une intervention.',
            ], 422);
        }

        $intervention = MaintenanceIntervention::create([
            'asset_id' => $data['asset_id'],
            'type' => 'CORRECTIVE',
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => 'EN_COURS',
            'reported_by' => Auth::guard('api')->id(),
            'scheduled_date' => $data['scheduled_date'] ?? now()->toDateString(),
            'started_at' => now(),
        ]);

        try {
            $updateResponse = $assetService->updateStatus($data['asset_id'], 'EN_MAINTENANCE', $token);
        } catch (ConnectionException) {
            $intervention->delete();

            return $this->assetServiceUnavailable();
        }

        if (! $updateResponse->successful()) {
            $intervention->delete();

            return $this->assetServiceUnavailable($updateResponse->status());
        }

        return response()->json($intervention->load(['plan', 'reporter']), 201);
    }

    public function start($id, AssetServiceClient $assetService)
    {
        $intervention = MaintenanceIntervention::findOrFail($id);

        if ($intervention->status !== 'PLANIFIEE') {
            return response()->json(['message' => 'Seule une intervention planifiée peut être démarrée.'], 422);
        }

        $token = request()->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        try {
            $updateResponse = $assetService->updateStatus($intervention->asset_id, 'EN_MAINTENANCE', $token);
        } catch (ConnectionException) {
            return $this->assetServiceUnavailable();
        }

        if (! $updateResponse->successful()) {
            return $this->assetServiceUnavailable($updateResponse->status());
        }

        $intervention->update([
            'status' => 'EN_COURS',
            'started_at' => now(),
        ]);

        return response()->json($intervention->load(['plan', 'reporter']));
    }

    public function close(Request $request, $id, AssetServiceClient $assetService)
    {
        $intervention = MaintenanceIntervention::findOrFail($id);

        if ($intervention->status !== 'EN_COURS') {
            return response()->json(['message' => 'Seule une intervention en cours peut être clôturée.'], 422);
        }

        $validator = Validator::make($request->all(), [
            'cost' => ['nullable', 'numeric', 'min:0'],
            'provider' => ['nullable', 'string', 'max:255'],
            'report' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        try {
            $updateResponse = $assetService->updateStatus($intervention->asset_id, 'EN_SERVICE', $token);
        } catch (ConnectionException) {
            return $this->assetServiceUnavailable();
        }

        if (! $updateResponse->successful()) {
            return $this->assetServiceUnavailable($updateResponse->status());
        }

        $data = $validator->validated();

        DB::transaction(function () use ($intervention, $data) {
            $intervention->update([
                'status' => 'TERMINEE',
                'ended_at' => now(),
                'cost' => $data['cost'] ?? null,
                'provider' => $data['provider'] ?? null,
                'report' => $data['report'] ?? null,
            ]);

            if ($intervention->plan_id && ($plan = MaintenancePlan::find($intervention->plan_id))) {
                $plan->update([
                    'next_due_date' => $plan->next_due_date->copy()->addDays($plan->periodicity_days),
                ]);
            }
        });

        return response()->json($intervention->fresh()->load(['plan', 'reporter']));
    }

    public function cancel($id, AssetServiceClient $assetService)
    {
        $intervention = MaintenanceIntervention::findOrFail($id);

        if (! in_array($intervention->status, ['PLANIFIEE', 'EN_COURS'], true)) {
            return response()->json(['message' => 'Cette intervention ne peut plus être annulée.'], 422);
        }

        $token = request()->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        if ($intervention->status === 'EN_COURS') {
            try {
                $updateResponse = $assetService->updateStatus($intervention->asset_id, 'EN_SERVICE', $token);
            } catch (ConnectionException) {
                return $this->assetServiceUnavailable();
            }

            if (! $updateResponse->successful()) {
                return $this->assetServiceUnavailable($updateResponse->status());
            }
        }

        $intervention->update(['status' => 'ANNULEE']);

        return response()->json($intervention->load(['plan', 'reporter']));
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
