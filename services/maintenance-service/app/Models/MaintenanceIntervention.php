<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MaintenanceIntervention extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'asset_id',
        'plan_id',
        'type',
        'title',
        'description',
        'status',
        'reported_by',
        'scheduled_date',
        'started_at',
        'ended_at',
        'cost',
        'provider',
        'report',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'cost' => 'decimal:2',
    ];

    public function plan()
    {
        return $this->belongsTo(MaintenancePlan::class, 'plan_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
