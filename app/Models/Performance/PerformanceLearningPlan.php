<?php

namespace App\Models\Performance;

use Illuminate\Database\Eloquent\Model;

class PerformanceLearningPlan extends Model
{
    protected $table = 'performance_learning_plans';
    protected $primaryKey = 'learning_plan_id';

    protected $fillable = [
        'appraisal_id',
        'course_title',
        'due_date',
        'learning_hours',
        'mid_year_status',
        'end_year_status',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function appraisal()
    {
        return $this->belongsTo(PerformanceAppraisal::class, 'appraisal_id', 'appraisal_id');
    }
}
