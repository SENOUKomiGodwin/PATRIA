<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'amortization_years', 'method'];

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
