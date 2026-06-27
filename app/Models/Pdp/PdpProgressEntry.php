<?php

namespace App\Models\Pdp;

use Illuminate\Database\Eloquent\Model;

class PdpProgressEntry extends Model
{
    protected $table = 'pdp_progress_entries';
    protected $primaryKey = 'pdp_progress_id';

    protected $fillable = [
        'pdp_plan_id',
        'pdp_goal_id',
        'review_frequency',
        'review_year',
        'review_quarter',
        'review_half',
        'review_period_label',
        'progress_percentage',
        'achievement_summary',
        'challenges',
        'support_needed',
        'next_steps',
        'status',
        'entered_by',
        'reviewed_by',
        'supervisor_comments',
        'submitted_at',
        'reviewed_at',
    ];

    protected $casts = [
        'review_year' => 'integer',
        'review_quarter' => 'integer',
        'review_half' => 'integer',
        'progress_percentage' => 'integer',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(PdpPlan::class, 'pdp_plan_id', 'pdp_plan_id');
    }

    public function goal()
    {
        return $this->belongsTo(PdpGoal::class, 'pdp_goal_id', 'pdp_goal_id');
    }

    public function enteredBy()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'entered_by', 'employee_id');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'reviewed_by', 'employee_id');
    }
}
