<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $table = 'leave_type';
    protected $primaryKey = 'leave_type_id';

    protected $fillable = [
        'leave_type_id', 'leave_type_name','num_of_day'
    ];

    public function leaveGroups()
    {
        return $this->belongsToMany(LeaveGroup::class)
            ->withPivot([
                'annual_entitlement',
                'carryover_days',
                'max_carryover_days', 
                'earning_rate',
                'gender', 
                'probation_period_days',
                'notice_period_days',
                'allow_half_day',
                'max_consecutive_days'
            ]);
    }

    public function leaveGroupSettings()
    {
        return $this->hasMany(LeaveGroupSetting::class, 'leave_type_id');
    }
}
