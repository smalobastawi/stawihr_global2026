<?php

namespace App\Models;

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
use App\Models\Payroll\AllowanceType;
use App\Traits\HasApprovalWorkflow;
use App\Traits\ProvidesApprovalDetails;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeEarnings extends Model
{
    use HasFactory, LogsActivity, HasApprovalWorkflow, ProvidesApprovalDetails;
    use BelongsToCompany;

    protected $table = 'employee_earnings';

    protected $fillable = [
        'employee_id',
        'payroll_earning_type_id',
        'calculation_type',
        'amount',
        'percentage',
        'rate',
        'units',
        'limit_per_month',
        'limit_per_year',
        'is_taxable',
        'is_pensionable',
        'is_recurring',
        'frequency',
        'effective_from',
        'effective_to',
        'financial_year_id',
        'payroll_year',
        'payroll_month',
        'description',
        'approved_by',
        'approved_at',
        'approval_notes',
        'created_by',
        'updated_by',
        'earning_category',
        'status',
        'approval_status',
        'date_approved',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'percentage' => 'decimal:2',
        'rate' => 'decimal:2',
        'limit_per_month' => 'decimal:2',
        'limit_per_year' => 'decimal:2',
        'is_taxable' => 'boolean',
        'is_pensionable' => 'boolean',
        'is_recurring' => 'boolean',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'approved_at' => 'datetime',
        'payroll_year' => 'integer',
        'payroll_month' => 'integer',
        'units' => 'integer',
    ];

    protected $appends = [
        'calculated_amount',
        'is_expired',
        'is_effective',
        'formatted_amount',
        'formatted_calculated_amount',
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
            ->setDescriptionForEvent(fn(string $eventName) => "Employee earning {$eventName}");
    }

    /**
     * Relationships
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function payrollEarningType()
    {
        return $this->belongsTo(PayrollEarningTypes::class, 'payroll_earning_type_id');
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

    public function financialYear()
    {
        return $this->belongsTo(FinancialYear::class, 'financial_year_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
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

    public function scopeTaxable($query)
    {
        return $query->where('is_taxable', true);
    }

    public function scopePensionable($query)
    {
        return $query->where('is_pensionable', true);
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
        return $query->whereNotNull('approved_at');
    }

    public function scopePendingApproval($query)
    {
        return $query->whereNull('approved_at');
    }

    /**
     * Accessors & Mutators
     */
    public function getCalculatedAmountAttribute()
    {
        return $this->calculateEarningAmount();
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
        return number_format($this->amount, 2);
    }

    public function getFormattedCalculatedAmountAttribute()
    {
        return number_format($this->calculated_amount, 2);
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
    public function calculateEarningAmount($basicSalary = null, $grossSalary = null)
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

        return $amount;
    }

    /**
     * Get working days in the current month
     * Can be customized based on company policy
     */
    public function getWorkingDaysInMonth()
    {
        // Default working days per month (can be made configurable)
        return 22;
    }



    public function getYearlyTotal()
    {
        return static::where('employee_id', $this->employee_id)
            ->where('payroll_earning_type_id', $this->payroll_earning_type_id)
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
            'status' => GeneralStatus::ACTIVE,
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
    public static function getActiveEarningsForEmployee($employeeId, $year = null, $month = null)
    {
        $query = static::active()
            ->forEmployee($employeeId)
            ->with(['payrollEarningType', 'employee']);

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

    public static function calculateTotalEarningsForEmployee($employeeId, $year, $month)
    {
        return static::getActiveEarningsForEmployee($employeeId, $year, $month)
            ->sum(fn($earning) => $earning->calculated_amount);
    }

    /**
     * Validation rules
     */
    public static function rules($id = null)
    {
        return [
            'employee_id' => 'required|exists:employees,employee_id',
            'payroll_earning_type_id' => 'required|exists:payroll_earning_types,id',
            'calculation_type' => 'required|in:' . implode(',', CalculationTypes::toArray()),
            'amount' => 'required_if:calculation_type,fixed_amount|numeric|min:0',
            'percentage' => 'required_if:calculation_type,percentage_of_basic,percentage_of_gross|numeric|between:0,100',
            'rate' => 'required_if:calculation_type,daily_rate|numeric|min:0',
            'units' => 'required_if:calculation_type,daily_rate|integer|min:1',
            'limit_per_month' => 'nullable|numeric|min:0',
            'limit_per_year' => 'nullable|numeric|min:0',
            'is_taxable' => 'boolean',
            'is_pensionable' => 'boolean',
            'is_recurring' => 'boolean',
            'frequency' => 'required_if:is_recurring,true|in:' . implode(',', PaymentFrequency::toArray()),
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'payroll_year' => 'required|integer|min:2000|max:2100',
            'payroll_month' => 'required|integer|min:1|max:12',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function allowanceType()
    {
        return $this->belongsTo(AllowanceType::class);
    }

    public function getApprovalDetails(): array
    {
        return [
            'Employee' => $this->employee->fullName() ?? 'N/A',
            'Employee ID' => $this->employee->employee_id ?? 'N/A',
            'Earning Type' => $this->payrollEarningType->name ?? 'N/A',
            'Earning Category' => $this->earning_category ?? 'N/A',
            'Calculation Type' => $this->calculation_type_name ?? 'N/A',
            'Amount' => number_format($this->amount, 2),
            'Percentage' => $this->percentage ? $this->percentage . '%' : 'N/A',
            'Rate' => $this->rate ? number_format($this->rate, 2) : 'N/A',
            'Units' => $this->units ?? 'N/A',
            'Calculated Amount' => number_format($this->calculated_amount, 2),
            'Frequency' => $this->frequency_name ?? 'N/A',
            'Is Recurring' => $this->is_recurring ? 'Yes' : 'No',
            'Is Taxable' => $this->is_taxable ? 'Yes' : 'No',
            'Is Pensionable' => $this->is_pensionable ? 'Yes' : 'No',
            'Monthly Limit' => $this->limit_per_month ? number_format($this->limit_per_month, 2) : 'No Limit',
            'Yearly Limit' => $this->limit_per_year ? number_format($this->limit_per_year, 2) : 'No Limit',
            'Effective From' => $this->effective_from->format('Y-m-d'),
            'Effective To' => $this->effective_to?->format('Y-m-d') ?? 'No End Date',
            'Payroll Period' => $this->payroll_year && $this->payroll_month ?
                Carbon::create()->month($this->payroll_month)->year($this->payroll_year)->format('F Y') : 'N/A',
            'Status' => $this->status == GeneralStatus::ACTIVE ? 'Active' : 'Inactive',
            'Description' => $this->description ?? 'No description',

        ];
    }
}
