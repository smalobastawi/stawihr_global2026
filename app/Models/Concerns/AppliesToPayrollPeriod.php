<?php

namespace App\Models\Concerns;

use App\Lib\Enumerations\PaymentFrequency;
use App\Models\Payroll\PayrollPeriod;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait AppliesToPayrollPeriod
{
    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function endPayrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class, 'end_payroll_period_id');
    }

    public function isOneTimeForPayroll(): bool
    {
        return !$this->is_recurring || $this->frequency === PaymentFrequency::ONE_TIME;
    }

    public function appliesToPayrollPeriod(PayrollPeriod $period): bool
    {
        if ($this->payroll_period_id) {
            return $this->appliesUsingPayrollPeriodFields($period);
        }

        return $this->appliesUsingLegacyEffectiveDates($period);
    }

    public function scopeApplicableForPayrollPeriod(Builder $query, PayrollPeriod $period): Builder
    {
        $periodStart = ($period->input_period_start ?? $period->start_date)->format('Y-m-d');
        $periodEnd = ($period->input_period_end ?? $period->end_date)->format('Y-m-d');

        return $query->where(function (Builder $builder) use ($period, $periodStart, $periodEnd) {
            $builder->where(function (Builder $periodBased) use ($period) {
                $periodBased->whereNotNull('payroll_period_id')
                    ->where(function (Builder $inner) use ($period) {
                        $inner->where(function (Builder $oneTime) use ($period) {
                            $oneTime->where(function (Builder $flags) {
                                $flags->where('is_recurring', false)
                                    ->orWhere('frequency', PaymentFrequency::ONE_TIME);
                            })->where('payroll_period_id', $period->id);
                        })->orWhere(function (Builder $recurring) use ($period) {
                            $recurring->where('is_recurring', true)
                                ->where('frequency', '!=', PaymentFrequency::ONE_TIME)
                                ->whereHas('payrollPeriod', function (Builder $startQuery) use ($period) {
                                    $startQuery->whereDate('start_date', '<=', ($period->start_date ?? $period->input_period_start)->format('Y-m-d'));
                                })
                                ->where(function (Builder $endQuery) use ($period) {
                                    $endQuery->whereNull('end_payroll_period_id')
                                        ->orWhereHas('endPayrollPeriod', function (Builder $endPeriodQuery) use ($period) {
                                            $endPeriodQuery->whereDate('start_date', '>=', ($period->start_date ?? $period->input_period_start)->format('Y-m-d'));
                                        });
                                });
                        });
                    });
            })->orWhere(function (Builder $legacy) use ($periodStart, $periodEnd) {
                $legacy->whereNull('payroll_period_id')
                    ->where('effective_from', '<=', $periodEnd)
                    ->where(function (Builder $subQ) use ($periodStart) {
                        $subQ->whereNull('effective_to')
                            ->orWhere('effective_to', '>=', $periodStart);
                    });
            });
        });
    }

    public static function syncPayrollPeriodInput(array $input): array
    {
        if (empty($input['payroll_period_id'])) {
            return $input;
        }

        $startPeriod = PayrollPeriod::findOrFail($input['payroll_period_id']);
        $startDate = $startPeriod->input_period_start ?? $startPeriod->start_date;
        $endDate = $startPeriod->input_period_end ?? $startPeriod->end_date;

        $input['payroll_year'] = $startDate->year;
        $input['payroll_month'] = $startPeriod->month_number ?? $startDate->month;

        if (!empty($input['financial_year_id'])) {
            // keep submitted financial year
        } elseif (function_exists('getActiveFinancialYear')) {
            $activeFinancialYear = getActiveFinancialYear();
            if ($activeFinancialYear) {
                $input['financial_year_id'] = $activeFinancialYear->id;
            }
        }

        $isRecurring = !empty($input['is_recurring']) && ($input['frequency'] ?? null) !== PaymentFrequency::ONE_TIME;

        if ($isRecurring && !empty($input['end_payroll_period_id'])) {
            $endPeriod = PayrollPeriod::findOrFail($input['end_payroll_period_id']);
            $input['effective_from'] = $startDate->format('Y-m-d');
            $input['effective_to'] = ($endPeriod->input_period_end ?? $endPeriod->end_date)->format('Y-m-d');
        } else {
            $input['end_payroll_period_id'] = null;
            $input['effective_from'] = $startDate->format('Y-m-d');
            $input['effective_to'] = $endDate->format('Y-m-d');
        }

        return $input;
    }

    protected function appliesUsingPayrollPeriodFields(PayrollPeriod $period): bool
    {
        $startPeriod = $this->payrollPeriod;
        if (!$startPeriod) {
            return false;
        }

        $currentStart = Carbon::parse($period->input_period_start ?? $period->start_date)->startOfDay();
        $rangeStart = Carbon::parse($startPeriod->input_period_start ?? $startPeriod->start_date)->startOfDay();

        if ($this->isOneTimeForPayroll()) {
            return (int) $this->payroll_period_id === (int) $period->id;
        }

        if ($currentStart->lt($rangeStart)) {
            return false;
        }

        if ($this->end_payroll_period_id && $this->endPayrollPeriod) {
            $rangeEnd = Carbon::parse(
                $this->endPayrollPeriod->input_period_start ?? $this->endPayrollPeriod->start_date
            )->startOfDay();

            if ($currentStart->gt($rangeEnd)) {
                return false;
            }
        }

        return $this->matchesFrequencyForPayrollPeriod($period, $startPeriod);
    }

    protected function matchesFrequencyForPayrollPeriod(PayrollPeriod $period, PayrollPeriod $startPeriod): bool
    {
        $periodDate = Carbon::parse($period->input_period_start ?? $period->start_date)->startOfDay();
        $effectiveFrom = Carbon::parse($startPeriod->input_period_start ?? $startPeriod->start_date)->startOfDay();

        return match ($this->frequency) {
            PaymentFrequency::MONTHLY => true,
            PaymentFrequency::WEEKLY => $periodDate->diffInWeeks($effectiveFrom) % 1 === 0,
            PaymentFrequency::BI_WEEKLY => $periodDate->diffInWeeks($effectiveFrom) % 2 === 0,
            PaymentFrequency::QUARTERLY => $periodDate->diffInMonths($effectiveFrom) % 3 === 0,
            PaymentFrequency::ANNUALLY => $periodDate->diffInYears($effectiveFrom) >= 0
                && $periodDate->month === $effectiveFrom->month,
            PaymentFrequency::ONE_TIME => false,
            default => true,
        };
    }

    protected function appliesUsingLegacyEffectiveDates(PayrollPeriod $period): bool
    {
        if (!$this->effective_from) {
            return false;
        }

        $periodStart = Carbon::parse($period->input_period_start ?? $period->start_date)->startOfDay();
        $periodEnd = Carbon::parse($period->input_period_end ?? $period->end_date)->startOfDay();
        $effectiveFrom = Carbon::parse($this->effective_from)->startOfDay();
        $effectiveTo = $this->effective_to ? Carbon::parse($this->effective_to)->startOfDay() : null;

        if ($effectiveFrom->gt($periodEnd)) {
            return false;
        }

        if ($effectiveTo && $effectiveTo->lt($periodStart)) {
            return false;
        }

        if (!$this->is_recurring || $this->frequency === PaymentFrequency::ONE_TIME) {
            return true;
        }

        return $this->shouldRecurForPeriod($periodStart->year, $periodStart->month);
    }
}
