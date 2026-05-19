<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApplicantEvaluation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'job_applicant_evaluations';
    protected $primaryKey = 'evaluation_id';

    protected $fillable = [
        'job_applicant_id',
        'evaluated_by',
        'job_requisition_id',
        'education_score',
        'experience_score',
        'technical_skills_score',
        'communication_score',
        'cultural_fit_score',
        'problem_solving_score',
        'overall_score',
        'strengths',
        'weaknesses',
        'notes',
        'recommendation',
        'interview_id',
        'evaluation_stage'
    ];

    protected $casts = [
        'overall_score' => 'decimal:2',
    ];

    // Recommendation constants
    const RECOMMENDATION_HIRE = 'hire';
    const RECOMMENDATION_REJECT = 'reject';
    const RECOMMENDATION_MAYBE = 'maybe';
    const RECOMMENDATION_SECOND_INTERVIEW = 'second_interview';

    // Evaluation stage constants
    const STAGE_SCREENING = 'screening';
    const STAGE_FIRST_INTERVIEW = 'first_interview';
    const STAGE_SECOND_INTERVIEW = 'second_interview';
    const STAGE_FINAL = 'final';

    /**
     * Get the applicant being evaluated
     */
    public function applicant()
    {
        return $this->belongsTo(JobApplicant::class, 'job_applicant_id');
    }

    /**
     * Get the evaluator
     */
    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    /**
     * Get the job requisition
     */
    public function jobRequisition()
    {
        return $this->belongsTo(JobRequisition::class, 'job_requisition_id');
    }

    /**
     * Get the interview (if evaluation is for an interview)
     */
    public function interview()
    {
        return $this->belongsTo(Interview::class, 'interview_id');
    }

    /**
     * Calculate overall score automatically
     */
    public function calculateOverallScore()
    {
        $scores = [
            $this->education_score,
            $this->experience_score,
            $this->technical_skills_score,
            $this->communication_score,
            $this->cultural_fit_score,
            $this->problem_solving_score
        ];

        // Filter out null values
        $scores = array_filter($scores, function($score) {
            return $score !== null;
        });

        if (count($scores) === 0) {
            return null;
        }

        $this->overall_score = round(array_sum($scores) / count($scores), 2);
        return $this->overall_score;
    }

    /**
     * Get recommendation label
     */
    public function getRecommendationLabelAttribute()
    {
        switch ($this->recommendation) {
            case self::RECOMMENDATION_HIRE:
                return 'Hire';
            case self::RECOMMENDATION_REJECT:
                return 'Reject';
            case self::RECOMMENDATION_MAYBE:
                return 'Maybe';
            case self::RECOMMENDATION_SECOND_INTERVIEW:
                return 'Second Interview';
            default:
                return 'Pending';
        }
    }

    /**
     * Get recommendation CSS class
     */
    public function getRecommendationClassAttribute()
    {
        switch ($this->recommendation) {
            case self::RECOMMENDATION_HIRE:
                return 'success';
            case self::RECOMMENDATION_REJECT:
                return 'danger';
            case self::RECOMMENDATION_MAYBE:
                return 'warning';
            case self::RECOMMENDATION_SECOND_INTERVIEW:
                return 'info';
            default:
                return 'default';
        }
    }

    /**
     * Get evaluation stage label
     */
    public function getEvaluationStageLabelAttribute()
    {
        switch ($this->evaluation_stage) {
            case self::STAGE_SCREENING:
                return 'Initial Screening';
            case self::STAGE_FIRST_INTERVIEW:
                return 'First Interview';
            case self::STAGE_SECOND_INTERVIEW:
                return 'Second Interview';
            case self::STAGE_FINAL:
                return 'Final Evaluation';
            default:
                return $this->evaluation_stage;
        }
    }

    /**
     * Scope for filtering by applicant
     */
    public function scopeByApplicant($query, $applicantId)
    {
        return $query->where('job_applicant_id', $applicantId);
    }

    /**
     * Scope for filtering by stage
     */
    public function scopeByStage($query, $stage)
    {
        return $query->where('evaluation_stage', $stage);
    }

    /**
     * Scope for filtering by evaluator
     */
    public function scopeByEvaluator($query, $evaluatorId)
    {
        return $query->where('evaluated_by', $evaluatorId);
    }

    /**
     * Boot method to calculate overall score before saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($evaluation) {
            $evaluation->calculateOverallScore();
        });
    }
}
