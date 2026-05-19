<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    // Relationship: A LeaveGroup has many settings
    public function settings()
    {
        return $this->hasMany(LeaveGroupSetting::class);
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_leavegroups', 'leave_group_id', 'employee_id');
    }

    public function publicHolidays()
    {
        return $this->belongsToMany(Holiday::class, 'public_holiday_leave_groups', 'leave_group_id', 'holiday_id');
    }

    public function weeklyHolidays()
    {
        return $this->belongsToMany(WeeklyHoliday::class, 'weekly_holiday_leave_groups', 'leave_group_id', 'holiday_id');
    }
}
