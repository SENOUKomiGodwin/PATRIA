<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class AssetServiceClient
{
    public function searchAssets(array $query, string $token): Response
    {
        return $this->request($token)->get('/api/assets', $query);
    }

    public function getAllAssets(string $token): ?array
    {
        $assets = [];
        $page = 1;
        $total = null;

        do {
            $response = $this->request($token)->get('/api/assets', [
                'per_page' => 100,
                'page' => $page,
            ]);

            if (! $response->successful()) {
                return null;
            }

            $payload = $response->json() ?? [];
            $items = $payload['data'] ?? [];
            $assets = array_merge($assets, $items);
            $total = $payload['total'] ?? $payload['meta']['total'] ?? $total ?? count($assets);
            $page++;
        } while (count($assets) < $total && count($items) === 100 && $page <= 50);

        return $assets;
    }

    private function request(string $token): PendingRequest
    {
        return Http::baseUrl($this->baseUrl())
            ->withToken($token)
            ->acceptJson()
            ->asJson()
            ->timeout(10);
    }

    private function baseUrl(): string
    {
        return rtrim((string) config('services.asset_service.url', 'http://localhost:8000'), '/');
    }
}
