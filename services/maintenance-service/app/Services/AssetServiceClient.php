<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class AssetServiceClient
{
    public function getAsset(string $assetId, string $token): Response
    {
        return $this->request($token)->get("/api/assets/{$assetId}");
    }

    public function updateStatus(string $assetId, string $status, string $token): Response
    {
        return $this->request($token)->patch("/api/assets/{$assetId}/status", [
            'status' => $status,
        ]);
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
