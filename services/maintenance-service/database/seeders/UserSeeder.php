<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Admin Principal', 'email' => 'admin@patria.gov', 'role' => 'ADMIN', 'service_id' => 'SRV-001'],
            ['name' => 'Gestionnaire Services', 'email' => 'gestionnaire@patria.gov', 'role' => 'GESTIONNAIRE', 'service_id' => 'SRV-002'],
            ['name' => 'Agent Opérationnel', 'email' => 'agent@patria.gov', 'role' => 'AGENT', 'service_id' => 'SRV-003'],
            ['name' => 'Auditeur Interne', 'email' => 'auditeur@patria.gov', 'role' => 'AUDITEUR', 'service_id' => 'SRV-004'],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('Password123!'),
                    'role_id' => Role::where('name', $userData['role'])->value('id'),
                    'service_id' => $userData['service_id'],
                    'active' => true,
                ]
            );
        }
    }
}
