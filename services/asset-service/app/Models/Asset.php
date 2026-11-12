<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'code',
        'name',
        'category_id',
        'acquisition_date',
        'acquisition_value',
        'status',
        'condition',
        'location_id',
        'service_id',
        'qr_code',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'acquisition_value' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function netBookValue(): float
    {
        if (!$this->category || $this->category->amortization_years <= 0) {
            return (float) $this->acquisition_value;
        }

        $yearsElapsed = $this->acquisition_date->diffInYears(now());
        $yearsElapsed = max(0, $yearsElapsed);

        $annualDepreciation = $this->acquisition_value / $this->category->amortization_years;
        $totalDepreciation = $annualDepreciation * $yearsElapsed;
        $netValue = max(0, $this->acquisition_value - $totalDepreciation);

        return round($netValue, 2);
    }

    public function depreciationSchedule(): array
    {
        $schedule = [];
        $years = $this->category->amortization_years;
        $annualDepreciation = $this->acquisition_value / $years;
        $cumulative = 0;

        for ($year = 0; $year <= $years; $year++) {
            if ($year === 0) {
                $schedule[] = [
                    'year' => $this->acquisition_date->year,
                    'depreciation' => 0,
                    'cumulative' => 0,
                    'net_book_value' => (float) $this->acquisition_value,
                ];
            } else {
                $cumulative += $annualDepreciation;
                $netValue = max(0, $this->acquisition_value - $cumulative);
                $schedule[] = [
                    'year' => $this->acquisition_date->year + $year,
                    'depreciation' => round($annualDepreciation, 2),
                    'cumulative' => round($cumulative, 2),
                    'net_book_value' => round($netValue, 2),
                ];
            }
        }

        return $schedule;
    }
}
