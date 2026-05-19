<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveGroupSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        'leave_type_id',
        'leave_group_id',
        'annual_entitlement',
        'carryover_days',
        'max_carryover_days',
        'earning_rate',
        'gender',
        'probation_period_days',
        'notice_period_days',
        'allow_half_day',
        'paid',
        'accrual_frequency',
        'applicable_on',
        'max_consecutive_days',
        'active',
        'allow_advanced_leave',
        'advanced_period_months',
        'advanced_limit_days',
    ];

    // Relationship: Each setting belongs to a leave group
    public function leaveGroup()
    {
        return $this->belongsTo(LeaveGroup::class);
    }

    // Relationship: Each setting belongs to a leave type
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
}
