<?php

namespace App\Services\Payroll\Currency;

use App\Lib\Enumerations\Currency;
use App\Models\Company;
use App\Models\Payroll\EmployeePayroll;
use App\Models\Payroll\PayrollPeriod;
use RuntimeException;

class PayrollCurrencyResolver
{
    public function __construct(
        protected ExchangeRateService $exchangeRateService,
        protected CurrencyConversionService $conversionService,
    ) {
    }

    public function resolve(EmployeePayroll $employeePayroll, PayrollPeriod $period, ?Company $company = null): PayrollCurrencyContext
    {
        $company = $company ?? $this->resolveCompany($employeePayroll);
        $baseCurrency = $this->resolveBaseCurrency($company);
        $salaryCurrency = $this->resolveSalaryCurrency($employeePayroll, $company);
        $paymentCurrency = $this->resolvePaymentCurrency($employeePayroll, $company, $salaryCurrency);

        $effectiveDate = $this->exchangeRateService->resolveEffectiveDate($company, $period);

        $salaryToBaseRate = 1.0;
        $salaryToBaseRecord = null;
        $requiresSalaryConversion = strtoupper($salaryCurrency) !== strtoupper($baseCurrency);

        if ($requiresSalaryConversion) {
            $salaryToBaseRecord = $this->exchangeRateService->getRate(
                $salaryCurrency,
                $baseCurrency,
                $effectiveDate,
                $period,
                $company->id
            );

            if (!$salaryToBaseRecord) {
                throw new RuntimeException(sprintf(
                    'No exchange rate from %s to %s for employee %s (payroll period %s).',
                    $salaryCurrency,
                    $baseCurrency,
                    $employeePayroll->payroll_number ?? $employeePayroll->employee_id,
                    $period->period_name ?? $period->id
                ));
            }

            $salaryToBaseRate = (float) $salaryToBaseRecord->rate;
        }

        $baseToPaymentRate = 1.0;
        $baseToPaymentRecord = null;
        $requiresPaymentConversion = strtoupper($paymentCurrency) !== strtoupper($baseCurrency);

        if ($requiresPaymentConversion) {
            $baseToPaymentRecord = $this->exchangeRateService->getRate(
                $baseCurrency,
                $paymentCurrency,
                $effectiveDate,
                $period,
                $company->id
            );

            if (!$baseToPaymentRecord) {
                throw new RuntimeException(sprintf(
                    'No exchange rate from %s to %s for employee %s (payroll period %s).',
                    $baseCurrency,
                    $paymentCurrency,
                    $employeePayroll->payroll_number ?? $employeePayroll->employee_id,
                    $period->period_name ?? $period->id
                ));
            }

            $baseToPaymentRate = (float) $baseToPaymentRecord->rate;
        }

        return new PayrollCurrencyContext(
            baseCurrency: strtoupper($baseCurrency),
            salaryCurrency: strtoupper($salaryCurrency),
            paymentCurrency: strtoupper($paymentCurrency),
            salaryToBaseRate: $salaryToBaseRate,
            baseToPaymentRate: $baseToPaymentRate,
            salaryToBaseRateRecord: $salaryToBaseRecord,
            baseToPaymentRateRecord: $baseToPaymentRecord,
            requiresSalaryConversion: $requiresSalaryConversion,
            requiresPaymentConversion: $requiresPaymentConversion,
            exchangeRateDate: $effectiveDate,
        );
    }

    public function resolveBaseCurrency(?Company $company): string
    {
        if (!$company) {
            return Currency::DEFAULT;
        }

        return $company->getPayrollBaseCurrency();
    }

    public function resolveSalaryCurrency(EmployeePayroll $employeePayroll, ?Company $company = null): string
    {
        if (!empty($employeePayroll->currency) && Currency::isValid($employeePayroll->currency)) {
            return strtoupper($employeePayroll->currency);
        }

        return $this->resolveBaseCurrency($company ?? $this->resolveCompany($employeePayroll));
    }

    public function resolvePaymentCurrency(EmployeePayroll $employeePayroll, ?Company $company = null, ?string $salaryCurrency = null): string
    {
        $company = $company ?? $this->resolveCompany($employeePayroll);
        $salaryCurrency = $salaryCurrency ?? $this->resolveSalaryCurrency($employeePayroll, $company);

        if (!$company->allow_employee_payment_currency) {
            return $this->resolveBaseCurrency($company);
        }

        if (!empty($employeePayroll->payment_currency) && Currency::isValid($employeePayroll->payment_currency)) {
            return strtoupper($employeePayroll->payment_currency);
        }

        if (!empty($company->default_payment_currency) && Currency::isValid($company->default_payment_currency)) {
            return strtoupper($company->default_payment_currency);
        }

        return $salaryCurrency;
    }

    public function resolveBankPaymentCurrency(EmployeePayroll $employeePayroll, PayrollCurrencyContext $context): string
    {
        if (!empty($employeePayroll->bank_payment_currency) && Currency::isValid($employeePayroll->bank_payment_currency)) {
            return strtoupper($employeePayroll->bank_payment_currency);
        }

        return $context->paymentCurrency;
    }

    protected function resolveCompany(EmployeePayroll $employeePayroll): ?Company
    {
        return $employeePayroll->employee?->company;
    }
}
