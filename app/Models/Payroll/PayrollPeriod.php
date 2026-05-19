<?php

namespace App\Models\Payroll;

use App\Traits\HasApprovalWorkflow;
use App\Traits\ProvidesApprovalDetails;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\Activitylog\LogOptions;

class PayrollPeriod extends Model
{
    use HasFactory,  HasApprovalWorkflow, ProvidesApprovalDetails, LogsActivity;

    protected $fillable = [
        'name',
        'period_type',
        'start_date',
        'end_date',
        'pay_date',
        'status',
        'is_current',
        'created_by',
        'updated_by',
        'month_number',
        'week_number',
        'biweekly_number',
        'input_period_start',
        'input_period_end',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'pay_date' => 'date',
        'is_current' => 'boolean',
        'input_period_start' => 'date',
        'input_period_end' => 'date',
    ];
    protected $dates = [
        'start_date',
        'end_date',
        'pay_date',
        'input_period_start',
        'input_period_end',
    ];

    // Period types
    const PERIOD_MONTHLY = 'monthly';
    const PERIOD_WEEKLY = 'weekly';
    const PERIOD_BIWEEKLY = 'bi-weekly';

    const PERIOD_TYPES = [
        self::PERIOD_MONTHLY => 'Monthly',
        self::PERIOD_WEEKLY => 'Weekly',
        self::PERIOD_BIWEEKLY => 'Bi-Weekly'
    ];

    // Status
    const STATUS_OPEN = 'open';
    const STATUS_PROCESSING = 'processing';
    const STATUS_CLOSED = 'closed';

    const STATUSES = [
        self::STATUS_OPEN => 'Open',
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_CLOSED => 'Closed'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            // Calculate the number fields if not provided
            if (empty($model->month_number)) {
                $model->month_number = $model->start_date->month;
            }
            if (empty($model->week_number)) {
                $model->week_number = $model->start_date->weekOfYear;
            }
            if (empty($model->biweekly_number)) {
                $model->biweekly_number = floor(($model->start_date->dayOfYear - 1) / 14) + 1;
            }

            // Set default input dates if not provided
            if (empty($model->input_period_start)) {
                $model->input_period_start = $model->start_date;
            }
            if (empty($model->input_period_end)) {
                $model->input_period_end = $model->end_date;
            }
        });

        static::updating(function ($model) {
            // Recalculate if start date changed
            if ($model->isDirty('start_date')) {
                $model->month_number = $model->start_date->month;
                $model->week_number = $model->start_date->weekOfYear;
                $model->biweekly_number = floor(($model->start_date->dayOfYear - 1) / 14) + 1;
            }
        });
    }

    public function payrollRecords()
    {
        return $this->hasMany(PayrollRecord::class);
    }

    /**
     * Get current payroll period
     */
    public static function getCurrentPeriod()
    {
        return self::where('is_current', true)->first();
    }

    /**
     * Create monthly period with input dates
     */
    public static function createMonthlyPeriod($year, $month, $inputStart = null, $inputEnd = null)
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $payDate = $endDate->copy()->addDays(5); // Pay 5 days after month end

        // Set default input dates if not provided
        if (!$inputStart) {
            $inputStart = $startDate;
        }
        if (!$inputEnd) {
            $inputEnd = $endDate;
        }

        return self::create([
            'name' => $startDate->format('F Y'),
            'period_type' => self::PERIOD_MONTHLY,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'pay_date' => $payDate,
            'input_period_start' => $inputStart,
            'input_period_end' => $inputEnd,
            'status' => self::STATUS_OPEN,
            'is_current' => false,
            'created_by' => auth()->id()
        ]);
    }

    /**
     * Create weekly period with input dates
     */
    public static function createWeeklyPeriod($startDate, $inputStart = null, $inputEnd = null)
    {
        $endDate = $startDate->copy()->addDays(6);
        $payDate = $endDate->copy()->addDays(3); // Pay 3 days after week end

        // Set default input dates if not provided
        if (!$inputStart) {
            $inputStart = $startDate;
        }
        if (!$inputEnd) {
            $inputEnd = $endDate;
        }

        return self::create([
            'name' => 'Week of ' . $startDate->format('M d, Y'),
            'period_type' => self::PERIOD_WEEKLY,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'pay_date' => $payDate,
            'input_period_start' => $inputStart,
            'input_period_end' => $inputEnd,
            'status' => self::STATUS_OPEN,
            'is_current' => false,
            'created_by' => auth()->id()
        ]);
    }

    /**
     * Create bi-weekly period with input dates
     */
    public static function createBiWeeklyPeriod($startDate, $inputStart = null, $inputEnd = null)
    {
        $endDate = $startDate->copy()->addDays(13);
        $payDate = $endDate->copy()->addDays(4); // Pay 4 days after period end

        // Set default input dates if not provided
        if (!$inputStart) {
            $inputStart = $startDate;
        }
        if (!$inputEnd) {
            $inputEnd = $endDate;
        }

        return self::create([
            'name' => 'Bi-weekly period ending ' . $endDate->format('M d, Y'),
            'period_type' => self::PERIOD_BIWEEKLY,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'pay_date' => $payDate,
            'input_period_start' => $inputStart,
            'input_period_end' => $inputEnd,
            'status' => self::STATUS_OPEN,
            'is_current' => false,
            'created_by' => auth()->id()
        ]);
    }

    /**
     * Set as current period
     */
    public function setAsCurrent()
    {
        // Remove current flag from all periods
        self::where('is_current', true)->update(['is_current' => false]);

        // Set this period as current
        $this->update(['is_current' => true]);
    }

    /**
     * Close payroll period and create next period based on input dates
     */
    public function close()
    {
        if ($this->is_current) {
            // Calculate next period based on input period end date
            $nextInputStart = $this->input_period_end->copy()->addDay();
            $nextInputEnd = null;
            $nextStart = null;
            $nextEnd = null;
            $name = null;

            switch ($this->period_type) {
                case self::PERIOD_MONTHLY:
                    $nextStart = $this->end_date->copy()->addDay()->startOfMonth();
                    $nextEnd = $nextStart->copy()->endOfMonth();
                    $nextInputEnd = $nextInputStart->copy()->addMonth()->subDay();
                    $name = $nextStart->format('F Y');
                    break;

                case self::PERIOD_WEEKLY:
                    $nextStart = $this->end_date->copy()->addDay();
                    $nextEnd = $nextStart->copy()->addDays(6);
                    $nextInputEnd = $nextInputStart->copy()->addDays(6);
                    $name = 'Week of ' . $nextStart->format('M d, Y');
                    break;

                case self::PERIOD_BIWEEKLY:
                    $nextStart = $this->end_date->copy()->addDay();
                    $nextEnd = $nextStart->copy()->addDays(13);
                    $nextInputEnd = $nextInputStart->copy()->addDays(13);
                    $name = 'Bi-weekly period ending ' . $nextEnd->format('M d, Y');
                    break;
            }

            $existing = self::where('start_date', $nextStart)
                ->where('end_date', $nextEnd)
                ->first();

            if ($existing) {
                if ($existing->status !== self::STATUS_OPEN) {
                    $existing->update([
                        'status' => self::STATUS_OPEN,
                        'input_period_start' => $nextInputStart,
                        'input_period_end' => $nextInputEnd
                    ]);
                }
                $existing->setAsCurrent();
            } else {
                $periodData = [
                    'name' => $name,
                    'period_type' => $this->period_type,
                    'start_date' => $nextStart,
                    'end_date' => $nextEnd,
                    'pay_date' => $nextEnd->copy()->subDays(5),
                    'input_period_start' => $nextInputStart,
                    'input_period_end' => $nextInputEnd,
                    'status' => self::STATUS_OPEN,
                    'is_current' => false,
                    'created_by' => auth()->id(),
                ];

                $newPeriod = self::create($periodData);
                $newPeriod->setAsCurrent();
            }
        }

        $this->update(['status' => self::STATUS_CLOSED]);
    }

    /**
     * Manually create next period with custom input dates
     */
    public function createNextPeriod($inputStart, $inputEnd)
    {
        $nextStart = null;
        $nextEnd = null;
        $name = null;

        switch ($this->period_type) {
            case self::PERIOD_MONTHLY:
                $nextStart = $this->end_date->copy()->addDay()->startOfMonth();
                $nextEnd = $nextStart->copy()->endOfMonth();
                $name = $nextStart->format('F Y');
                break;

            case self::PERIOD_WEEKLY:
                $nextStart = $this->end_date->copy()->addDay();
                $nextEnd = $nextStart->copy()->addDays(6);
                $name = 'Week of ' . $nextStart->format('M d, Y');
                break;

            case self::PERIOD_BIWEEKLY:
                $nextStart = $this->end_date->copy()->addDay();
                $nextEnd = $nextStart->copy()->addDays(13);
                $name = 'Bi-weekly period ending ' . $nextEnd->format('M d, Y');
                break;
        }
        $payDate = Carbon::create($inputEnd->year, $inputEnd->month - 1, 26);

        $periodData = [
            'name' => $name,
            'period_type' => $this->period_type,
            'start_date' => $nextStart,
            'end_date' => $nextEnd,
            'pay_date' => $payDate,
            'input_period_start' => $inputStart,
            'input_period_end' => $inputEnd,
            'status' => self::STATUS_OPEN,
            'is_current' => false,
            'created_by' => auth()->id(),
        ];

        return self::create($periodData);
    }

    /**
     * Check if period can be processed
     */
    public function canBeProcessed()
    {
        return $this->status === self::STATUS_OPEN;
    }

    /**
     * Check if period is closed
     */
    public function isClosed()
    {
        return $this->status === self::STATUS_CLOSED;
    }

    /**
     * Get period summary
     */
    public function getSummary()
    {
        $records = $this->payrollRecords();

        return [
            'total_employees' => $records->count(),
            'total_gross_salary' => $records->sum('gross_salary'),
            'total_deductions' => $records->sum('total_deductions'),
            'total_net_salary' => $records->sum('net_salary'),
            'total_paye' => $records->sum('paye_tax'),
            'total_nssf' => $records->sum('nssf_contribution'),
            'total_shif' => $records->sum('shif_contribution'),
            'total_housing_levy' => $records->sum('housing_levy'),
            'paid_count' => $records->where('status', PayrollRecord::STATUS_PAID)->count(),
            'unpaid_count' => $records->whereIn('status', [
                PayrollRecord::STATUS_DRAFT,
                PayrollRecord::STATUS_CALCULATED,
                PayrollRecord::STATUS_APPROVED
            ])->count()
        ];
    }

    /**
     * Scope for open periods
     */
    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    /**
     * Scope for current period
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Get input period duration in days
     */
    public function getInputPeriodDurationAttribute()
    {
        return $this->input_period_start->diffInDays($this->input_period_end) + 1;
    }

    /**
     * Check if current date is within input period
     */
    public function isWithinInputPeriod()
    {
        $now = Carbon::now();
        return $now->between($this->input_period_start, $this->input_period_end);
    }

    /**
     * Get days remaining in input period
     */
    public function getInputDaysRemainingAttribute()
    {
        $now = Carbon::now();
        if ($now->gt($this->input_period_end)) {
            return 0;
        }
        return $now->diffInDays($this->input_period_end) + 1;
    }

    public function getMonthNumberAttribute()
    {
        return $this->start_date->month;
    }

    public function getYearNumberAttribute()
    {
        return $this->start_date->year;
    }
    public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
    }
}
