<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_unauthenticated_cannot_access_assets()
    {
        $response = $this->getJson('/api/assets');
        $response->assertStatus(401);
    }

    public function test_admin_can_list_all_assets()
    {
        $admin = User::where('email', 'admin@patria.gov')->first();
        $token = auth('api')->login($admin);

        $response = $this->withToken($token)->getJson('/api/assets');
        $response->assertStatus(200);
        $response->assertJsonCount(30, 'data');
    }

    public function test_gestionnaire_can_only_see_their_service_assets()
    {
        $gestionnaire = User::where('email', 'gestionnaire@patria.gov')->first();
        $token = auth('api')->login($gestionnaire);

        $response = $this->withToken($token)->getJson('/api/assets');
        $response->assertStatus(200);

        $assets = $response->json('data');
        foreach ($assets as $asset) {
            $this->assertEquals('SRV-002', $asset['service_id']);
        }
    }

    public function test_agent_can_only_see_their_service_assets()
    {
        $agent = User::where('email', 'agent@patria.gov')->first();
        $token = auth('api')->login($agent);

        $response = $this->withToken($token)->getJson('/api/assets');
        $response->assertStatus(200);

        $assets = $response->json('data');
        foreach ($assets as $asset) {
            $this->assertEquals('SRV-003', $asset['service_id']);
        }
    }

    public function test_auditeur_can_see_all_assets()
    {
        $auditeur = User::where('email', 'auditeur@patria.gov')->first();
        $token = auth('api')->login($auditeur);

        $response = $this->withToken($token)->getJson('/api/assets');
        $response->assertStatus(200);
        $response->assertJsonCount(30, 'data');
    }

    public function test_admin_can_create_asset()
    {
        $admin = User::where('email', 'admin@patria.gov')->first();
        $token = auth('api')->login($admin);
        $category = Category::first();

        $response = $this->withToken($token)->postJson('/api/assets', [
            'name' => 'Test Asset',
            'category_id' => $category->id,
            'acquisition_date' => now()->toDateString(),
            'acquisition_value' => 1000.00,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('assets', ['name' => 'Test Asset']);
    }

    public function test_gestionnaire_can_create_asset()
    {
        $gestionnaire = User::where('email', 'gestionnaire@patria.gov')->first();
        $token = auth('api')->login($gestionnaire);
        $category = Category::first();

        $response = $this->withToken($token)->postJson('/api/assets', [
            'name' => 'Test Asset',
            'category_id' => $category->id,
            'acquisition_date' => now()->toDateString(),
            'acquisition_value' => 1000.00,
        ]);

        $response->assertStatus(201);
    }

    public function test_agent_cannot_create_asset()
    {
        $agent = User::where('email', 'agent@patria.gov')->first();
        $token = auth('api')->login($agent);
        $category = Category::first();

        $response = $this->withToken($token)->postJson('/api/assets', [
            'name' => 'Test Asset',
            'category_id' => $category->id,
            'acquisition_date' => now()->toDateString(),
            'acquisition_value' => 1000.00,
        ]);

        $response->assertStatus(403);
    }

    public function test_auditeur_cannot_create_asset()
    {
        $auditeur = User::where('email', 'auditeur@patria.gov')->first();
        $token = auth('api')->login($auditeur);
        $category = Category::first();

        $response = $this->withToken($token)->postJson('/api/assets', [
            'name' => 'Test Asset',
            'category_id' => $category->id,
            'acquisition_date' => now()->toDateString(),
            'acquisition_value' => 1000.00,
        ]);

        $response->assertStatus(403);
    }

    public function test_filter_by_category()
    {
        $admin = User::where('email', 'admin@patria.gov')->first();
        $token = auth('api')->login($admin);
        $category = Category::where('name', 'Informatique')->first();

        $response = $this->withToken($token)->getJson('/api/assets?category_id=' . $category->id);
        $response->assertStatus(200);

        $assets = $response->json('data');
        foreach ($assets as $asset) {
            $this->assertEquals($category->id, $asset['category_id']);
        }
    }

    public function test_filter_by_status()
    {
        $admin = User::where('email', 'admin@patria.gov')->first();
        $token = auth('api')->login($admin);

        $response = $this->withToken($token)->getJson('/api/assets?status=EN_SERVICE');
        $response->assertStatus(200);

        $assets = $response->json('data');
        foreach ($assets as $asset) {
            $this->assertEquals('EN_SERVICE', $asset['status']);
        }
    }

    public function test_search_assets()
    {
        $admin = User::where('email', 'admin@patria.gov')->first();
        $token = auth('api')->login($admin);

        $response = $this->withToken($token)->getJson('/api/assets?search=Bureau');
        $response->assertStatus(200);

        $assets = $response->json('data');
        $this->assertGreaterThan(0, count($assets));
    }

    public function test_depreciation_calculation()
    {
        $asset = Asset::first();
        $admin = User::where('email', 'admin@patria.gov')->first();
        $token = auth('api')->login($admin);

        $response = $this->withToken($token)->getJson('/api/assets/' . $asset->id . '/depreciation');
        $response->assertStatus(200);

        $data = $response->json();
        $this->assertArrayHasKey('schedule', $data);
        $this->assertArrayHasKey('amortization_years', $data);
        $this->assertIsArray($data['schedule']);
    }

    public function test_net_book_value_never_negative()
    {
        $category = Category::where('name', 'Informatique')->first();
        $asset = Asset::where('category_id', $category->id)->first();

        $netValue = $asset->netBookValue();
        $this->assertGreaterThanOrEqual(0, $netValue);
    }

    public function test_qr_code_returns_svg()
    {
        $asset = Asset::first();
        $admin = User::where('email', 'admin@patria.gov')->first();
        $token = auth('api')->login($admin);

        $response = $this->withToken($token)->get('/api/assets/' . $asset->id . '/qr');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml');
        $this->assertStringContainsString('<svg', $response->getContent());
    }

    public function test_stats_endpoint()
    {
        $admin = User::where('email', 'admin@patria.gov')->first();
        $token = auth('api')->login($admin);

        $response = $this->withToken($token)->getJson('/api/assets/stats');
        $response->assertStatus(200);

        $data = $response->json();
        $this->assertArrayHasKey('total', $data);
        $this->assertArrayHasKey('total_acquisition_value', $data);
        $this->assertArrayHasKey('total_net_book_value', $data);
        $this->assertArrayHasKey('by_category', $data);
        $this->assertArrayHasKey('by_status', $data);
        $this->assertEquals(30, $data['total']);
    }

    public function test_pagination_works()
    {
        $admin = User::where('email', 'admin@patria.gov')->first();
        $token = auth('api')->login($admin);

        $response = $this->withToken($token)->getJson('/api/assets?per_page=10');
        $response->assertStatus(200);

        $data = $response->json();
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('meta', $data);
        $this->assertCount(10, $data['data']);
    }
}
