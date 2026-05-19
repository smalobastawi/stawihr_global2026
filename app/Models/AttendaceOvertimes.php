<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class AttendaceOvertimes extends Model
{
    //use BelongsToCompany;

    protected $table = 'attendance_overtime_approvals';

    protected $fillable = [
        'id',
        'employee_id',
        'approve_working_hour',
        'created_by',
        'updated_by',
        'date',
        'month',
        'national_id',
        'employee_id',
        'time_in',
        'time_out',
        'is_late',
        'late_time',
        'over_time',
        'approved_over_time',
        'total_time_worked',
        'updated_by',
        'approved_by',
        'department_id',
        'entry_type',
        'work_shift_id',
        'employee_type',
        'attendance_entry_id',
    ];

    protected $dates = ['date', 'time_in', 'time_out', 'updated_by'];
}
