<?php

namespace Tests\Feature;

use App\Models\Movement;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MovementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        config(['services.asset_service.url' => 'http://asset-service.test']);
    }

    public function test_unauthenticated_cannot_access_movements(): void
    {
        $this->getJson('/api/movements')->assertStatus(401);
        $this->postJson('/api/movements', [])->assertStatus(401);
    }

    public function test_agent_cannot_create_movement(): void
    {
        $user = $this->userWithRole('AGENT');
        $token = auth('api')->login($user);

        $this->withToken($token)->postJson('/api/movements', [
            'asset_id' => '11111111-1111-1111-1111-111111111111',
            'type' => 'AFFECTATION',
        ])->assertStatus(403);
    }

    public function test_admin_can_create_affectation_movement(): void
    {
        $user = $this->userWithRole('ADMIN');
        $token = auth('api')->login($user);
        $assetId = '11111111-1111-1111-1111-111111111111';

        Http::fake(function ($request) use ($assetId) {
            if ($request->method() === 'GET' && str_ends_with($request->url(), "/api/assets/{$assetId}")) {
                return Http::response([
                    'id' => $assetId,
                    'status' => 'EN_SERVICE',
                    'service_id' => 'SRV-001',
                ], 200);
            }

            if ($request->method() === 'PATCH' && str_ends_with($request->url(), "/api/assets/{$assetId}/assignment")) {
                return Http::response(['id' => $assetId, 'service_id' => 'SRV-002'], 200);
            }

            return Http::response([], 404);
        });

        $response = $this->withToken($token)->postJson('/api/movements', [
            'asset_id' => $assetId,
            'type' => 'AFFECTATION',
            'to_service_id' => 'SRV-002',
            'to_location_id' => 5,
            'reason' => 'Affectation service informatique',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('type', 'AFFECTATION')
            ->assertJsonPath('from_service_id', 'SRV-001')
            ->assertJsonPath('to_service_id', 'SRV-002');

        $this->assertDatabaseHas('movements', [
            'asset_id' => $assetId,
            'type' => 'AFFECTATION',
            'from_service_id' => 'SRV-001',
            'to_service_id' => 'SRV-002',
            'to_location_id' => 5,
            'user_id' => $user->id,
        ]);

        Http::assertSent(fn ($request) => $request->method() === 'GET'
            && str_ends_with($request->url(), "/api/assets/{$assetId}")
            && $request->hasHeader('Authorization', "Bearer {$token}"));
    }

    public function test_transfer_requires_different_service(): void
    {
        $user = $this->userWithRole('GESTIONNAIRE');
        $token = auth('api')->login($user);
        $assetId = '22222222-2222-2222-2222-222222222222';

        Http::fake(fn () => Http::response([
            'id' => $assetId,
            'status' => 'EN_SERVICE',
            'service_id' => 'SRV-001',
        ], 200));

        $this->withToken($token)->postJson('/api/movements', [
            'asset_id' => $assetId,
            'type' => 'TRANSFERT',
            'to_service_id' => 'SRV-001',
        ])->assertStatus(422);

        $this->assertDatabaseMissing('movements', ['asset_id' => $assetId]);
        Http::assertSentCount(1);
    }

    public function test_cession_updates_asset_status_and_creates_movement(): void
    {
        $user = $this->userWithRole('ADMIN');
        $token = auth('api')->login($user);
        $assetId = '33333333-3333-3333-3333-333333333333';

        Http::fake(function ($request) use ($assetId) {
            if ($request->method() === 'GET') {
                return Http::response([
                    'id' => $assetId,
                    'status' => 'EN_SERVICE',
                    'service_id' => 'SRV-001',
                ], 200);
            }

            if ($request->method() === 'PATCH' && str_ends_with($request->url(), "/api/assets/{$assetId}/status")) {
                return Http::response(['id' => $assetId, 'status' => 'CEDE'], 200);
            }

            return Http::response([], 404);
        });

        $this->withToken($token)->postJson('/api/movements', [
            'asset_id' => $assetId,
            'type' => 'CESSION',
            'reason' => 'Cession definitive',
        ])->assertStatus(201)->assertJsonPath('type', 'CESSION');

        $this->assertDatabaseHas('movements', [
            'asset_id' => $assetId,
            'type' => 'CESSION',
        ]);

        Http::assertSent(fn ($request) => $request->method() === 'PATCH'
            && str_ends_with($request->url(), "/api/assets/{$assetId}/status")
            && $request['status'] === 'CEDE');
    }

    public function test_reformed_asset_cannot_be_moved(): void
    {
        $user = $this->userWithRole('ADMIN');
        $token = auth('api')->login($user);
        $assetId = '44444444-4444-4444-4444-444444444444';

        Http::fake(fn () => Http::response([
            'id' => $assetId,
            'status' => 'REFORME',
            'service_id' => 'SRV-001',
        ], 200));

        $this->withToken($token)->postJson('/api/movements', [
            'asset_id' => $assetId,
            'type' => 'AFFECTATION',
            'to_service_id' => 'SRV-002',
        ])->assertStatus(422);

        $this->assertDatabaseMissing('movements', ['asset_id' => $assetId]);
        Http::assertSentCount(1);
    }

    public function test_ceded_asset_cannot_be_moved(): void
    {
        $user = $this->userWithRole('ADMIN');
        $token = auth('api')->login($user);
        $assetId = '55555555-5555-5555-5555-555555555555';

        Http::fake(fn () => Http::response([
            'id' => $assetId,
            'status' => 'CEDE',
            'service_id' => 'SRV-001',
        ], 200));

        $this->withToken($token)->postJson('/api/movements', [
            'asset_id' => $assetId,
            'type' => 'TRANSFERT',
            'to_service_id' => 'SRV-002',
        ])->assertStatus(422);

        $this->assertDatabaseMissing('movements', ['asset_id' => $assetId]);
        Http::assertSentCount(1);
    }

    public function test_unknown_asset_returns_not_found(): void
    {
        $user = $this->userWithRole('ADMIN');
        $token = auth('api')->login($user);
        $assetId = '66666666-6666-6666-6666-666666666666';

        Http::fake(fn () => Http::response(['message' => 'Not found'], 404));

        $this->withToken($token)->postJson('/api/movements', [
            'asset_id' => $assetId,
            'type' => 'AFFECTATION',
        ])->assertStatus(404);

        $this->assertDatabaseMissing('movements', ['asset_id' => $assetId]);
    }

    public function test_asset_service_failure_returns_bad_gateway(): void
    {
        $user = $this->userWithRole('ADMIN');
        $token = auth('api')->login($user);
        $assetId = '77777777-7777-7777-7777-777777777777';

        Http::fake(function () {
            throw new ConnectionException('timeout');
        });

        $this->withToken($token)->postJson('/api/movements', [
            'asset_id' => $assetId,
            'type' => 'AFFECTATION',
        ])->assertStatus(502);

        $this->assertDatabaseMissing('movements', ['asset_id' => $assetId]);
    }

    public function test_movements_can_be_filtered_and_paginated(): void
    {
        $user = $this->userWithRole('ADMIN');
        $token = auth('api')->login($user);
        $assetId = '88888888-8888-8888-8888-888888888888';

        Movement::create([
            'asset_id' => $assetId,
            'type' => 'AFFECTATION',
            'from_service_id' => 'SRV-001',
            'to_service_id' => 'SRV-002',
            'movement_date' => now()->toDateString(),
            'user_id' => $user->id,
        ]);
        Movement::create([
            'asset_id' => $assetId,
            'type' => 'CESSION',
            'from_service_id' => 'SRV-002',
            'movement_date' => now()->toDateString(),
            'user_id' => $user->id,
        ]);

        $response = $this->withToken($token)->getJson("/api/movements?asset_id={$assetId}&type=AFFECTATION&per_page=1");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.type', 'AFFECTATION');
    }

    private function userWithRole(string $roleName): User
    {
        return User::create([
            'name' => ucfirst(strtolower($roleName)),
            'email' => strtolower($roleName).'@example.com',
            'password' => bcrypt('password'),
            'role_id' => Role::where('name', $roleName)->value('id'),
            'service_id' => 'SRV-001',
            'active' => true,
        ]);
    }
}
