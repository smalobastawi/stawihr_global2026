<?php

namespace App\Models\Performance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerformanceAppraisal extends Model
{
    use SoftDeletes;

    protected $table = 'performance_appraisals';
    protected $primaryKey = 'appraisal_id';

    protected $fillable = [
        'employee_id',
        'supervisor_id',
        'review_period',
        'review_start_date',
        'review_end_date',
        'status',
        'total_itemized_weighting',
        'total_self_weighting',
        'total_review_weighting',
        'employee_comments',
        'supervisor_comments',
        'hod_comments',
        'employee_signed',
        'employee_sign_date',
        'supervisor_signed',
        'supervisor_sign_date',
        'hod_signed',
        'hod_sign_date',
        'finalized_by',
        'finalized_at',
    ];

    protected $casts = [
        'review_start_date' => 'date',
        'review_end_date' => 'date',
        'total_itemized_weighting' => 'decimal:2',
        'total_self_weighting' => 'decimal:2',
        'total_review_weighting' => 'decimal:2',
        'employee_signed' => 'boolean',
        'employee_sign_date' => 'datetime',
        'supervisor_signed' => 'boolean',
        'supervisor_sign_date' => 'datetime',
        'hod_signed' => 'boolean',
        'hod_sign_date' => 'datetime',
        'finalized_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id', 'employee_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'supervisor_id', 'employee_id');
    }

    public function finalizer()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'finalized_by', 'employee_id');
    }

    public function scores()
    {
        return $this->hasMany(PerformanceAppraisalScore::class, 'appraisal_id', 'appraisal_id');
    }

    public function behavioralScores()
    {
        return $this->hasMany(PerformanceAppraisalBehavioralScore::class, 'appraisal_id', 'appraisal_id');
    }

    public function developmentPlans()
    {
        return $this->hasMany(PerformanceDevelopmentPlan::class, 'appraisal_id', 'appraisal_id');
    }

    public function learningPlans()
    {
        return $this->hasMany(PerformanceLearningPlan::class, 'appraisal_id', 'appraisal_id');
    }

    public function pipPlans()
    {
        return $this->hasMany(\App\Models\Pip\PipPlan::class, 'appraisal_id', 'appraisal_id');
    }

    /**
     * Calculate final score based on weighted focus area scores.
     */
    public function calculateFinalScore()
    {
        $scores = $this->scores()->with(['goal.focusArea'])->get();

        $finalScore = 0;
        $focusAreaScores = [];

        foreach ($scores as $score) {
            if (!$score->goal || !$score->goal->focusArea) {
                continue;
            }

            $focusAreaId = $score->goal->focus_area_id;
            if (!isset($focusAreaScores[$focusAreaId])) {
                $focusAreaScores[$focusAreaId] = [
                    'total' => 0,
                    'weight' => $score->goal->focusArea->weight,
                ];
            }

            $focusAreaScores[$focusAreaId]['total'] += $score->review_weighting;
        }

        foreach ($focusAreaScores as $faScore) {
            $weighted = ($faScore['total']) * $faScore['weight'];
            $finalScore += $weighted;
        }

        // Add behavioral scores
        $behavioralTotal = $this->behavioralScores()->sum('review_weighting');
        $finalScore += $behavioralTotal;

        return round($finalScore, 2);
    }
}
