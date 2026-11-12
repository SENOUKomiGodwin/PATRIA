<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'ADMIN')->first();
        $gestionnaireRole = Role::where('name', 'GESTIONNAIRE')->first();
        $agentRole = Role::where('name', 'AGENT')->first();
        $auditeurRole = Role::where('name', 'AUDITEUR')->first();

        User::firstOrCreate(
            ['email' => 'admin@patria.gov'],
            [
                'name' => 'Admin Principal',
                'password' => bcrypt('Password123!'),
                'role_id' => $adminRole->id,
                'service_id' => 'SRV-001',
                'active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'gestionnaire@patria.gov'],
            [
                'name' => 'Gestionnaire Services',
                'password' => bcrypt('Password123!'),
                'role_id' => $gestionnaireRole->id,
                'service_id' => 'SRV-002',
                'active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'agent@patria.gov'],
            [
                'name' => 'Agent Opérationnel',
                'password' => bcrypt('Password123!'),
                'role_id' => $agentRole->id,
                'service_id' => 'SRV-003',
                'active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'auditeur@patria.gov'],
            [
                'name' => 'Auditeur Interne',
                'password' => bcrypt('Password123!'),
                'role_id' => $auditeurRole->id,
                'service_id' => 'SRV-004',
                'active' => true,
            ]
        );
    }
}
