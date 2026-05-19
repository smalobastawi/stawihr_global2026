<?php

namespace App\Models\Performance;

use Illuminate\Database\Eloquent\Model;

class ReviewPeriod extends Model
{
    protected $primaryKey = 'period_id';

    protected $fillable = [
        'period_name',
        'start_date',
        'end_date',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active periods
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order', 'asc');
    }

    /**
     * Get formatted period with dates
     */
    public function getFormattedPeriodAttribute()
    {
        return "{$this->period_name} ({$this->start_date->format('M d, Y')} - {$this->end_date->format('M d, Y')})";
    }
}
