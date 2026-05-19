<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToCompany;

class Department extends Model
{
    //use BelongsToCompany;

    use SoftDeletes;
    //use BelongsToCompany;

    protected $table = 'department';
    protected $primaryKey = 'department_id';

    protected $fillable = [
        'department_id',
        'department_name',
        'department_head_id'
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'department_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class,  'department_id');
    }

    public function weeklyHolidays()
    {
        return $this->belongsToMany(WeeklyHoliday::class, 'weekly_holiday_departments', 'department_id', 'holiday_id');
    }
    public function publicHolidays()
    {
        return $this->belongsToMany(Holiday::class, 'public_holiday_departments', 'department_id', 'holiday_id');
    }

    public function departmentHead()
    {
        return $this->belongsTo(Employee::class, 'department_head_id', 'employee_id');
    }
}
