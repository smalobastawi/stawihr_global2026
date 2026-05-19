<?php

namespace App\Models\Performance;

use Illuminate\Database\Eloquent\Model;

class PerformanceAppraisalScore extends Model
{
    protected $table = 'performance_appraisal_scores';
    protected $primaryKey = 'score_id';

    protected $fillable = [
        'appraisal_id',
        'goal_id',
        'itemized_weighting',
        'self_weighting',
        'review_weighting',
        'self_comments',
        'review_comments',
    ];

    protected $casts = [
        'itemized_weighting' => 'decimal:2',
        'self_weighting' => 'decimal:2',
        'review_weighting' => 'decimal:2',
    ];

    public function appraisal()
    {
        return $this->belongsTo(PerformanceAppraisal::class, 'appraisal_id', 'appraisal_id');
    }

    public function goal()
    {
        return $this->belongsTo(PerformanceGoal::class, 'goal_id', 'goal_id');
    }
}
