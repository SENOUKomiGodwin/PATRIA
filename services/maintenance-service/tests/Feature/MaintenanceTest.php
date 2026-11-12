<?php

namespace Tests\Feature;

use App\Models\MaintenanceIntervention;
use App\Models\MaintenancePlan;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MaintenanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        config(['services.asset_service.url' => 'http://asset-service.test']);
    }

    public function test_unauthenticated_cannot_access_interventions(): void
    {
        $this->getJson('/api/interventions')->assertStatus(401);
        $this->postJson('/api/interventions', [])->assertStatus(401);
    }

    public function test_agent_can_declare_corrective_intervention(): void
    {
        $agent = $this->userWithRole('AGENT');
        $token = auth('api')->login($agent);
        $assetId = '11111111-1111-1111-1111-111111111111';

        Http::fake(function ($request) use ($assetId) {
            if ($request->method() === 'GET' && str_contains($request->url(), "/api/assets/{$assetId}")) {
                return Http::response(['id' => $assetId, 'status' => 'EN_SERVICE'], 200);
            }

            if ($request->method() === 'PATCH' && str_contains($request->url(), "/api/assets/{$assetId}/status")) {
                return Http::response(['id' => $assetId, 'status' => 'EN_MAINTENANCE'], 200);
            }

            return Http::response(['message' => 'not found'], 404);
        });

        $response = $this->withToken($token)->postJson('/api/interventions', [
            'asset_id' => $assetId,
            'title' => 'Panne moteur',
            'description' => 'Le bien ne démarre plus',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('type', 'CORRECTIVE')
            ->assertJsonPath('status', 'EN_COURS')
            ->assertJsonPath('reported_by', $agent->id);

        $this->assertDatabaseHas('maintenance_interventions', [
            'asset_id' => $assetId,
            'type' => 'CORRECTIVE',
            'status' => 'EN_COURS',
        ]);

        Http::assertSent(fn ($request) => $request->method() === 'PATCH'
            && str_contains($request->url(), "/api/assets/{$assetId}/status")
            && $request['status'] === 'EN_MAINTENANCE'
            && $request->hasHeader('Authorization', "Bearer {$token}"));
    }

    public function test_reforme_or_cede_asset_cannot_be_intervened(): void
    {
        $agent = $this->userWithRole('AGENT');
        $token = auth('api')->login($agent);
        $assetId = '22222222-2222-2222-2222-222222222222';

        Http::fake(fn () => Http::response(['id' => $assetId, 'status' => 'REFORME'], 200));

        $this->withToken($token)->postJson('/api/interventions', [
            'asset_id' => $assetId,
            'title' => 'Panne',
        ])->assertStatus(422);

        $this->assertDatabaseMissing('maintenance_interventions', ['asset_id' => $assetId]);
        Http::assertNotSent(fn ($request) => $request->method() === 'PATCH');
    }

    public function test_unknown_asset_returns_not_found(): void
    {
        $agent = $this->userWithRole('AGENT');
        $token = auth('api')->login($agent);

        Http::fake(fn () => Http::response(['message' => 'not found'], 404));

        $this->withToken($token)->postJson('/api/interventions', [
            'asset_id' => '33333333-3333-3333-3333-333333333333',
            'title' => 'Panne',
        ])->assertStatus(404);

        $this->assertDatabaseCount('maintenance_interventions', 0);
    }

    public function test_asset_service_failure_returns_bad_gateway_and_records_nothing(): void
    {
        $agent = $this->userWithRole('AGENT');
        $token = auth('api')->login($agent);
        $assetId = '44444444-4444-4444-4444-444444444444';

        Http::fake(function ($request) use ($assetId) {
            if ($request->method() === 'GET') {
                return Http::response(['id' => $assetId, 'status' => 'EN_SERVICE'], 200);
            }

            throw new ConnectionException('timeout');
        });

        $this->withToken($token)->postJson('/api/interventions', [
            'asset_id' => $assetId,
            'title' => 'Panne',
        ])->assertStatus(502);

        $this->assertDatabaseCount('maintenance_interventions', 0);
    }

    public function test_start_close_and_cancel_transitions(): void
    {
        $gestionnaire = $this->userWithRole('GESTIONNAIRE');
        $token = auth('api')->login($gestionnaire);
        $assetId = '55555555-5555-5555-5555-555555555555';
        $plan = MaintenancePlan::create([
            'asset_id' => $assetId,
            'title' => 'Maintenance annuelle',
            'periodicity_days' => 30,
            'next_due_date' => now()->addDays(10)->toDateString(),
            'active' => true,
        ]);
        $dueDate = $plan->next_due_date->toDateString();

        $intervention = MaintenanceIntervention::create([
            'asset_id' => $assetId,
            'plan_id' => $plan->id,
            'type' => 'PREVENTIVE',
            'title' => 'Maintenance annuelle',
            'status' => 'PLANIFIEE',
            'scheduled_date' => now()->toDateString(),
        ]);

        Http::fake(function ($request) use ($assetId) {
            if ($request->method() === 'PATCH' && str_contains($request->url(), "/api/assets/{$assetId}/status")) {
                return Http::response(['id' => $assetId, 'status' => $request['status']], 200);
            }

            return Http::response(['message' => 'not found'], 404);
        });

        $this->withToken($token)->patchJson("/api/interventions/{$intervention->id}/start")
            ->assertStatus(200)
            ->assertJsonPath('status', 'EN_COURS');

        Http::assertSent(fn ($request) => $request->method() === 'PATCH'
            && $request['status'] === 'EN_MAINTENANCE');

        $this->withToken($token)->patchJson("/api/interventions/{$intervention->id}/close", [
            'cost' => 150.50,
            'provider' => 'TechServices',
            'report' => 'Remplacement des filtres',
        ])->assertStatus(200)
            ->assertJsonPath('status', 'TERMINEE');

        $this->assertDatabaseHas('maintenance_interventions', [
            'id' => $intervention->id,
            'status' => 'TERMINEE',
            'provider' => 'TechServices',
        ]);

        $plan->refresh();
        $this->assertEquals(
            now()->parse($dueDate)->addDays(30)->toDateString(),
            $plan->next_due_date->toDateString()
        );

        Http::assertSent(fn ($request) => $request->method() === 'PATCH'
            && $request['status'] === 'EN_SERVICE');

        $second = MaintenanceIntervention::create([
            'asset_id' => $assetId,
            'type' => 'CORRECTIVE',
            'title' => 'Nouvelle panne',
            'status' => 'EN_COURS',
            'scheduled_date' => now()->toDateString(),
            'started_at' => now(),
        ]);

        $this->withToken($token)->patchJson("/api/interventions/{$second->id}/cancel")
            ->assertStatus(200)
            ->assertJsonPath('status', 'ANNULEE');

        $this->assertDatabaseHas('maintenance_interventions', [
            'id' => $second->id,
            'status' => 'ANNULEE',
        ]);
    }

    public function test_agent_cannot_start_close_or_cancel(): void
    {
        $agent = $this->userWithRole('AGENT');
        $token = auth('api')->login($agent);
        $intervention = MaintenanceIntervention::create([
            'asset_id' => '66666666-6666-6666-6666-666666666666',
            'type' => 'CORRECTIVE',
            'title' => 'Panne',
            'status' => 'PLANIFIEE',
            'scheduled_date' => now()->toDateString(),
        ]);

        $this->withToken($token)->patchJson("/api/interventions/{$intervention->id}/start")->assertStatus(403);
        $this->withToken($token)->patchJson("/api/interventions/{$intervention->id}/close", [])->assertStatus(403);
        $this->withToken($token)->patchJson("/api/interventions/{$intervention->id}/cancel")->assertStatus(403);
    }

    public function test_interventions_index_filters_and_stats(): void
    {
        $gestionnaire = $this->userWithRole('GESTIONNAIRE');
        $token = auth('api')->login($gestionnaire);
        $assetId = '77777777-7777-7777-7777-777777777777';

        MaintenanceIntervention::create([
            'asset_id' => $assetId,
            'type' => 'CORRECTIVE',
            'title' => 'Panne A',
            'status' => 'TERMINEE',
            'scheduled_date' => '2026-01-10',
            'started_at' => '2026-01-10 08:00:00',
            'ended_at' => '2026-01-10 10:00:00',
            'cost' => 200,
        ]);
        MaintenanceIntervention::create([
            'asset_id' => $assetId,
            'type' => 'PREVENTIVE',
            'title' => 'Visite B',
            'status' => 'EN_COURS',
            'scheduled_date' => '2026-01-15',
            'started_at' => '2026-01-15 09:00:00',
        ]);

        $this->withToken($token)->getJson('/api/interventions?asset_id='.$assetId.'&status=EN_COURS')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        $stats = $this->withToken($token)->getJson('/api/interventions/stats')->assertStatus(200);

        $this->assertEquals(1, $stats->json('by_status.EN_COURS'));
        $this->assertEquals(1, $stats->json('by_status.TERMINEE'));
        $this->assertEquals(200.0, (float) $stats->json('total_cost'));
        $this->assertEquals(2.0, (float) $stats->json('average_duration_hours'));
        $this->assertEquals(200.0, (float) $stats->json('cost_by_type.CORRECTIVE'));
    }

    public function test_maintenance_plans_crud_and_permissions(): void
    {
        $agent = $this->userWithRole('AGENT');
        $agentToken = auth('api')->login($agent);
        $assetId = '88888888-8888-8888-8888-888888888888';

        $this->withToken($agentToken)->postJson('/api/maintenance-plans', [
            'asset_id' => $assetId,
            'title' => 'Plan annuel',
            'periodicity_days' => 365,
            'next_due_date' => now()->addMonth()->toDateString(),
        ])->assertStatus(403);

        $gestionnaire = $this->userWithRole('GESTIONNAIRE');
        $gestionnaireToken = auth('api')->login($gestionnaire);

        $response = $this->withToken($gestionnaireToken)->postJson('/api/maintenance-plans', [
            'asset_id' => $assetId,
            'title' => 'Plan annuel',
            'periodicity_days' => 365,
            'next_due_date' => now()->addMonth()->toDateString(),
        ]);

        $response->assertStatus(201)->assertJsonPath('active', true);
        $planId = $response->json('id');

        $this->withToken($gestionnaireToken)->patchJson("/api/maintenance-plans/{$planId}", [
            'periodicity_days' => 180,
        ])->assertStatus(200)->assertJsonPath('periodicity_days', 180);

        $this->withToken($gestionnaireToken)->deleteJson("/api/maintenance-plans/{$planId}")->assertStatus(204);
        $this->assertDatabaseMissing('maintenance_plans', ['id' => $planId]);

        $auditeur = $this->userWithRole('AUDITEUR');
        $auditeurToken = auth('api')->login($auditeur);

        MaintenancePlan::create([
            'asset_id' => $assetId,
            'title' => 'Plan lecture',
            'periodicity_days' => 90,
            'next_due_date' => now()->addDays(5)->toDateString(),
            'active' => true,
        ]);

        $this->withToken($auditeurToken)->getJson('/api/maintenance-plans')->assertStatus(200);
    }

    public function test_check_due_command_creates_planned_interventions(): void
    {
        $assetId = '99999999-9999-9999-9999-999999999999';

        $duePlan = MaintenancePlan::create([
            'asset_id' => $assetId,
            'title' => 'Plan échu',
            'periodicity_days' => 30,
            'next_due_date' => now()->subDay()->toDateString(),
            'active' => true,
        ]);
        $futurePlan = MaintenancePlan::create([
            'asset_id' => $assetId,
            'title' => 'Plan futur',
            'periodicity_days' => 30,
            'next_due_date' => now()->addMonth()->toDateString(),
            'active' => true,
        ]);
        $openPlan = MaintenancePlan::create([
            'asset_id' => $assetId,
            'title' => 'Plan déjà ouvert',
            'periodicity_days' => 30,
            'next_due_date' => now()->subDays(5)->toDateString(),
            'active' => true,
        ]);
        MaintenanceIntervention::create([
            'asset_id' => $assetId,
            'plan_id' => $openPlan->id,
            'type' => 'PREVENTIVE',
            'title' => 'Plan déjà ouvert',
            'status' => 'PLANIFIEE',
            'scheduled_date' => now()->subDays(5)->toDateString(),
        ]);

        $this->artisan('maintenance:check-due')->assertSuccessful();

        $this->assertDatabaseHas('maintenance_interventions', [
            'plan_id' => $duePlan->id,
            'type' => 'PREVENTIVE',
            'status' => 'PLANIFIEE',
        ]);
        $this->assertDatabaseMissing('maintenance_interventions', [
            'plan_id' => $futurePlan->id,
        ]);
        $this->assertSame(
            1,
            MaintenanceIntervention::where('plan_id', $openPlan->id)->count()
        );

        $this->artisan('maintenance:check-due')->assertSuccessful();

        $this->assertSame(
            1,
            MaintenanceIntervention::where('plan_id', $duePlan->id)->count(),
            'La commande ne doit pas dupliquer une intervention ouverte.'
        );
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
