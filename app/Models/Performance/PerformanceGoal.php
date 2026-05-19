<?php

namespace App\Models\Performance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerformanceGoal extends Model
{
    use SoftDeletes;

    protected $table = 'performance_goals';
    protected $primaryKey = 'goal_id';

    protected $fillable = [
        'focus_area_id',
        'strategic_objective',
        'performance_metric',
        'performance_target',
        'key_initiatives',
        'itemized_weighting',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'itemized_weighting' => 'decimal:2',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function focusArea()
    {
        return $this->belongsTo(PerformanceFocusArea::class, 'focus_area_id', 'focus_area_id');
    }

    public function appraisalScores()
    {
        return $this->hasMany(PerformanceAppraisalScore::class, 'goal_id', 'goal_id');
    }
}
