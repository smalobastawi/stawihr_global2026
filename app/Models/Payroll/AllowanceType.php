<?php

namespace App\Models\Payroll;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AllowanceType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'default_calculation_type',
        'default_amount',
        'default_percentage',
        'is_taxable',
        'is_pensionable',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'default_amount' => 'decimal:2',
        'default_percentage' => 'decimal:2',
        'is_taxable' => 'boolean',
        'is_pensionable' => 'boolean',
        'is_active' => 'boolean'
    ];

    // Common Kenyan allowance types
    const COMMON_ALLOWANCES = [
        'house_allowance' => 'House Allowance',
        'transport_allowance' => 'Transport Allowance',
        'medical_allowance' => 'Medical Allowance',
        'lunch_allowance' => 'Lunch Allowance',
        'overtime_allowance' => 'Overtime Allowance',
        'acting_allowance' => 'Acting Allowance',
        'hardship_allowance' => 'Hardship Allowance',
        'risk_allowance' => 'Risk Allowance',
        'commuter_allowance' => 'Commuter Allowance',
        'leave_allowance' => 'Leave Allowance'
    ];

    /**
     * Relationship with Employee Allowances
     */
    public function employeeAllowances()
    {
        return $this->hasMany(EmployeeAllowance::class);
    }

    /**
     * Scope for active allowance types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for taxable allowance types
     */
    public function scopeTaxable($query)
    {
        return $query->where('is_taxable', true);
    }

    /**
     * Scope for pensionable allowance types
     */
    public function scopePensionable($query)
    {
        return $query->where('is_pensionable', true);
    }

    /**
     * Get allowance type by code
     */
    public static function getByCode($code)
    {
        return self::where('code', $code)->where('is_active', true)->first();
    }
}