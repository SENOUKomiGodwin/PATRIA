<?php

namespace App\Http\Controllers;

use App\Exports\TabularExport;
use App\Models\ReportExport;
use App\Services\ReportingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    private const EXPORT_TYPES = ['assets', 'movements', 'inventory', 'maintenance'];

    private const EXPORT_TITLES = [
        'assets' => 'Export des biens',
        'movements' => 'Export des mouvements',
        'inventory' => 'Export des campagnes inventaire',
        'maintenance' => 'Export des interventions maintenance',
    ];

    public function dashboard(Request $request, ReportingService $reporting)
    {
        $user = Auth::guard('api')->user();
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        return response()->json(
            Cache::remember(
                "reports:dashboard:{$user->id}",
                300,
                fn () => $reporting->dashboard($token)
            )
        );
    }

    public function depreciation(Request $request, ReportingService $reporting)
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        $year = (int) $request->input('year', now()->year);

        if ($year < 1900 || $year > 2100) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => ['year' => ['L\'année doit être comprise entre 1900 et 2100.']],
            ], 422);
        }

        return response()->json($reporting->depreciation($token, $year));
    }

    public function export(Request $request, string $type, ReportingService $reporting)
    {
        $type = strtolower($type);
        $format = strtolower((string) $request->input('format', 'pdf'));

        if (! in_array($type, self::EXPORT_TYPES, true) || ! in_array($format, ['pdf', 'xlsx'], true)) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => [
                    'type' => ['Type attendu : '.implode(', ', self::EXPORT_TYPES).'.'],
                    'format' => ['Format attendu : pdf ou xlsx.'],
                ],
            ], 422);
        }

        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        $user = Auth::guard('api')->user();
        $data = $reporting->exportData($type, $token);
        $title = self::EXPORT_TITLES[$type];

        [$binary, $extension, $mime] = $format === 'pdf'
            ? $this->renderPdf($title, $data, $user?->name)
            : $this->renderXlsx($title, $data);

        $relativePath = 'exports/'.$type.'-'.now()->format('Ymd-His').'-'.Str::uuid().'.'.$extension;
        Storage::disk('local')->put($relativePath, $binary);

        ReportExport::create([
            'type' => $type,
            'format' => $format,
            'filters' => $request->query(),
            'file_path' => $relativePath,
            'requested_by' => $user?->id,
            'created_at' => now(),
        ]);

        $filename = $type.'-'.$format.'-'.now()->format('Ymd-His').'.'.$extension;

        return response($binary, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function renderPdf(string $title, array $data, ?string $userName): array
    {
        $pdf = Pdf::loadView('reports.export', [
            'title' => $title,
            'headings' => $data['headings'],
            'rows' => $data['rows'],
            'warnings' => $data['warnings'],
            'generatedAt' => now()->format('d/m/Y H:i'),
            'userName' => $userName ?? 'Système',
        ])->setPaper('a4', 'landscape');

        return [$pdf->output(), 'pdf', 'application/pdf'];
    }

    private function renderXlsx(string $title, array $data): array
    {
        $binary = Excel::raw(
            new TabularExport($title, $data['headings'], $data['rows']),
            \Maatwebsite\Excel\Excel::XLSX
        );

        return [$binary, 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
    }
}
