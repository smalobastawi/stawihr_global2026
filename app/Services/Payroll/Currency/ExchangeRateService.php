<?php

namespace App\Services\Payroll\Currency;

use App\Lib\Enumerations\Currency;
use App\Lib\Enumerations\ExchangeRateEffectiveDatePolicy;
use App\Models\Company;
use App\Models\Payroll\CurrencyExchangeRate;
use App\Models\Payroll\PayrollPeriod;
use Carbon\Carbon;

class ExchangeRateService
{
    public function resolveEffectiveDate(Company $company, PayrollPeriod $period): string
    {
        $policy = $company->exchange_rate_effective_date_policy
            ?? ExchangeRateEffectiveDatePolicy::PAYROLL_PERIOD_END;

        return match ($policy) {
            ExchangeRateEffectiveDatePolicy::PAYROLL_PERIOD_START => $this->formatDate($period->start_date ?? $period->input_period_start),
            ExchangeRateEffectiveDatePolicy::PAYMENT_DATE => now()->format('Y-m-d'),
            ExchangeRateEffectiveDatePolicy::LATEST_APPROVED,
            ExchangeRateEffectiveDatePolicy::PAYROLL_PERIOD_END => $this->formatDate($period->end_date ?? $period->input_period_end),
            default => $this->formatDate($period->end_date ?? $period->input_period_end),
        };
    }

    public function getRate(
        string $fromCurrency,
        string $toCurrency,
        string $effectiveDate,
        ?PayrollPeriod $period = null,
        ?int $companyId = null
    ): ?CurrencyExchangeRate {
        $from = strtoupper($fromCurrency);
        $to = strtoupper($toCurrency);

        if ($from === $to) {
            return null;
        }

        $query = CurrencyExchangeRate::query()
            ->forPayroll()
            ->forPair($from, $to)
            ->where('effective_date', '<=', $effectiveDate)
            ->orderByDesc('effective_date')
            ->orderByDesc('id');

        if ($companyId) {
            $query->where(function ($builder) use ($companyId) {
                $builder->where('company_id', $companyId)
                    ->orWhereNull('company_id');
            });
        }

        if ($period) {
            $periodSpecific = (clone $query)
                ->where('payroll_period_id', $period->id)
                ->first();

            if ($periodSpecific) {
                return $periodSpecific;
            }
        }

        return $query->whereNull('payroll_period_id')->first();
    }

    public function lockRatesForPayrollRecord(CurrencyExchangeRate ...$rates): void
    {
        foreach ($rates as $rate) {
            if ($rate && $rate->status !== \App\Lib\Enumerations\ExchangeRateStatus::LOCKED) {
                $rate->update(['status' => \App\Lib\Enumerations\ExchangeRateStatus::LOCKED]);
            }
        }
    }

    public function validateRatesForPeriod(array $employeePayrolls, PayrollPeriod $period, Company $company): array
    {
        $resolver = app(PayrollCurrencyResolver::class);
        $missing = [];

        foreach ($employeePayrolls as $employeePayroll) {
            try {
                $resolver->resolve($employeePayroll, $period, $company);
            } catch (\Throwable $e) {
                $employee = $employeePayroll->employee;
                $missing[] = [
                    'employee_id' => $employeePayroll->employee_id,
                    'employee_name' => $employee?->fullName() ?? 'Unknown',
                    'payroll_number' => $employeePayroll->payroll_number,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $missing;
    }

    protected function formatDate($date): string
    {
        if ($date instanceof Carbon) {
            return $date->format('Y-m-d');
        }

        return Carbon::parse($date)->format('Y-m-d');
    }
}
