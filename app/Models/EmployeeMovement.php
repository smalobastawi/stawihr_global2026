<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EmployeeMovement extends Model
{
    //use BelongsToCompany;

    use HasFactory,  LogsActivity;

    protected $fillable = [
        'employee_id',
        'payroll_number',
        'current_department',
        'current_designation',
        'current_section_id',
        'current_group_id',
        'current_work_shift_id',
        'current_branch',
        'current_employee_type',
        'movement_date',
        'new_section_id',
        'new_group_id',
        'new_designation_id',
        'new_department_id',
        'new_work_shift_id',
        'new_branch',
        'new_employee_type',
        'new_employee_status',
        'description',
        'created_by',
        'updated_by',

    ];

    protected $dates = ['movement_date', 'created_at', 'updated_at'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function currentDepartment()
    {
        return $this->belongsTo(Department::class, 'employee_id');
    }
    public function newDepartment()
    {
        return $this->belongsTo(Department::class, 'employee_id');
    }
    public function currentDesignation()
    {
        return $this->belongsTo(Designation::class, 'id');
    }
    public function newDesignation()
    {
        return $this->belongsTo(Designation::class, 'id');
    }
    public function currentJobGroup()
    {
        return $this->belongsTo(JobGroup::class, 'id');
    }
    public function newJobGroup()
    {
        return $this->belongsTo(JobGroup::class, 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
    }
}
