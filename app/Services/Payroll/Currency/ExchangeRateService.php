<?php

namespace App\Services\Payroll\Currency;

use App\Lib\Enumerations\ExchangeRateStatus;
use App\Models\Company;
use App\Models\Payroll\CurrencyExchangeRate;
use App\Models\Payroll\PayrollPeriod;
use Carbon\Carbon;

class ExchangeRateService
{
    public function getRate(
        string $fromCurrency,
        string $toCurrency,
        PayrollPeriod $period,
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
            ->where('payroll_period_id', $period->id)
            ->orderByRaw("CASE WHEN status = ? THEN 0 WHEN status = ? THEN 1 ELSE 2 END", [
                ExchangeRateStatus::ACTIVE,
                ExchangeRateStatus::LOCKED,
            ])
            ->orderByDesc('id');

        if ($companyId) {
            $query->where(function ($builder) use ($companyId) {
                $builder->where('company_id', $companyId)
                    ->orWhereNull('company_id');
            });
        }

        return $query->first();
    }

    public function lockRatesForPayrollRecord(CurrencyExchangeRate ...$rates): void
    {
        foreach ($rates as $rate) {
            if ($rate && $rate->status !== \App\Lib\Enumerations\ExchangeRateStatus::LOCKED) {
                $rate->update(['status' => \App\Lib\Enumerations\ExchangeRateStatus::LOCKED]);
            }
        }
    }

    public function validateRatesForPeriod(array $employeePayrolls, PayrollPeriod $period, ?Company $company = null): array
    {
        $resolver = app(PayrollCurrencyResolver::class);
        $missing = [];

        foreach ($employeePayrolls as $employeePayroll) {
            try {
                $resolver->resolve($employeePayroll, $period);
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

    public function periodSnapshotDate(PayrollPeriod $period): string
    {
        return $this->formatDate($period->end_date ?? $period->input_period_end);
    }

    protected function formatDate($date): string
    {
        if ($date instanceof Carbon) {
            return $date->format('Y-m-d');
        }

        return Carbon::parse($date)->format('Y-m-d');
    }
}
