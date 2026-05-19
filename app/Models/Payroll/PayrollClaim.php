<?php

namespace App\Models\Payroll;

use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;

class PayrollClaim extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'payroll_claims';

    protected $fillable = [
        'employee_id',
        'claim_type',
        'claim_title',
        'description',
        'claim_amount',
        'currency',
        'claim_year',
        'claim_month',
        'recovery_method',
        'recovery_periods',
        'recovery_amount_per_period',
        'recovery_start_year',
        'recovery_start_month',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'amount_paid',
        'amount_recovered',
        'paid_at',
        'payment_reference',
        'attachments',
        'reference_number',
        'effective_date',
        'recovery_completion_date',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'claim_amount' => 'decimal:2',
        'recovery_amount_per_period' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_recovered' => 'decimal:2',
        'claim_year' => 'integer',
        'claim_month' => 'integer',
        'recovery_periods' => 'integer',
        'recovery_start_year' => 'integer',
        'recovery_start_month' => 'integer',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'effective_date' => 'date',
        'recovery_completion_date' => 'date',
        'attachments' => 'array'
    ];

    protected $appends = [
        'remaining_balance',
        'recovery_percentage',
        'is_fully_recovered',
        'next_recovery_date',
        'formatted_claim_amount',
        'formatted_amount_recovered',
        'status_label'
    ];

    // Claim types - representing debts/liabilities employee owes to organization
    const CLAIM_TYPES = [
        'salary_advance' => 'Salary Advance Recovery',
        'overpayment' => 'Salary Overpayment Recovery',
        'loan_repayment' => 'Staff Loan Repayment',
        'equipment_damage' => 'Equipment Damage/Loss',
        'training_bond' => 'Training Bond Recovery',
        'disciplinary_fine' => 'Disciplinary Fine',
        'advance_repayment' => 'Cash Advance Repayment',
        'uniform_cost' => 'Uniform/PPE Cost Recovery',
        'canteen_charges' => 'Canteen/Mess Charges',
        'accommodation_charges' => 'Staff Accommodation Charges',
        'transport_recovery' => 'Transport Cost Recovery',
        'other_debt' => 'Other Debt/Liability'
    ];

    // Status options - representing debt recovery status
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING_APPROVAL = 'pending_approval';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_ACTIVE = 'active'; // Active recovery in progress
    const STATUS_PARTIALLY_RECOVERED = 'partially_recovered';
    const STATUS_FULLY_RECOVERED = 'fully_recovered';
    const STATUS_CANCELLED = 'cancelled';

    // Recovery methods
    const RECOVERY_LUMP_SUM = 'lump_sum';
    const RECOVERY_INSTALLMENTS = 'installments';

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Payroll claim {$eventName}");
    }

    /**
     * Relationships
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
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

    public function recoveries()
    {
        return $this->hasMany(PayrollClaimRecovery::class);
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

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByClaimType($query, $type)
    {
        return $query->where('claim_type', $type);
    }

    public function scopeForPeriod($query, $year, $month = null)
    {
        $query->where('claim_year', $year);
        if ($month) {
            $query->where('claim_month', $month);
        }
        return $query;
    }

    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at')
                    ->where('status', self::STATUS_APPROVED);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', self::STATUS_PENDING_APPROVAL);
    }

    public function scopeRequiringRecovery($query)
    {
        return $query->whereIn('status', [
            self::STATUS_ACTIVE,
            self::STATUS_PARTIALLY_RECOVERED
        ])->where('amount_recovered', '<', DB::raw('claim_amount'));
    }

    /**
     * Accessors
     */
    public function getRemainingBalanceAttribute()
    {
        return max(0, $this->claim_amount - $this->amount_recovered);
    }

    public function getRecoveryPercentageAttribute()
    {
        if ($this->claim_amount == 0) return 0;
        return round(($this->amount_recovered / $this->claim_amount) * 100, 2);
    }

    public function getIsFullyRecoveredAttribute()
    {
        return $this->amount_recovered >= $this->claim_amount;
    }

    public function getNextRecoveryDateAttribute()
    {
        if ($this->recovery_method === self::RECOVERY_LUMP_SUM || $this->is_fully_recovered) {
            return null;
        }

        $nextRecovery = $this->recoveries()
            ->where('status', 'pending')
            ->orderBy('recovery_year')
            ->orderBy('recovery_month')
            ->first();

        if ($nextRecovery) {
            return Carbon::createFromDate($nextRecovery->recovery_year, $nextRecovery->recovery_month, 1);
        }

        return null;
    }

    public function getFormattedClaimAmountAttribute()
    {
        return number_format($this->claim_amount, 2);
    }

    public function getFormattedAmountRecoveredAttribute()
    {
        return number_format($this->amount_recovered, 2);
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING_APPROVAL => 'Pending Approval',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_ACTIVE => 'Active Recovery',
            self::STATUS_PARTIALLY_RECOVERED => 'Partially Recovered',
            self::STATUS_FULLY_RECOVERED => 'Fully Recovered',
            self::STATUS_CANCELLED => 'Cancelled',
            default => 'Unknown'
        };
    }

    /**
     * Business Logic Methods
     */
    public function generateReferenceNumber()
    {
        $prefix = 'PC';
        $year = date('Y');
        $month = date('m');

        $lastClaim = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastClaim ? (intval(substr($lastClaim->reference_number, -4)) + 1) : 1;

        return $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function approve(User $approver, string $notes = null)
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'approval_notes' => $notes
        ]);

        return $this;
    }

    public function reject(User $rejector, string $notes = null)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'approved_by' => $rejector->id,
            'approved_at' => now(),
            'approval_notes' => $notes
        ]);

        return $this;
    }

    public function activateRecovery(string $activationReference = null)
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'effective_date' => now(),
            'payment_reference' => $activationReference
        ]);

        // Generate recovery schedule if installments
        if ($this->recovery_method === self::RECOVERY_INSTALLMENTS) {
            $this->generateRecoverySchedule();
        }

        return $this;
    }

    public function generateRecoverySchedule()
    {
        if ($this->recovery_method !== self::RECOVERY_INSTALLMENTS || !$this->recovery_periods) {
            return;
        }

        // Clear existing recovery schedule
        $this->recoveries()->delete();

        $startYear = $this->recovery_start_year ?: now()->year;
        $startMonth = $this->recovery_start_month ?: now()->month;
        $recoveryAmount = $this->recovery_amount_per_period ?: ($this->claim_amount / $this->recovery_periods);

        for ($i = 1; $i <= $this->recovery_periods; $i++) {
            $recoveryDate = Carbon::createFromDate($startYear, $startMonth, 1)->addMonths($i - 1);
            
            // Adjust last installment to cover any rounding differences
            $scheduledAmount = ($i === $this->recovery_periods) 
                ? ($this->claim_amount - ($recoveryAmount * ($this->recovery_periods - 1)))
                : $recoveryAmount;

            PayrollClaimRecovery::create([
                'payroll_claim_id' => $this->id,
                'employee_id' => $this->employee_id,
                'recovery_year' => $recoveryDate->year,
                'recovery_month' => $recoveryDate->month,
                'installment_number' => $i,
                'scheduled_amount' => $scheduledAmount,
                'balance_amount' => $this->claim_amount - ($scheduledAmount * $i),
                'status' => 'pending',
                'created_by' => auth()->id() ?? $this->created_by
            ]);
        }
    }

    public function processRecovery($recoveryYear, $recoveryMonth, $actualAmount)
    {
        $recovery = $this->recoveries()
            ->where('recovery_year', $recoveryYear)
            ->where('recovery_month', $recoveryMonth)
            ->first();

        if (!$recovery) {
            throw new \Exception('Recovery record not found for specified period');
        }

        $recovery->update([
            'actual_amount' => $actualAmount,
            'status' => 'processed',
            'processed_at' => now()
        ]);

        // Update claim recovery amount
        $this->increment('amount_recovered', $actualAmount);

        // Update status based on recovery progress
        if ($this->is_fully_recovered) {
            $this->update([
                'status' => self::STATUS_FULLY_RECOVERED,
                'recovery_completion_date' => now()
            ]);
        } else {
            $this->update(['status' => self::STATUS_PARTIALLY_RECOVERED]);
        }

        return $this;
    }

    public function cancel(string $reason = null)
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'approval_notes' => $reason
        ]);

        // Cancel pending recoveries
        $this->recoveries()->where('status', 'pending')->update(['status' => 'cancelled']);

        return $this;
    }

    /**
     * Static helper methods
     */
    public static function getClaimTypesArray()
    {
        return self::CLAIM_TYPES;
    }

    public static function getStatusArray()
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING_APPROVAL => 'Pending Approval',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_ACTIVE => 'Active Recovery',
            self::STATUS_PARTIALLY_RECOVERED => 'Partially Recovered',
            self::STATUS_FULLY_RECOVERED => 'Fully Recovered',
            self::STATUS_CANCELLED => 'Cancelled'
        ];
    }

    public static function getRecoveryMethodsArray()
    {
        return [
            self::RECOVERY_LUMP_SUM => 'Lump Sum',
            self::RECOVERY_INSTALLMENTS => 'Installments'
        ];
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($claim) {
            if (empty($claim->reference_number)) {
                $claim->reference_number = $claim->generateReferenceNumber();
            }
        });
    }
}