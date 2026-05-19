<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use App\Models\Employee;

class LunchReport extends Model
{
    //use BelongsToCompany;

    use HasFactory;

    protected $fillable = [
        "employee_id",
        "first_name",
        "middle_name",
        "last_name",
        "date",
        'department_id',
        'created_by',
        'month',
        'lunch_checkin_time',
        'national_id',
        'sensor_id',
        'employee_type',
    ];

    protected $dates = ['date', 'created_by', 'lunch_checkin_time'];

    public function employeeDetails()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
