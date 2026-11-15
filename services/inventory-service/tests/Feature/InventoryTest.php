<?php

namespace Tests\Feature;

use App\Models\InventoryCampaign;
use App\Models\InventoryLine;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        config(['services.asset_service.url' => 'http://asset-service.test']);
    }

    public function test_unauthenticated_cannot_access_campaigns(): void
    {
        $this->getJson('/api/campaigns')->assertStatus(401);
        $this->postJson('/api/campaigns', [])->assertStatus(401);
    }

    public function test_agent_cannot_create_campaign_but_gestionnaire_can(): void
    {
        $agent = $this->userWithRole('AGENT');
        $agentToken = auth('api')->login($agent);

        $this->withToken($agentToken)->postJson('/api/campaigns', [
            'label' => 'Inventaire annuel',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addWeek()->toDateString(),
        ])->assertStatus(403);

        $gestionnaire = $this->userWithRole('GESTIONNAIRE');
        $gestionnaireToken = auth('api')->login($gestionnaire);

        $response = $this->withToken($gestionnaireToken)->postJson('/api/campaigns', [
            'label' => 'Inventaire annuel',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addWeek()->toDateString(),
        ]);

        $response->assertStatus(201)->assertJsonPath('status', 'OUVERTE');
        $this->assertDatabaseHas('inventory_campaigns', ['label' => 'Inventaire annuel', 'status' => 'OUVERTE']);
    }

    public function test_agent_can_add_conforme_line(): void
    {
        $agent = $this->userWithRole('AGENT');
        $token = auth('api')->login($agent);
        $campaign = $this->campaign();
        $assetId = '11111111-1111-1111-1111-111111111111';

        Http::fake(function ($request) use ($assetId) {
            return Http::response([
                'data' => [[
                    'id' => $assetId,
                    'code' => 'PAT-2026-00001',
                    'qr_code' => 'PAT-2026-00001',
                    'location_id' => 5,
                ]],
            ], 200);
        });

        $response = $this->withToken($token)->postJson("/api/campaigns/{$campaign->id}/lines", [
            'asset_code' => 'PAT-2026-00001',
            'found_location_id' => 5,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('result', 'CONFORME')
            ->assertJsonPath('expected_location_id', 5)
            ->assertJsonPath('found_location_id', 5);

        $this->assertDatabaseHas('inventory_lines', [
            'campaign_id' => $campaign->id,
            'asset_id' => $assetId,
            'result' => 'CONFORME',
            'counted_by' => $agent->id,
        ]);

        Http::assertSent(fn ($request) => $request->method() === 'GET'
            && str_contains($request->url(), '/api/assets')
            && str_contains($request->url(), 'search=PAT-2026-00001')
            && $request->hasHeader('Authorization', "Bearer {$token}"));
    }

    public function test_line_is_ecart_when_location_differs(): void
    {
        $agent = $this->userWithRole('AGENT');
        $token = auth('api')->login($agent);
        $campaign = $this->campaign();
        $assetId = '22222222-2222-2222-2222-222222222222';

        Http::fake(fn () => Http::response([
            'data' => [[
                'id' => $assetId,
                'code' => 'PAT-2026-00002',
                'qr_code' => 'PAT-2026-00002',
                'location_id' => 5,
            ]],
        ], 200));

        $this->withToken($token)->postJson("/api/campaigns/{$campaign->id}/lines", [
            'asset_code' => 'PAT-2026-00002',
            'found_location_id' => 9,
        ])->assertStatus(201)->assertJsonPath('result', 'ECART');

        $this->assertDatabaseHas('inventory_lines', [
            'campaign_id' => $campaign->id,
            'asset_id' => $assetId,
            'result' => 'ECART',
            'expected_location_id' => 5,
            'found_location_id' => 9,
        ]);
    }

    public function test_unknown_asset_code_returns_not_found(): void
    {
        $agent = $this->userWithRole('AGENT');
        $token = auth('api')->login($agent);
        $campaign = $this->campaign();

        Http::fake(fn () => Http::response(['data' => []], 200));

        $this->withToken($token)->postJson("/api/campaigns/{$campaign->id}/lines", [
            'asset_code' => 'PAT-INCONNU',
            'found_location_id' => 5,
        ])->assertStatus(404);

        $this->assertDatabaseMissing('inventory_lines', ['campaign_id' => $campaign->id]);
    }

    public function test_cannot_add_line_to_closed_campaign(): void
    {
        $agent = $this->userWithRole('AGENT');
        $token = auth('api')->login($agent);
        $campaign = $this->campaign(['status' => 'CLOTUREE']);

        $this->withToken($token)->postJson("/api/campaigns/{$campaign->id}/lines", [
            'asset_code' => 'PAT-2026-00001',
            'found_location_id' => 5,
        ])->assertStatus(422);

        Http::assertNothingSent();
    }

    public function test_close_marks_missing_assets_and_discrepancies_are_readable_by_auditeur(): void
    {
        $gestionnaire = $this->userWithRole('GESTIONNAIRE');
        $gestionnaireToken = auth('api')->login($gestionnaire);
        $auditeur = $this->userWithRole('AUDITEUR');
        $campaign = $this->campaign();

        $assetA = 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa';
        $assetB = 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb';
        $assetC = 'cccccccc-cccc-cccc-cccc-cccccccccccc';
        $assetD = 'dddddddd-dddd-dddd-dddd-dddddddddddd';

        InventoryLine::create([
            'campaign_id' => $campaign->id,
            'asset_id' => $assetA,
            'expected_location_id' => 1,
            'found_location_id' => 1,
            'result' => 'CONFORME',
            'counted_by' => $gestionnaire->id,
        ]);
        InventoryLine::create([
            'campaign_id' => $campaign->id,
            'asset_id' => $assetD,
            'expected_location_id' => 4,
            'found_location_id' => 8,
            'result' => 'ECART',
            'counted_by' => $gestionnaire->id,
        ]);

        Http::fake(function ($request) use ($assetA, $assetB, $assetC) {
            if ($request->method() === 'GET' && str_contains($request->url(), '/api/assets') && ! str_contains($request->url(), 'search=')) {
                return Http::response([
                    'data' => [
                        ['id' => $assetA, 'code' => 'PAT-A', 'location_id' => 1],
                        ['id' => $assetB, 'code' => 'PAT-B', 'location_id' => 2],
                        ['id' => $assetC, 'code' => 'PAT-C', 'location_id' => 3],
                    ],
                    'total' => 3,
                ], 200);
            }

            return Http::response(['data' => []], 200);
        });

        $response = $this->withToken($gestionnaireToken)->postJson("/api/campaigns/{$campaign->id}/close");

        $response->assertStatus(200)->assertJsonPath('status', 'CLOTUREE');

        $this->assertDatabaseHas('inventory_lines', [
            'campaign_id' => $campaign->id,
            'asset_id' => $assetB,
            'result' => 'MANQUANT',
            'expected_location_id' => 2,
        ]);
        $this->assertDatabaseHas('inventory_lines', [
            'campaign_id' => $campaign->id,
            'asset_id' => $assetC,
            'result' => 'MANQUANT',
            'expected_location_id' => 3,
        ]);
        $this->assertDatabaseMissing('inventory_lines', [
            'campaign_id' => $campaign->id,
            'asset_id' => $assetA,
            'result' => 'MANQUANT',
        ]);

        $auditeurToken = auth('api')->login($auditeur);
        $discrepancies = $this->withToken($auditeurToken)->getJson("/api/campaigns/{$campaign->id}/discrepancies");

        $discrepancies->assertStatus(200);
        $this->assertCount(3, $discrepancies->json());
        $this->assertEqualsCanonicalizing(
            ['ECART', 'MANQUANT', 'MANQUANT'],
            collect($discrepancies->json())->pluck('result')->all()
        );
    }

    public function test_agent_cannot_close_campaign(): void
    {
        $agent = $this->userWithRole('AGENT');
        $token = auth('api')->login($agent);
        $campaign = $this->campaign();

        $this->withToken($token)->postJson("/api/campaigns/{$campaign->id}/close")->assertStatus(403);
        Http::assertNothingSent();
    }

    public function test_asset_service_failure_returns_bad_gateway(): void
    {
        $agent = $this->userWithRole('AGENT');
        $token = auth('api')->login($agent);
        $campaign = $this->campaign();

        Http::fake(function () {
            throw new ConnectionException('timeout');
        });

        $this->withToken($token)->postJson("/api/campaigns/{$campaign->id}/lines", [
            'asset_code' => 'PAT-2026-00001',
            'found_location_id' => 5,
        ])->assertStatus(502);

        $this->assertDatabaseMissing('inventory_lines', ['campaign_id' => $campaign->id]);
    }

    private function campaign(array $attributes = []): InventoryCampaign
    {
        return InventoryCampaign::create($attributes + [
            'label' => 'Inventaire test',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addWeek()->toDateString(),
            'status' => 'OUVERTE',
        ]);
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
