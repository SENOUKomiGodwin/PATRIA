<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'asset_id',
        'type',
        'from_service_id',
        'to_service_id',
        'to_location_id',
        'movement_date',
        'reason',
        'user_id',
    ];

    protected $casts = [
        'asset_id' => 'string',
        'to_location_id' => 'integer',
        'movement_date' => 'date',
        'user_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
