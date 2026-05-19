<?php

namespace App\Models;

use App\Lib\Enumerations\ApprovalStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Facades\LogBatch;
use App\Lib\Enumerations\CalculationTypes;
use App\Lib\Enumerations\GeneralStatus;
use App\Lib\Enumerations\PaymentFrequency;
use App\Models\Payroll\DeductionType;
use App\Traits\HasApprovalWorkflow;
use App\Traits\ProvidesApprovalDetails;

class EmployeeDeductions extends Model
{
    //use BelongsToCompany;

    use HasFactory,  LogsActivity,  HasApprovalWorkflow, ProvidesApprovalDetails, SoftDeletes;

    protected $table = 'employee_deductions';

    protected $fillable = [
        'employee_id',
        'payroll_deduction_type_id',
        'deduction_category',
        'calculation_type',
        'amount',
        'percentage',
        'rate',
        'units',
        'limit_per_month',
        'limit_per_year',
        'reference_number',
        'is_tax_deductible',
        'is_recurring',
        'frequency',
        'effective_from',
        'effective_to',
        'payroll_year',
        'payroll_month',
        'description',
        'approved_by',
        'approved_at',
        'approval_notes',
        'created_by',
        'updated_by',
        'status',
        'financial_year_id',
        'approval_status',
        'date_approved',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'percentage' => 'decimal:2',
        'rate' => 'decimal:2',
        'limit_per_month' => 'decimal:2',
        'limit_per_year' => 'decimal:2',
        'is_tax_deductible' => 'boolean',
        'is_recurring' => 'boolean',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'approved_at' => 'datetime',
        'payroll_year' => 'integer',
        'payroll_month' => 'integer',
        'units' => 'integer',
    ];

    protected $appends = [
        'calculated_deduction_amount',
        'is_expired',
        'is_effective',
        'formatted_amount',
        'formatted_calculated_deduction_amount',
        'calculation_type_name',
        'frequency_name'
    ];

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Employee deduction {$eventName}");
    }

    /**
     * Relationships
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function payrollDeductionType()
    {
        return $this->belongsTo(DeductionType::class, 'payroll_deduction_type_id', 'id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', GeneralStatus::ACTIVE);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeForPayrollPeriod($query, $year, $month)
    {
        return $query->where('payroll_year', $year)
            ->where('payroll_month', $month);
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeTaxDeductible($query)
    {
        return $query->where('is_tax_deductible', true);
    }

    public function scopeByCalculationType($query, $type)
    {
        return $query->where('calculation_type', $type);
    }

    public function scopeEffectiveOn($query, $date = null)
    {
        $date = $date ?: Carbon::now();
        return $query->where('effective_from', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', $date);
            });
    }

    public function scopeApproved($query)
    {
        //where approval_status is approved
        return $query->where('approval_status', ApprovalStatus::APPROVED);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('approval_status', ApprovalStatus::PENDING);
    }

    /**
     * Accessors & Mutators
     */
    public function getCalculatedDeductionAmountAttribute()
    {
        return $this->calculateDeductionAmount();
    }

    public function getIsExpiredAttribute()
    {
        return $this->effective_to && Carbon::parse($this->effective_to)->isPast();
    }

    public function getIsEffectiveAttribute()
    {
        $now = Carbon::now();
        return Carbon::parse($this->effective_from)->lte($now) &&
            (!$this->effective_to || Carbon::parse($this->effective_to)->gte($now));
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount ?? 0, 2);
    }

    public function getFormattedCalculatedDeductionAmountAttribute()
    {
        return number_format($this->calculated_deduction_amount ?? 0, 2);
    }

    public function getCalculationTypeNameAttribute()
    {
        return CalculationTypes::getName($this->calculation_type);
    }

    public function getFrequencyNameAttribute()
    {
        return PaymentFrequency::getName($this->frequency);
    }

    public function getIsApprovedAttribute()
    {
        return !is_null($this->approved_at);
    }

    /**
     * Business Logic Methods
     */
    public function calculateDeductionAmount($basicSalary = null, $grossSalary = null)
    {
        $basicSalary = $basicSalary ?? $this->employee->basic_salary ?? 0;
        $grossSalary = $grossSalary ?? $this->employee->gross_salary ?? 0;

        $amount = match ($this->calculation_type) {
            CalculationTypes::FIXED_AMOUNT => $this->amount,
            CalculationTypes::PERCENTAGE_OF_BASIC => ($basicSalary * $this->percentage) / 100,
            CalculationTypes::PERCENTAGE_OF_GROSS => ($grossSalary * $this->percentage) / 100,

            CalculationTypes::DAILY_RATE => $this->rate * ($this->units ?? 0),
            default => $this->amount
        };

        return $this->applyLimits($amount);
    }

    public function applyLimits($calculatedAmount)
    {
        // Apply monthly limit
        if ($this->limit_per_month && $calculatedAmount > $this->limit_per_month) {
            $calculatedAmount = $this->limit_per_month;
        }

        // Apply yearly limit
        if ($this->limit_per_year) {
            $yearlyTotal = $this->getYearlyTotal();
            $remaining = max(0, $this->limit_per_year - $yearlyTotal);
            $calculatedAmount = min($calculatedAmount, $remaining);
        }

        return round($calculatedAmount, 2);
    }

    public function getYearlyTotal()
    {
        return static::where('employee_id', $this->employee_id)
            ->where('payroll_deduction_type_id', $this->payroll_deduction_type_id)
            ->where('payroll_year', $this->payroll_year)
            ->where('id', '!=', $this->id)
            ->sum('amount');
    }

    public function isApplicableForPeriod($year, $month)
    {
        $periodDate = Carbon::createFromDate($year, $month, 1);

        return $this->is_effective &&
            $periodDate->between(
                Carbon::parse($this->effective_from),
                $this->effective_to ? Carbon::parse($this->effective_to) : Carbon::now()->addCentury()
            );
    }

    public function shouldRecurForPeriod($year, $month)
    {
        if (!$this->is_recurring) return false;

        $periodDate = Carbon::createFromDate($year, $month, 1);
        $effectiveFrom = Carbon::parse($this->effective_from);

        return match ($this->frequency) {
            PaymentFrequency::MONTHLY => true,
            PaymentFrequency::WEEKLY => $periodDate->diffInWeeks($effectiveFrom) % 1 === 0,
            PaymentFrequency::BI_WEEKLY => $periodDate->diffInWeeks($effectiveFrom) % 2 === 0,
            PaymentFrequency::QUARTERLY => $periodDate->diffInMonths($effectiveFrom) % 3 === 0,
            PaymentFrequency::ANNUALLY => $periodDate->diffInYears($effectiveFrom) >= 1 &&
                $periodDate->month === $effectiveFrom->month,
            PaymentFrequency::ONE_TIME => false,
            default => true
        };
    }

    /**
     * Approval methods
     */
    public function approve(User $approver, string $notes = null)
    {
        $this->update([
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'approval_notes' => $notes,
            'status' => 1 // Set status to active upon approval
        ]);

        return $this;
    }

    public function reject(User $rejector, string $notes = null)
    {
        $this->update([
            'approved_by' => $rejector->id,
            'approved_at' => now(),
            'approval_notes' => $notes,
            'deleted_at' => now()
        ]);

        return $this;
    }

    /**
     * Static methods for bulk operations
     */
    public static function getActiveDeductionsForEmployee($employeeId, $year = null, $month = null)
    {
        $query = static::active()
            ->forEmployee($employeeId)
            ->with(['payrollDeductionType', 'employee']);

        if ($year && $month) {
            $query->where(function ($q) use ($year, $month) {
                $q->forPayrollPeriod($year, $month)
                    ->orWhere(function ($subQ) use ($year, $month) {
                        $subQ->recurring()
                            ->effectiveOn(Carbon::createFromDate($year, $month, 1));
                    });
            });
        }

        return $query->get();
    }

    public static function calculateTotalDeductionsForEmployee($employeeId, $year, $month)
    {
        return static::getActiveDeductionsForEmployee($employeeId, $year, $month)
            ->sum(fn($deduction) => $deduction->calculated_deduction_amount);
    }

    /**
     * Validation rules
     */
    public static function rules($id = null)
    {
        return [
            'employee_id' => 'required|exists:employees,employee_id',
            'payroll_deduction_type_id' => 'required|exists:payroll_deduction_types,id',
            'calculation_type' => 'required|in:' . implode(',', CalculationTypes::toArray()),
            'amount' => 'required_if:calculation_type,fixed_amount|numeric|min:0',
            'percentage' => 'required_if:calculation_type,percentage_of_basic,percentage_of_gross|numeric|between:0,100',
            'rate' => 'required_if:calculation_type,hourly_rate,daily_rate|numeric|min:0',
            'units' => 'required_if:calculation_type,hourly_rate,daily_rate|integer|min:1',
            'limit_per_month' => 'nullable|numeric|min:0',
            'limit_per_year' => 'nullable|numeric|min:0',
            'is_tax_deductible' => 'boolean',
            'is_recurring' => 'boolean',
            'frequency' => 'required_if:is_recurring,true|in:' . implode(',', PaymentFrequency::toArray()),
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'payroll_year' => 'required|integer|min:2000|max:2100',
            'payroll_month' => 'required|integer|min:1|max:12',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function getApprovalDetails(): array
    {
        return [
            'Employee' => $this->employee->full_name ?? 'N/A',
            'Deduction Type' => $this->payrollDeductionType->name ?? 'N/A',
            'Amount' => number_format($this->amount, 2),
            'Effective From' => $this->effective_from->format('Y-m-d'),
            'Effective To' => $this->effective_to?->format('Y-m-d') ?? 'N/A'
        ];
    }
}
