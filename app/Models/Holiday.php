<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $table = 'holiday';
    protected $primaryKey = 'holiday_id';

    protected $fillable = [
        'holiday_id', 'holiday_name'
    ];


    public function departments()
    {
        return $this->belongsToMany(Department::class, 'public_holiday_departments', 'holiday_id', 'department_id');
    }

    public function leaveGroups()
    {
        return $this->belongsToMany(LeaveGroup::class, 'public_holiday_leave_groups', 'holiday_id', 'leave_group_id');
    }
}
