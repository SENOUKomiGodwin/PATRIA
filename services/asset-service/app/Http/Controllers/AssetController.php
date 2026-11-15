<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('api')->user();
        $query = Asset::with(['category', 'location']);

        if ($user && in_array($user->role->name, ['GESTIONNAIRE', 'AGENT'])) {
            $query->where('service_id', $user->service_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('service_id') && ($user && in_array($user->role->name, ['ADMIN', 'AUDITEUR']))) {
            $query->where('service_id', $request->service_id);
        }

        $perPage = $request->input('per_page', 15);
        $assets = $query->paginate($perPage);

        return response()->json($assets);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'acquisition_date' => 'required|date',
            'acquisition_value' => 'required|numeric|min:0',
            'status' => 'in:EN_SERVICE,EN_MAINTENANCE,REFORME,CEDE',
            'condition' => 'in:NEUF,BON,MOYEN,MAUVAIS',
            'location_id' => 'nullable|exists:locations,id',
            'service_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $code = $this->generateAssetCode();
        $uuid = \Illuminate\Support\Str::uuid();

        $asset = Asset::create([
            'id' => $uuid,
            'code' => $code,
            'name' => $request->name,
            'category_id' => $request->category_id,
            'acquisition_date' => $request->acquisition_date,
            'acquisition_value' => $request->acquisition_value,
            'status' => $request->status ?? 'EN_SERVICE',
            'condition' => $request->condition ?? 'NEUF',
            'location_id' => $request->location_id,
            'service_id' => $request->service_id,
            'qr_code' => $code,
        ]);

        return response()->json($asset->load(['category', 'location']), 201);
    }

    public function show($id)
    {
        $user = Auth::guard('api')->user();
        $asset = Asset::with(['category', 'location'])->findOrFail($id);

        if ($user && in_array($user->role->name, ['GESTIONNAIRE', 'AGENT'])) {
            if ($asset->service_id !== $user->service_id) {
                return response()->json(['message' => 'Accès non autorisé'], 403);
            }
        }

        return response()->json($asset);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'acquisition_date' => 'sometimes|date',
            'acquisition_value' => 'sometimes|numeric|min:0',
            'status' => 'in:EN_SERVICE,EN_MAINTENANCE,REFORME,CEDE',
            'condition' => 'in:NEUF,BON,MOYEN,MAUVAIS',
            'location_id' => 'nullable|exists:locations,id',
            'service_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $asset = Asset::findOrFail($id);
        $asset->update($request->all());

        return response()->json($asset->load(['category', 'location']));
    }

    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();

        return response()->json(['message' => 'Asset deleted'], 200);
    }

    public function qr($id)
    {
        $asset = Asset::findOrFail($id);

        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($asset->code);

        return response($qrCodeSvg)->header('Content-Type', 'image/svg+xml');
    }

    public function depreciation($id)
    {
        $asset = Asset::with('category')->findOrFail($id);

        return response()->json([
            'asset_id' => $asset->id,
            'asset_code' => $asset->code,
            'asset_name' => $asset->name,
            'acquisition_value' => $asset->acquisition_value,
            'acquisition_date' => $asset->acquisition_date,
            'amortization_years' => $asset->category->amortization_years,
            'schedule' => $asset->depreciationSchedule(),
        ]);
    }

    public function stats()
    {
        $user = Auth::guard('api')->user();
        $query = Asset::query();

        if ($user && in_array($user->role->name, ['GESTIONNAIRE', 'AGENT'])) {
            $query->where('service_id', $user->service_id);
        }

        $total = $query->count();
        $totalAcquisitionValue = (clone $query)->sum('acquisition_value');

        $byCategory = (clone $query)
            ->join('categories', 'assets.category_id', '=', 'categories.id')
            ->selectRaw('categories.name as category, COUNT(*) as count')
            ->groupBy('categories.name')
            ->get();

        $byStatus = (clone $query)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $totalNetBookValue = 0;
        (clone $query)->chunk(100, function ($assets) use (&$totalNetBookValue) {
            foreach ($assets as $asset) {
                $totalNetBookValue += $asset->netBookValue();
            }
        });

        return response()->json([
            'total' => $total,
            'total_acquisition_value' => round($totalAcquisitionValue, 2),
            'total_net_book_value' => round($totalNetBookValue, 2),
            'by_category' => $byCategory,
            'by_status' => $byStatus,
        ]);
    }

    private function generateAssetCode(): string
    {
        $year = now()->year;
        $prefix = "PAT-{$year}-";

        $lastAsset = Asset::where('code', 'like', "{$prefix}%")
            ->orderBy('code', 'desc')
            ->first();

        if ($lastAsset) {
            $lastNumber = (int) substr($lastAsset->code, -5);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf("PAT-%s-%05d", $year, $newNumber);
    }
}
