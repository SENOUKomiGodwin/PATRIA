<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MaintenancePlan extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'asset_id',
        'title',
        'periodicity_days',
        'next_due_date',
        'active',
    ];

    protected $casts = [
        'periodicity_days' => 'integer',
        'next_due_date' => 'date',
        'active' => 'boolean',
    ];

    public function interventions()
    {
        return $this->hasMany(MaintenanceIntervention::class, 'plan_id');
    }
}
