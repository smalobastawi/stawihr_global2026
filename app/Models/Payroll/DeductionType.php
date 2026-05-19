<?php

namespace App\Models\Payroll;

use App\Models\EmployeeDeductions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\Activitylog\LogOptions;


class DeductionType extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'code',
        'description',
        'default_calculation_type',
        'default_amount',
        'default_percentage',
        'is_statutory',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'default_amount' => 'decimal:2',
        'default_percentage' => 'decimal:2',
        'is_statutory' => 'boolean',
        'is_active' => 'boolean'
    ];

    // Common Kenyan deduction types
    const COMMON_DEDUCTIONS = [
        'loan_repayment' => 'Loan Repayment',
        'advance_salary' => 'Advance Salary',
        'insurance_premium' => 'Insurance Premium',
        'union_dues' => 'Union Dues',
        'welfare_contribution' => 'Welfare Contribution',
        'disciplinary_fine' => 'Disciplinary Fine',
        'uniform_deduction' => 'Uniform Deduction',
        'canteen_deduction' => 'Canteen Deduction',
        'cooperative_contribution' => 'Cooperative Contribution',
        'sacco_contribution' => 'SACCO Contribution'
    ];

    // Statutory deductions
    const STATUTORY_DEDUCTIONS = [
        'paye' => 'PAYE Tax',
        'nssf' => 'NSSF Contribution',
        'shif' => 'SHIF Contribution',
        'housing_levy' => 'Housing Levy'
    ];

    /**
     * Relationship with Employee Deductions
     */
    public function employeeDeductions()
    {
        return $this->hasMany(EmployeeDeductions::class, 'payroll_deduction_type_id', 'id');
    }

    /**
     * Scope for active deduction types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for statutory deduction types
     */
    public function scopeStatutory($query)
    {
        return $query->where('is_statutory', true);
    }

    /**
     * Scope for non-statutory deduction types
     */
    public function scopeNonStatutory($query)
    {
        return $query->where('is_statutory', false);
    }

    /**
     * Get deduction type by code
     */
    public static function getByCode($code)
    {
        return self::where('code', $code)->where('is_active', true)->first();
    }
    public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
    }
}
