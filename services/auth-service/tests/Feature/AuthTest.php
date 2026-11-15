<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_login_success(): void
    {
        $role = Role::where('name', 'ADMIN')->first();
        $user = User::create([
            'uuid' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('Password123!'),
            'role_id' => $role->id,
            'service_id' => 'SRV-001',
            'active' => true,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
                'user' => [
                    'uuid',
                    'name',
                    'email',
                    'role',
                    'service_id',
                ],
            ]);
    }

    public function test_login_invalid_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422);
    }

    public function test_login_inactive_user(): void
    {
        $role = Role::where('name', 'ADMIN')->first();
        $user = User::create([
            'uuid' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
            'name' => 'Inactive User',
            'email' => 'inactive@example.com',
            'password' => bcrypt('Password123!'),
            'role_id' => $role->id,
            'service_id' => 'SRV-001',
            'active' => false,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'inactive@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(422);
    }

    public function test_me_requires_authentication(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(401);
    }

    public function test_me_returns_user_data(): void
    {
        $role = Role::where('name', 'ADMIN')->first();
        $user = User::create([
            'uuid' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('Password123!'),
            'role_id' => $role->id,
            'service_id' => 'SRV-001',
            'active' => true,
        ]);

        $token = auth()->guard('api')->login($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/me');

        $response->assertStatus(200)
            ->assertJson([
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'ADMIN',
                'service_id' => 'SRV-001',
            ]);
    }

    public function test_users_crud_requires_admin_role(): void
    {
        $role = Role::where('name', 'AGENT')->first();
        $user = User::create([
            'uuid' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
            'name' => 'Agent User',
            'email' => 'agent@example.com',
            'password' => bcrypt('Password123!'),
            'role_id' => $role->id,
            'service_id' => 'SRV-002',
            'active' => true,
        ]);

        $token = auth()->guard('api')->login($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/users');

        $response->assertStatus(403);
    }
}
