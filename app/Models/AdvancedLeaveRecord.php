<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvancedLeaveRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'financial_year_id',
        'advanced_days',
        'recovered_days',
        'transactions',
    ];

    protected $casts = [
        'transactions' => 'array',
        'advanced_days' => 'decimal:2',
        'recovered_days' => 'decimal:2',
    ];

    // Relationship: Each record belongs to an employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // Relationship: Each record belongs to a leave type
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    // Relationship: Each record belongs to a financial year
    public function financialYear()
    {
        return $this->belongsTo(FinancialYear::class, 'financial_year_id');
    }

    /**
     * Get the net advanced days (advanced - recovered)
     */
    public function getNetAdvancedDaysAttribute()
    {
        return $this->advanced_days - $this->recovered_days;
    }

    /**
     * Check if advanced leave is available for a given amount
     */
    public function canAdvance($requestedDays)
    {
        return $this->net_advanced_days + $requestedDays <= $this->getAvailableAdvanceLimit();
    }

    /**
     * Get the available advance limit for this record
     */
    public function getAvailableAdvanceLimit()
    {
        // Get the leave group setting for this leave type
        $employee = $this->employee;
        if (!$employee) {
            return 0;
        }

        $leaveGroup = $employee->leaveGroups()->first();
        if (!$leaveGroup) {
            return 0;
        }

        $setting = $leaveGroup->settings()
            ->where('leave_type_id', $this->leave_type_id)
            ->first();

        if (!$setting || !$setting->allow_advanced_leave) {
            return 0;
        }

        // Calculate limit based on absolute days
        $annualEntitlement = $setting->annual_entitlement ?? 0;

        return $setting->advanced_limit_days ? min($setting->advanced_limit_days, $annualEntitlement) : 0;
    }
}
