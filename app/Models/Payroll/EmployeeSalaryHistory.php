<?php

namespace App\Models\Payroll;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalaryHistory extends Model
{
    protected $table = 'employee_salary_history';

    protected $fillable = [
        'employee_id',
        'previous_salary',
        'new_salary',
        'salary_change_amount',
        'salary_change_percentage',
        'effective_date',
        'change_type',
        'change_reason',
        'changed_by',
        'metadata'
    ];

    protected $casts = [
        'previous_salary' => 'decimal:2',
        'new_salary' => 'decimal:2',
        'salary_change_amount' => 'decimal:2',
        'salary_change_percentage' => 'decimal:2',
        'effective_date' => 'date',
        'metadata' => 'array'
    ];

    // Change types
    const CHANGE_TYPES = [
        'promotion' => 'Promotion',
        'annual_increment' => 'Annual Increment',
        'adjustment' => 'Salary Adjustment',
        'demotion' => 'Demotion',
        'market_correction' => 'Market Correction',
        'allowance_to_basic' => 'Allowance to Basic Conversion',
        'other' => 'Other'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Scope for changes during a period
    public function scopeDuringPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('effective_date', [$startDate, $endDate]);
    }

    // Scope for changes before a date
    public function scopeEffectiveBefore($query, $date)
    {
        return $query->where('effective_date', '<=', $date);
    }
}
