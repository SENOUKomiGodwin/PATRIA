<?php

namespace App\Console\Commands;

use App\Models\MaintenanceIntervention;
use App\Models\MaintenancePlan;
use Illuminate\Console\Command;

class CheckDueMaintenancePlans extends Command
{
    protected $signature = 'maintenance:check-due';

    protected $description = 'Crée une intervention planifiée pour chaque plan de maintenance échu';

    public function handle(): int
    {
        $plans = MaintenancePlan::query()
            ->where('active', true)
            ->whereDate('next_due_date', '<=', now()->toDateString())
            ->get();

        $created = 0;

        foreach ($plans as $plan) {
            $hasOpenIntervention = MaintenanceIntervention::query()
                ->where('plan_id', $plan->id)
                ->whereIn('status', ['PLANIFIEE', 'EN_COURS'])
                ->exists();

            if ($hasOpenIntervention) {
                continue;
            }

            MaintenanceIntervention::create([
                'asset_id' => $plan->asset_id,
                'plan_id' => $plan->id,
                'type' => 'PREVENTIVE',
                'title' => $plan->title,
                'status' => 'PLANIFIEE',
                'scheduled_date' => $plan->next_due_date->toDateString(),
            ]);

            $created++;
        }

        $this->info("{$created} intervention(s) planifiée(s) créée(s).");

        return self::SUCCESS;
    }
}
