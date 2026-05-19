<?php

namespace App\Models\Pip;

use Illuminate\Database\Eloquent\Model;

class PipConcern extends Model
{
    protected $table = 'pip_concerns';
    protected $primaryKey = 'concern_id';

    protected $fillable = [
        'pip_id',
        'goal_id',
        'behavioral_item_id',
        'appraisal_score_id',
        'description',
        'actual_score',
        'target_score',
    ];

    protected $casts = [
        'actual_score' => 'decimal:2',
        'target_score' => 'decimal:2',
    ];

    public function pip()
    {
        return $this->belongsTo(PipPlan::class, 'pip_id', 'pip_id');
    }

    public function goal()
    {
        return $this->belongsTo(\App\Models\Performance\PerformanceGoal::class, 'goal_id', 'goal_id');
    }

    public function behavioralItem()
    {
        return $this->belongsTo(\App\Models\Performance\PerformanceBehavioralItem::class, 'behavioral_item_id', 'behavioral_item_id');
    }

    public function appraisalScore()
    {
        return $this->belongsTo(\App\Models\Performance\PerformanceAppraisalScore::class, 'appraisal_score_id', 'score_id');
    }
}
