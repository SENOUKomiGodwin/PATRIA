<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ReportingService
{
    private const TIMEOUT = 5;

    private const MAX_PAGES = 50;

    public function dashboard(string $token): array
    {
        $warnings = [];

        $responses = Http::pool(fn (Pool $pool) => [
            $this->poolRequest($pool, 'asset_service', '/api/assets/stats', [], $token),
            $this->poolRequest($pool, 'maintenance_service', '/api/interventions/stats', [], $token),
            $this->poolRequest($pool, 'movement_service', '/api/movements', ['per_page' => 5], $token),
            $this->poolRequest($pool, 'inventory_service', '/api/campaigns', ['per_page' => 1], $token),
        ]);

        [$assetsResponse, $maintenanceResponse, $movementsResponse, $campaignsResponse] = array_values($responses);

        $assets = $this->unwrap($assetsResponse, 'asset-service', $warnings);
        $maintenance = $this->unwrap($maintenanceResponse, 'maintenance-service', $warnings);
        $movements = $this->unwrap($movementsResponse, 'movement-service', $warnings);
        $campaigns = $this->unwrap($campaignsResponse, 'inventory-service', $warnings);

        $byStatus = collect($assets['by_status'] ?? [])
            ->mapWithKeys(fn ($row) => [($row['status'] ?? null) => (int) ($row['count'] ?? 0)]);

        $inventory = $this->latestCampaign($campaigns, $token, $warnings);

        return [
            'generated_at' => now()->toIso8601String(),
            'assets' => $assets === null ? null : [
                'total' => (int) ($assets['total'] ?? 0),
                'acquisition_value' => (float) ($assets['total_acquisition_value'] ?? 0),
                'net_book_value' => (float) ($assets['total_net_book_value'] ?? 0),
                'by_category' => $assets['by_category'] ?? [],
                'by_status' => $assets['by_status'] ?? [],
                'in_maintenance' => (int) ($byStatus['EN_MAINTENANCE'] ?? 0),
            ],
            'maintenance_cost' => $maintenance === null ? null : (float) ($maintenance['total_cost'] ?? 0),
            'latest_movements' => $movements['data'] ?? [],
            'inventory' => $inventory,
            'warnings' => $warnings,
        ];
    }

    public function depreciation(string $token, int $year): array
    {
        $warnings = [];
        $assets = $this->getAll('asset_service', '/api/assets', [], $token);

        if ($assets === null) {
            return [
                'year' => $year,
                'by_category' => [],
                'total' => ['dotation' => 0, 'cumulative' => 0, 'vnc' => 0],
                'warnings' => ['asset-service : liste des biens indisponible.'],
            ];
        }

        $rows = [];

        foreach ($assets as $asset) {
            $category = $asset['category']['name'] ?? 'Sans catégorie';
            $amortizationYears = (int) ($asset['category']['amortization_years'] ?? 5);
            $amortizationYears = $amortizationYears > 0 ? $amortizationYears : 5;
            $value = (float) ($asset['acquisition_value'] ?? 0);
            $acquisitionYear = (int) date('Y', strtotime((string) ($asset['acquisition_date'] ?? 'now')));
            $elapsed = $year - $acquisitionYear;

            $annual = $value / $amortizationYears;
            $dotation = ($elapsed >= 1 && $elapsed <= $amortizationYears) ? $annual : 0;
            $cumulativeYears = max(0, min($elapsed, $amortizationYears));
            $cumulative = min($value, $annual * $cumulativeYears);
            $vnc = max(0, $value - $cumulative);

            $rows[$category]['dotation'] = ($rows[$category]['dotation'] ?? 0) + $dotation;
            $rows[$category]['cumulative'] = ($rows[$category]['cumulative'] ?? 0) + $cumulative;
            $rows[$category]['vnc'] = ($rows[$category]['vnc'] ?? 0) + $vnc;
        }

        $byCategory = collect($rows)
            ->map(fn ($values, $category) => [
                'category' => $category,
                'dotation' => round($values['dotation'], 2),
                'cumulative' => round($values['cumulative'], 2),
                'vnc' => round($values['vnc'], 2),
            ])
            ->values()
            ->all();

        return [
            'year' => $year,
            'by_category' => $byCategory,
            'total' => [
                'dotation' => round(array_sum(array_column($byCategory, 'dotation')), 2),
                'cumulative' => round(array_sum(array_column($byCategory, 'cumulative')), 2),
                'vnc' => round(array_sum(array_column($byCategory, 'vnc')), 2),
            ],
            'warnings' => $warnings,
        ];
    }

    /**
     * @return array{headings: array<int, string>, rows: array<int, array<int, mixed>>, warnings: array<int, string>}
     */
    public function exportData(string $type, string $token): array
    {
        return match ($type) {
            'assets' => $this->exportAssets($token),
            'movements' => $this->exportMovements($token),
            'inventory' => $this->exportInventory($token),
            'maintenance' => $this->exportMaintenance($token),
            default => ['headings' => [], 'rows' => [], 'warnings' => ["Type d'export inconnu : {$type}."]],
        };
    }

    private function exportAssets(string $token): array
    {
        $warnings = [];
        $assets = $this->getAll('asset_service', '/api/assets', [], $token);

        if ($assets === null) {
            return $this->emptyExport(['asset-service : liste des biens indisponible.']);
        }

        return [
            'headings' => ['Code', 'Nom', 'Catégorie', 'Statut', 'Valeur acquisition', 'Date acquisition'],
            'rows' => array_map(fn ($asset) => [
                $asset['code'] ?? '',
                $asset['name'] ?? '',
                $asset['category']['name'] ?? '',
                $asset['status'] ?? '',
                $asset['acquisition_value'] ?? '',
                $asset['acquisition_date'] ?? '',
            ], $assets),
            'warnings' => $warnings,
        ];
    }

    private function exportMovements(string $token): array
    {
        $warnings = [];
        $movements = $this->getAll('movement_service', '/api/movements', [], $token);

        if ($movements === null) {
            return $this->emptyExport(['movement-service : liste des mouvements indisponible.']);
        }

        return [
            'headings' => ['Date', 'Type', 'Actif', 'De', 'Vers', 'Motif'],
            'rows' => array_map(fn ($movement) => [
                $movement['movement_date'] ?? '',
                $movement['type'] ?? '',
                $movement['asset_id'] ?? '',
                $movement['from_service_id'] ?? '',
                $movement['to_service_id'] ?? $movement['to_location_id'] ?? '',
                $movement['reason'] ?? '',
            ], $movements),
            'warnings' => $warnings,
        ];
    }

    private function exportInventory(string $token): array
    {
        $warnings = [];
        $campaigns = $this->getAll('inventory_service', '/api/campaigns', [], $token);

        if ($campaigns === null) {
            return $this->emptyExport(['inventory-service : liste des campagnes indisponible.']);
        }

        return [
            'headings' => ['Libellé', 'Début', 'Fin', 'Statut'],
            'rows' => array_map(fn ($campaign) => [
                $campaign['label'] ?? '',
                $campaign['start_date'] ?? '',
                $campaign['end_date'] ?? '',
                $campaign['status'] ?? '',
            ], $campaigns),
            'warnings' => $warnings,
        ];
    }

    private function exportMaintenance(string $token): array
    {
        $warnings = [];
        $interventions = $this->getAll('maintenance_service', '/api/interventions', [], $token);

        if ($interventions === null) {
            return $this->emptyExport(['maintenance-service : liste des interventions indisponible.']);
        }

        return [
            'headings' => ['Titre', 'Type', 'Statut', 'Actif', 'Planifiée le', 'Coût', 'Prestataire'],
            'rows' => array_map(fn ($intervention) => [
                $intervention['title'] ?? '',
                $intervention['type'] ?? '',
                $intervention['status'] ?? '',
                $intervention['asset_id'] ?? '',
                $intervention['scheduled_date'] ?? '',
                $intervention['cost'] ?? '',
                $intervention['provider'] ?? '',
            ], $interventions),
            'warnings' => $warnings,
        ];
    }

    private function latestCampaign(?array $campaigns, string $token, array &$warnings): array
    {
        $campaign = $campaigns['data'][0] ?? null;

        if (! $campaign) {
            return ['campaign' => null, 'conformity_rate' => null];
        }

        $detail = $this->get('inventory_service', "/api/campaigns/{$campaign['id']}", [], $token);

        if (! $detail instanceof Response || ! $detail->successful()) {
            $warnings[] = 'inventory-service : détail de la dernière campagne indisponible.';

            return [
                'campaign' => [
                    'id' => $campaign['id'] ?? null,
                    'label' => $campaign['label'] ?? null,
                    'status' => $campaign['status'] ?? null,
                ],
                'conformity_rate' => null,
            ];
        }

        $lines = $detail->json('lines') ?? [];
        $total = count($lines);
        $conformes = collect($lines)->where('result', 'CONFORME')->count();

        return [
            'campaign' => [
                'id' => $campaign['id'] ?? null,
                'label' => $campaign['label'] ?? null,
                'status' => $campaign['status'] ?? null,
            ],
            'conformity_rate' => $total > 0 ? round($conformes / $total * 100, 1) : null,
            'total_lines' => $total,
        ];
    }

    private function emptyExport(array $warnings): array
    {
        return ['headings' => [], 'rows' => [], 'warnings' => $warnings];
    }

    private function unwrap(mixed $response, string $serviceName, array &$warnings): ?array
    {
        if (! $response instanceof Response || ! $response->successful()) {
            $warnings[] = "Le service {$serviceName} est indisponible.";

            return null;
        }

        return $response->json() ?? [];
    }

    private function poolRequest(Pool $pool, string $service, string $path, array $query, string $token)
    {
        return $pool->withToken($token)
            ->baseUrl($this->baseUrl($service))
            ->acceptJson()
            ->timeout(self::TIMEOUT)
            ->retry(1, 200)
            ->get($path, $query);
    }

    private function get(string $service, string $path, array $query, string $token): Response|ConnectionException
    {
        try {
            return Http::baseUrl($this->baseUrl($service))
                ->withToken($token)
                ->acceptJson()
                ->timeout(self::TIMEOUT)
                ->retry(1, 200)
                ->get($path, $query);
        } catch (ConnectionException $exception) {
            return $exception;
        }
    }

    private function getAll(string $service, string $path, array $query, string $token): ?array
    {
        $items = [];
        $page = 1;

        do {
            $response = $this->get($service, $path, $query + [
                'per_page' => 100,
                'page' => $page,
            ], $token);

            if (! $response instanceof Response || ! $response->successful()) {
                return null;
            }

            $payload = $response->json() ?? [];
            $data = $payload['data'] ?? [];
            $items = array_merge($items, $data);
            $total = $payload['total'] ?? count($items);
            $page++;
        } while (count($items) < $total && count($data) === 100 && $page <= self::MAX_PAGES);

        return $items;
    }

    private function baseUrl(string $service): string
    {
        return rtrim((string) config("services.{$service}.url", 'http://localhost:8000'), '/');
    }
}
