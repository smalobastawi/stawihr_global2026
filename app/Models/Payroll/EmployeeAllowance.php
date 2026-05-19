<?php

namespace App\Models\Payroll;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\Activitylog\LogOptions;


class EmployeeAllowance extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'employee_payroll_id',
        'allowance_type_id',
        'name',
        'calculation_type',
        'amount',
        'percentage',
        'is_taxable',
        'is_pensionable',
        'is_active',
        'effective_date',
        'end_date',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'percentage' => 'decimal:2',
        'is_taxable' => 'boolean',
        'is_pensionable' => 'boolean',
        'is_active' => 'boolean',
        'effective_date' => 'date',
        'end_date' => 'date'
    ];

    // Calculation types
    const CALCULATION_TYPES = [
        'fixed' => 'Fixed Amount',
        'percentage' => 'Percentage of Basic Income',
        'formula' => 'Custom Formula'
    ];

    /**
     * Relationship with Employee Payroll
     */
    public function employeePayroll()
    {
        return $this->belongsTo(EmployeePayroll::class);
    }

    /**
     * Relationship with Allowance Type
     */
    public function allowanceType()
    {
        return $this->belongsTo(AllowanceType::class);
    }

    /**
     * Calculate allowance amount
     */
    public function calculateAmount($basicSalary = null)
    {
        $basicSalary = $basicSalary ?? $this->employeePayroll->basic_salary;

        switch ($this->calculation_type) {
            case 'fixed':
                return $this->amount;
            case 'percentage':
                return ($basicSalary * $this->percentage / 100);
            case 'formula':
                // Custom formula calculation can be implemented here
                return $this->amount;
            default:
                return 0;
        }
    }

    /**
     * Scope for active allowances
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('effective_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Scope for taxable allowances
     */
    public function scopeTaxable($query)
    {
        return $query->where('is_taxable', true);
    }

    /**
     * Scope for pensionable allowances
     */
    public function scopePensionable($query)
    {
        return $query->where('is_pensionable', true);
    }
    public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
    }
}
