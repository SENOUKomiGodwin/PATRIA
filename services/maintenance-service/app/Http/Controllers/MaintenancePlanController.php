<?php

namespace App\Http\Controllers;

use App\Models\MaintenancePlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaintenancePlanController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenancePlan::withCount('interventions')->latest();

        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }

        $perPage = min(max((int) $request->input('per_page', 15), 1), 100);

        return response()->json($query->paginate($perPage));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $plan = MaintenancePlan::create($validator->validated() + ['active' => true]);

        return response()->json($plan, 201);
    }

    public function show($id)
    {
        return response()->json(
            MaintenancePlan::with('interventions')->findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $plan = MaintenancePlan::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'asset_id' => ['sometimes', 'uuid'],
            'title' => ['sometimes', 'string', 'max:255'],
            'periodicity_days' => ['sometimes', 'integer', 'min:1'],
            'next_due_date' => ['sometimes', 'date'],
            'active' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $plan->update($validator->validated());

        return response()->json($plan->fresh());
    }

    public function destroy($id)
    {
        MaintenancePlan::findOrFail($id)->delete();

        return response()->json(null, 204);
    }

    private function rules(): array
    {
        return [
            'asset_id' => ['required', 'uuid'],
            'title' => ['required', 'string', 'max:255'],
            'periodicity_days' => ['required', 'integer', 'min:1'],
            'next_due_date' => ['required', 'date'],
            'active' => ['nullable', 'boolean'],
        ];
    }
}
