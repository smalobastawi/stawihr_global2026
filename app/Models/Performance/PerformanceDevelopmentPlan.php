<?php

namespace App\Models\Performance;

use Illuminate\Database\Eloquent\Model;

class PerformanceDevelopmentPlan extends Model
{
    protected $table = 'performance_development_plans';
    protected $primaryKey = 'development_plan_id';

    protected $fillable = [
        'appraisal_id',
        'competency_name',
        'expected_proficiency',
        'smart_objective',
        'self_rating',
        'self_comments',
        'reviewer_rating',
        'reviewer_comments',
        'agreed_rating',
        'competencies_of_focus',
    ];

    protected $casts = [
        'self_rating' => 'decimal:1',
        'reviewer_rating' => 'decimal:1',
        'agreed_rating' => 'decimal:1',
    ];

    public function appraisal()
    {
        return $this->belongsTo(PerformanceAppraisal::class, 'appraisal_id', 'appraisal_id');
    }
}
