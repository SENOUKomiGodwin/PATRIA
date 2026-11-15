<?php

namespace Tests\Feature;

use App\Models\ReportSnapshot;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        config([
            'services.asset_service.url' => 'http://asset.test',
            'services.movement_service.url' => 'http://movement.test',
            'services.inventory_service.url' => 'http://inventory.test',
            'services.maintenance_service.url' => 'http://maintenance.test',
        ]);
    }

    public function test_unauthenticated_cannot_access_reports(): void
    {
        $this->getJson('/api/reports/dashboard')->assertStatus(401);
        $this->getJson('/api/reports/depreciation')->assertStatus(401);
        $this->getJson('/api/reports/exports/assets')->assertStatus(401);
    }

    public function test_dashboard_aggregates_all_services(): void
    {
        $auditeur = $this->userWithRole('AUDITEUR');
        $token = auth('api')->login($auditeur);

        Http::fake(fn ($request) => $this->fakeUpstream($request));

        $response = $this->withToken($token)->getJson('/api/reports/dashboard');

        $response->assertStatus(200)
            ->assertJsonPath('assets.total', 10)
            ->assertJsonPath('assets.acquisition_value', 100000)
            ->assertJsonPath('assets.net_book_value', 60000)
            ->assertJsonPath('assets.in_maintenance', 2)
            ->assertJsonPath('maintenance_cost', 1234.5)
            ->assertJsonPath('inventory.conformity_rate', 33.3)
            ->assertJsonCount(1, 'latest_movements');

        $this->assertEmpty($response->json('warnings'));
        Http::assertSent(fn ($request) => str_contains($request->url(), 'http://asset.test/api/assets/stats')
            && $request->hasHeader('Authorization', "Bearer {$token}"));
    }

    public function test_dashboard_returns_warnings_when_service_is_down(): void
    {
        $auditeur = $this->userWithRole('AUDITEUR');
        $token = auth('api')->login($auditeur);

        Http::fake(function ($request) {
            if (str_contains($request->url(), 'asset.test')) {
                throw new ConnectionException('timeout');
            }

            return $this->fakeUpstream($request);
        });

        $response = $this->withToken($token)->getJson('/api/reports/dashboard');

        $response->assertStatus(200)
            ->assertJsonPath('assets', null)
            ->assertJsonPath('maintenance_cost', 1234.5);

        $this->assertNotEmpty($response->json('warnings'));
        $this->assertStringContainsString('asset-service', implode(' ', $response->json('warnings')));
    }

    public function test_dashboard_is_cached_for_five_minutes(): void
    {
        $auditeur = $this->userWithRole('AUDITEUR');
        $token = auth('api')->login($auditeur);

        Http::fake(fn ($request) => $this->fakeUpstream($request));

        $this->withToken($token)->getJson('/api/reports/dashboard')->assertStatus(200);
        $sentAfterFirst = Http::recorded()->count();

        $this->withToken($token)->getJson('/api/reports/dashboard')->assertStatus(200);

        $this->assertSame($sentAfterFirst, Http::recorded()->count(), 'Le dashboard mis en cache ne doit pas rappeler les services amont.');
    }

    public function test_depreciation_report_by_category_and_total(): void
    {
        $auditeur = $this->userWithRole('AUDITEUR');
        $token = auth('api')->login($auditeur);

        Http::fake(fn ($request) => str_contains($request->url(), 'asset.test/api/assets')
            ? Http::response([
                'data' => [
                    [
                        'id' => 'a1',
                        'code' => 'PAT-1',
                        'name' => 'Chaise',
                        'category' => ['name' => 'Mobilier', 'amortization_years' => 5],
                        'acquisition_value' => 10000,
                        'acquisition_date' => '2020-01-15',
                    ],
                    [
                        'id' => 'a2',
                        'code' => 'PAT-2',
                        'name' => 'PC portable',
                        'category' => ['name' => 'Informatique', 'amortization_years' => 4],
                        'acquisition_value' => 8000,
                        'acquisition_date' => '2024-06-01',
                    ],
                ],
                'total' => 2,
            ], 200)
            : Http::response(['data' => []], 200));

        $response = $this->withToken($token)->getJson('/api/reports/depreciation?year=2026');

        $response->assertStatus(200)
            ->assertJsonPath('total.dotation', 2000)
            ->assertJsonPath('total.cumulative', 14000)
            ->assertJsonPath('total.vnc', 4000);

        $byCategory = collect($response->json('by_category'))->keyBy('category');
        $this->assertEquals(0, $byCategory['Mobilier']['dotation']);
        $this->assertEquals(10000, $byCategory['Mobilier']['cumulative']);
        $this->assertEquals(0, $byCategory['Mobilier']['vnc']);
        $this->assertEquals(2000, $byCategory['Informatique']['dotation']);
        $this->assertEquals(4000, $byCategory['Informatique']['vnc']);
    }

    public function test_exports_content_type_permissions_and_traceability(): void
    {
        Storage::fake('local');

        $agent = $this->userWithRole('AGENT');
        $agentToken = auth('api')->login($agent);

        $this->withToken($agentToken)->getJson('/api/reports/exports/assets?format=pdf')
            ->assertStatus(403);

        $auditeur = $this->userWithRole('AUDITEUR');
        $auditeurToken = auth('api')->login($auditeur);

        Http::fake(fn ($request) => $this->fakeUpstream($request));

        $pdf = $this->withToken($auditeurToken)->get('/api/reports/exports/assets?format=pdf');
        $pdf->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $pdf->headers->get('content-type'));

        $this->assertDatabaseHas('report_exports', [
            'type' => 'assets',
            'format' => 'pdf',
            'requested_by' => $auditeur->id,
        ]);

        $xlsx = $this->withToken($auditeurToken)->get('/api/reports/exports/movements?format=xlsx');
        $xlsx->assertStatus(200);
        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $xlsx->headers->get('content-type')
        );

        $this->assertDatabaseHas('report_exports', [
            'type' => 'movements',
            'format' => 'xlsx',
            'requested_by' => $auditeur->id,
        ]);

        $this->withToken($auditeurToken)->getJson('/api/reports/exports/inconnu?format=pdf')
            ->assertStatus(422);
        $this->withToken($auditeurToken)->getJson('/api/reports/exports/assets?format=csv')
            ->assertStatus(422);
    }

    public function test_snapshot_command_records_daily_dashboard(): void
    {
        $admin = $this->userWithRole('ADMIN');

        Http::fake(fn ($request) => $this->fakeUpstream($request));

        $this->artisan('reports:snapshot-daily')->assertSuccessful();

        $snapshot = ReportSnapshot::where('type', 'dashboard')->latest()->first();
        $this->assertNotNull($snapshot);
        $this->assertSame(now()->toDateString(), $snapshot->period);
        $this->assertSame(10, $snapshot->data['assets']['total']);
    }

    private function fakeUpstream($request)
    {
        $url = $request->url();

        return match (true) {
            str_contains($url, 'asset.test/api/assets/stats') => Http::response([
                'total' => 10,
                'total_acquisition_value' => 100000,
                'total_net_book_value' => 60000,
                'by_category' => [
                    ['category' => 'Mobilier', 'count' => 4],
                    ['category' => 'Informatique', 'count' => 6],
                ],
                'by_status' => [
                    ['status' => 'EN_SERVICE', 'count' => 8],
                    ['status' => 'EN_MAINTENANCE', 'count' => 2],
                ],
            ], 200),
            str_contains($url, 'asset.test/api/assets') => Http::response(['data' => [], 'total' => 0], 200),
            str_contains($url, 'maintenance.test/api/interventions/stats') => Http::response([
                'by_status' => ['EN_COURS' => 1],
                'total_cost' => 1234.5,
                'average_duration_hours' => 3.5,
                'cost_by_type' => ['CORRECTIVE' => 1234.5],
            ], 200),
            str_contains($url, 'maintenance.test/api/interventions') => Http::response(['data' => [], 'total' => 0], 200),
            str_contains($url, 'movement.test/api/movements') => Http::response([
                'data' => [[
                    'id' => 'm1',
                    'asset_id' => 'a1',
                    'type' => 'AFFECTATION',
                    'movement_date' => '2026-01-10',
                ]],
                'total' => 1,
            ], 200),
            str_contains($url, 'inventory.test/api/campaigns/camp-1') => Http::response([
                'id' => 'camp-1',
                'label' => 'Inventaire 2026',
                'status' => 'CLOTUREE',
                'lines' => [
                    ['result' => 'CONFORME'],
                    ['result' => 'ECART'],
                    ['result' => 'MANQUANT'],
                ],
            ], 200),
            str_contains($url, 'inventory.test/api/campaigns') => Http::response([
                'data' => [['id' => 'camp-1', 'label' => 'Inventaire 2026', 'status' => 'CLOTUREE']],
                'total' => 1,
            ], 200),
            default => Http::response(['data' => []], 200),
        };
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
