<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Facades\LogBatch;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Traits\WithSupervisorPermissions;

class Attendance extends Model
{
    use HasFactory, LogsActivity, softDeletes;
    use BelongsToCompany;
    use WithSupervisorPermissions;

    protected $table = 'attendances';
    protected $primaryKey = 'id';
    protected $fillable = [
        'date',
        'month',
        'national_id',
        'employee_id',
        'time_in',
        'time_out',
        'is_late',
        'late_time',
        'over_time',
        'working_time',
        'workingHours',
        'total_time_worked',
        'approval_status',
        'presence_status',
        'sensor_id',
        'created_by',
        'updated_by',
        'approved_by',
        'department_id',
        'lunch_checkin',
        'entry_type',
        'work_shift_id',
        'employee_type',
        'approved_over_time',
        'overtime_approval_by',
        'payroll_number',
        'location_id'
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'lunch_checkin' => 'datetime',
            'time_in' => 'datetime',
            'time_out' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
    }


    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }
    public function workShift()
    {
        return $this->belongsTo(WorkShift::class, 'work_shift_id');
    }

    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class, 'employee_type');
    }
    public function section()
    {
        return $this->belongsTo(EmployeeType::class, 'section_id');
    }
    public function overtimeApproval()
    {
        return $this->belongsTo(AttendaceOvertimes::class, 'id', 'attendance_entry_id');
    }
}
