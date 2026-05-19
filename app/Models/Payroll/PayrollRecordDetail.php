<?php

namespace App\Models\Payroll;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\Activitylog\LogOptions;

class PayrollRecordDetail extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'payroll_record_id',
        'type',
        'name',
        'code',
        'amount',
        'calculation_basis',
        'rate',
        'units',
        'is_taxable',
        'is_pensionable',
        'description',
        'metadata',
        'type_id',
        'employee_id',
        'payroll_period_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'calculation_basis' => 'decimal:2',
        'rate' => 'decimal:4',
        'units' => 'decimal:2',
        'is_taxable' => 'boolean',
        'is_pensionable' => 'boolean',
        'metadata' => 'array'
    ];

    // Detail types
    const TYPE_ALLOWANCE = 'allowance';
    const TYPE_DEDUCTION = 'deduction';
    const TYPE_STATUTORY_DEDUCTION = 'statutory_deduction';
    const TYPE_EARNING = 'earning';
    const TYPE_COMPANY_CONTRIBUTION = 'company_contribution';

    const TYPES = [
        self::TYPE_ALLOWANCE => 'Allowance',
        self::TYPE_DEDUCTION => 'Deduction',
        self::TYPE_STATUTORY_DEDUCTION => 'Statutory Deduction',
        self::TYPE_EARNING => 'Earning',
        self::TYPE_COMPANY_CONTRIBUTION => 'Company Contribution'
    ];

    /**
     * Relationship with Payroll Record
     */
    public function payrollRecord()
    {
        return $this->belongsTo(PayrollRecord::class);
    }

    /**
     * Scope for allowances
     */
    public function scopeAllowances($query)
    {
        return $query->where('type', self::TYPE_ALLOWANCE);
    }

    /**
     * Scope for deductions
     */
    public function scopeDeductions($query)
    {
        return $query->where('type', self::TYPE_DEDUCTION);
    }

    /**
     * Scope for statutory deductions
     */
    public function scopeStatutoryDeductions($query)
    {
        return $query->where('type', self::TYPE_STATUTORY_DEDUCTION);
    }

    /**
     * Scope for taxable items
     */
    public function scopeTaxable($query)
    {
        return $query->where('is_taxable', true);
    }

    /**
     * Scope for pensionable items
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
