<?php

namespace App\Models\Performance;

use Illuminate\Database\Eloquent\Model;

class PerformanceAppraisalBehavioralScore extends Model
{
    protected $table = 'performance_appraisal_behavioral_scores';
    protected $primaryKey = 'behavioral_score_id';

    protected $fillable = [
        'appraisal_id',
        'behavioral_item_id',
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

    public function behavioralItem()
    {
        return $this->belongsTo(PerformanceBehavioralItem::class, 'behavioral_item_id', 'behavioral_item_id');
    }
}
