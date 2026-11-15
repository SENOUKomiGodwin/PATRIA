<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'ADMIN')->first();
        $gestionnaireRole = Role::where('name', 'GESTIONNAIRE')->first();
        $agentRole = Role::where('name', 'AGENT')->first();
        $auditeurRole = Role::where('name', 'AUDITEUR')->first();

        User::firstOrCreate(
            ['email' => 'admin@patria.gov'],
            [
                'uuid' => Uuid::uuid4()->toString(),
                'name' => 'Admin PATRIA',
                'password' => Hash::make('Password123!'),
                'role_id' => $adminRole->id,
                'service_id' => 'SRV-001',
                'active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'gestionnaire@patria.gov'],
            [
                'uuid' => Uuid::uuid4()->toString(),
                'name' => 'Gestionnaire PATRIA',
                'password' => Hash::make('Password123!'),
                'role_id' => $gestionnaireRole->id,
                'service_id' => 'SRV-002',
                'active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'agent@patria.gov'],
            [
                'uuid' => Uuid::uuid4()->toString(),
                'name' => 'Agent PATRIA',
                'password' => Hash::make('Password123!'),
                'role_id' => $agentRole->id,
                'service_id' => 'SRV-003',
                'active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'auditeur@patria.gov'],
            [
                'uuid' => Uuid::uuid4()->toString(),
                'name' => 'Auditeur PATRIA',
                'password' => Hash::make('Password123!'),
                'role_id' => $auditeurRole->id,
                'service_id' => 'SRV-004',
                'active' => true,
            ]
        );
    }
}
