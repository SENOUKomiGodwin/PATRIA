<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class InventoryLine extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'campaign_id',
        'asset_id',
        'expected_location_id',
        'found_location_id',
        'result',
        'counted_by',
    ];

    protected $casts = [
        'asset_id' => 'string',
        'expected_location_id' => 'integer',
        'found_location_id' => 'integer',
        'counted_by' => 'integer',
    ];

    public function campaign()
    {
        return $this->belongsTo(InventoryCampaign::class, 'campaign_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'counted_by');
    }
}
