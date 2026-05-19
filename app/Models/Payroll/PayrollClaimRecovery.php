<?php

namespace App\Models\Payroll;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PayrollClaimRecovery extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'payroll_claim_recoveries';

    protected $fillable = [
        'payroll_claim_id',
        'employee_id',
        'recovery_year',
        'recovery_month',
        'installment_number',
        'scheduled_amount',
        'actual_amount',
        'balance_amount',
        'status',
        'processed_at',
        'payroll_reference',
        'notes',
        'adjustment_amount',
        'adjustment_reason',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'recovery_year' => 'integer',
        'recovery_month' => 'integer',
        'installment_number' => 'integer',
        'scheduled_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'adjustment_amount' => 'decimal:2',
        'processed_at' => 'datetime'
    ];

    protected $appends = [
        'recovery_period_label',
        'variance_amount',
        'formatted_scheduled_amount',
        'formatted_actual_amount',
        'status_label'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSED = 'processed';
    const STATUS_SKIPPED = 'skipped';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Payroll claim recovery {$eventName}");
    }

    /**
     * Relationships
     */
    public function payrollClaim()
    {
        return $this->belongsTo(PayrollClaim::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
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
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeForPeriod($query, $year, $month)
    {
        return $query->where('recovery_year', $year)
                    ->where('recovery_month', $month);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', self::STATUS_PROCESSED);
    }

    public function scopeForPayrollPeriod($query, $year, $month)
    {
        return $query->where('recovery_year', $year)
                    ->where('recovery_month', $month);
    }

    /**
     * Accessors
     */
    public function getRecoveryPeriodLabelAttribute()
    {
        $monthName = date('F', mktime(0, 0, 0, $this->recovery_month, 1));
        return "{$monthName} {$this->recovery_year}";
    }

    public function getVarianceAmountAttribute()
    {
        return $this->actual_amount - $this->scheduled_amount;
    }

    public function getFormattedScheduledAmountAttribute()
    {
        return number_format($this->scheduled_amount, 2);
    }

    public function getFormattedActualAmountAttribute()
    {
        return number_format($this->actual_amount, 2);
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSED => 'Processed',
            self::STATUS_SKIPPED => 'Skipped',
            self::STATUS_CANCELLED => 'Cancelled',
            default => 'Unknown'
        };
    }

    /**
     * Business Logic Methods
     */
    public function process($actualAmount, $payrollReference = null, $notes = null)
    {
        $this->update([
            'actual_amount' => $actualAmount,
            'status' => self::STATUS_PROCESSED,
            'processed_at' => now(),
            'payroll_reference' => $payrollReference,
            'notes' => $notes,
            'updated_by' => auth()->id()
        ]);

        // Update the parent claim's recovery amount
        $this->payrollClaim->increment('amount_recovered', $actualAmount);

        // Check if claim is fully recovered
        if ($this->payrollClaim->is_fully_recovered) {
            $this->payrollClaim->update([
                'status' => PayrollClaim::STATUS_FULLY_RECOVERED,
                'recovery_completion_date' => now()
            ]);
        } else {
            $this->payrollClaim->update([
                'status' => PayrollClaim::STATUS_PARTIALLY_RECOVERED
            ]);
        }

        return $this;
    }

    public function skip($reason = null)
    {
        $this->update([
            'status' => self::STATUS_SKIPPED,
            'notes' => $reason,
            'updated_by' => auth()->id()
        ]);

        return $this;
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'notes' => $reason,
            'updated_by' => auth()->id()
        ]);

        return $this;
    }

    public function adjust($adjustmentAmount, $reason)
    {
        $this->update([
            'adjustment_amount' => $adjustmentAmount,
            'adjustment_reason' => $reason,
            'scheduled_amount' => $this->scheduled_amount + $adjustmentAmount,
            'updated_by' => auth()->id()
        ]);

        return $this;
    }

    /**
     * Static helper methods
     */
    public static function getStatusArray()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSED => 'Processed',
            self::STATUS_SKIPPED => 'Skipped',
            self::STATUS_CANCELLED => 'Cancelled'
        ];
    }

    public static function getPendingRecoveriesForPeriod($year, $month)
    {
        return self::with(['payrollClaim', 'employee'])
            ->forPayrollPeriod($year, $month)
            ->pending()
            ->orderBy('employee_id')
            ->orderBy('installment_number')
            ->get();
    }

    public static function getTotalRecoveriesForPeriod($year, $month)
    {
        return self::forPayrollPeriod($year, $month)
            ->processed()
            ->sum('actual_amount');
    }
}