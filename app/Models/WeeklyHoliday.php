<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyHoliday extends Model
{
    protected $table = 'weekly_holiday';
    protected $primaryKey = 'week_holiday_id';

    protected $fillable = [
        'week_holiday_id', 'day_name','status'
    ];

    /**
     * Active weekly non-working days configured under Leave Management > Weekly Holiday.
     */
    public static function activeDayNames(): array
    {
        return static::query()
            ->where('status', 1)
            ->pluck('day_name')
            ->map(fn ($day) => strtolower($day))
            ->unique()
            ->values()
            ->all();
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'weekly_holiday_departments', 'holiday_id', 'department_id');
    }

    public function leaveGroups()
    {
        return $this->belongsToMany(LeaveGroup::class, 'weekly_holiday_leave_groups', 'holiday_id', 'leave_group_id');
    }

    
}
