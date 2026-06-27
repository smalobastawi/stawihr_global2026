<?php

namespace App\Models\Pdp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PdpPlan extends Model
{
    use SoftDeletes;

    protected $table = 'pdp_plans';
    protected $primaryKey = 'pdp_plan_id';

    protected $fillable = [
        'employee_id',
        'supervisor_id',
        'department_id',
        'designation_id',
        'plan_title',
        'plan_year',
        'start_date',
        'end_date',
        'review_frequency',
        'development_focus',
        'career_aspirations',
        'status',
        'employee_acknowledged',
        'employee_ack_date',
        'employee_comments',
        'supervisor_approved',
        'supervisor_approve_date',
        'supervisor_comments',
        'hr_reviewed',
        'hr_review_date',
        'hr_comments',
        'overall_summary',
        'created_by',
    ];

    protected $casts = [
        'plan_year' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'employee_acknowledged' => 'boolean',
        'employee_ack_date' => 'datetime',
        'supervisor_approved' => 'boolean',
        'supervisor_approve_date' => 'datetime',
        'hr_reviewed' => 'boolean',
        'hr_review_date' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id', 'employee_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'supervisor_id', 'employee_id');
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class, 'department_id', 'department_id');
    }

    public function designation()
    {
        return $this->belongsTo(\App\Models\Designation::class, 'designation_id', 'designation_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'created_by', 'employee_id');
    }

    public function goals()
    {
        return $this->hasMany(PdpGoal::class, 'pdp_plan_id', 'pdp_plan_id')->orderBy('sort_order');
    }

    public function progressEntries()
    {
        return $this->hasMany(PdpProgressEntry::class, 'pdp_plan_id', 'pdp_plan_id');
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft', 'active'], true);
    }

    public function averageProgress(): int
    {
        $goals = $this->goals;

        if ($goals->isEmpty()) {
            return 0;
        }

        return (int) round($goals->avg('overall_progress'));
    }

    public function reviewPeriodLabel(int $year, ?int $quarter = null, ?int $half = null): string
    {
        if ($this->review_frequency === 'quarterly' && $quarter) {
            return "Q{$quarter} {$year}";
        }

        if ($this->review_frequency === 'bi_annually' && $half) {
            return $half === 1 ? "H1 {$year}" : "H2 {$year}";
        }

        return (string) $year;
    }
}
