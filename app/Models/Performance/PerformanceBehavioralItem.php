<?php

namespace App\Models\Performance;

use Illuminate\Database\Eloquent\Model;

class PerformanceBehavioralItem extends Model
{
    protected $table = 'performance_behavioral_items';
    protected $primaryKey = 'behavioral_item_id';

    protected $fillable = [
        'item_name',
        'weight',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function behavioralScores()
    {
        return $this->hasMany(PerformanceAppraisalBehavioralScore::class, 'behavioral_item_id', 'behavioral_item_id');
    }
}
