<?php

namespace App\Console\Commands;

use App\Models\ReportSnapshot;
use App\Models\User;
use App\Services\ReportingService;
use Illuminate\Console\Command;
use Tymon\JWTAuth\Facades\JWTAuth;

class SnapshotDashboard extends Command
{
    protected $signature = 'reports:snapshot-daily';

    protected $description = 'Enregistre un snapshot quotidien du dashboard de reporting';

    public function handle(ReportingService $reporting): int
    {
        $user = User::whereHas('role', fn ($query) => $query->where('name', 'ADMIN'))
            ->where('active', true)
            ->first()
            ?? User::where('active', true)->first();

        if (! $user) {
            $this->warn('Aucun utilisateur actif trouvé pour générer le snapshot.');

            return self::FAILURE;
        }

        try {
            $token = JWTAuth::fromUser($user);
        } catch (\Throwable $exception) {
            $this->error('Impossible de générer le token JWT : '.$exception->getMessage());

            return self::FAILURE;
        }

        $data = $reporting->dashboard($token);

        ReportSnapshot::create([
            'type' => 'dashboard',
            'period' => now()->toDateString(),
            'data' => $data,
            'generated_at' => now(),
        ]);

        $this->info('Snapshot dashboard enregistré pour le '.now()->toDateString().'.');

        return self::SUCCESS;
    }
}
