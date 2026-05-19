<?php

namespace App\Models\Pip;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PipPlan extends Model
{
    use SoftDeletes;

    protected $table = 'pip_plans';
    protected $primaryKey = 'pip_id';

    protected $fillable = [
        'employee_id',
        'supervisor_id',
        'hr_manager_id',
        'appraisal_id',
        'position',
        'department_id',
        'designation_id',
        'plan_period_start',
        'plan_period_end',
        'purpose',
        'trigger_score',
        'trigger_type',
        'status',
        'outcome',
        'outcome_notes',
        'employee_acknowledged',
        'employee_ack_date',
        'supervisor_signed',
        'supervisor_sign_date',
        'hr_validated',
        'hr_validation_date',
        'is_locked',
        'created_by',
    ];

    protected $casts = [
        'plan_period_start' => 'date',
        'plan_period_end' => 'date',
        'trigger_score' => 'decimal:2',
        'employee_acknowledged' => 'boolean',
        'employee_ack_date' => 'datetime',
        'supervisor_signed' => 'boolean',
        'supervisor_sign_date' => 'datetime',
        'hr_validated' => 'boolean',
        'hr_validation_date' => 'datetime',
        'is_locked' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id', 'employee_id');
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class, 'department_id', 'department_id');
    }

    public function designation()
    {
        return $this->belongsTo(\App\Models\Designation::class, 'designation_id', 'designation_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'supervisor_id', 'employee_id');
    }

    public function hrManager()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'hr_manager_id', 'employee_id');
    }

    public function appraisal()
    {
        return $this->belongsTo(\App\Models\Performance\PerformanceAppraisal::class, 'appraisal_id', 'appraisal_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'created_by', 'employee_id');
    }

    public function concerns()
    {
        return $this->hasMany(PipConcern::class, 'pip_id', 'pip_id');
    }

    public function goals()
    {
        return $this->hasMany(PipGoal::class, 'pip_id', 'pip_id');
    }

    public function supportResources()
    {
        return $this->hasMany(PipSupportResource::class, 'pip_id', 'pip_id');
    }

    public function reviewSchedules()
    {
        return $this->hasMany(PipReviewSchedule::class, 'pip_id', 'pip_id')->orderBy('stage_number');
    }

    public function generateReviewSchedules()
    {
        $start = $this->plan_period_start;
        $stages = ['First Review', 'Second Review', 'Third Review', 'Fourth Review', 'Final Review'];

        foreach ($stages as $index => $stage) {
            $weeks = ($index + 1) * 2; // bi-weekly
            PipReviewSchedule::create([
                'pip_id' => $this->pip_id,
                'review_stage' => $stage,
                'stage_number' => $index + 1,
                'scheduled_date' => $start->copy()->addWeeks($weeks),
                'status' => 'pending',
            ]);
        }
    }

    public function isAcknowledged()
    {
        return $this->employee_acknowledged && $this->supervisor_signed;
    }

    public function canBeEdited()
    {
        return !$this->is_locked && in_array($this->status, ['draft', 'active', 'in_review']);
    }
}
