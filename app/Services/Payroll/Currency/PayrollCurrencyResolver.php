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
        $periodLabel = $period->name ?? (string) $period->id;

        $salaryToBaseRate = 1.0;
        $salaryToBaseRateRecord = null;
        $requiresSalaryConversion = strtoupper($salaryCurrency) !== strtoupper($baseCurrency);

        if ($requiresSalaryConversion) {
            $salaryToBaseRateRecord = $this->exchangeRateService->getRate(
                $salaryCurrency,
                $baseCurrency,
                $period,
                $company?->id
            );

            if (!$salaryToBaseRateRecord) {
                throw new RuntimeException(sprintf(
                    'No exchange rate from %s to %s configured for payroll period "%s" (employee %s). Add the rate under Payroll → Setup → Exchange Rates.',
                    $salaryCurrency,
                    $baseCurrency,
                    $periodLabel,
                    $employeePayroll->payroll_number ?? $employeePayroll->employee_id
                ));
            }

            $salaryToBaseRate = (float) $salaryToBaseRateRecord->rate;
        }

        $baseToPaymentRate = 1.0;
        $baseToPaymentRateRecord = null;
        $requiresPaymentConversion = strtoupper($paymentCurrency) !== strtoupper($baseCurrency);

        if ($requiresPaymentConversion) {
            $baseToPaymentRateRecord = $this->exchangeRateService->getRate(
                $baseCurrency,
                $paymentCurrency,
                $period,
                $company?->id
            );

            if (!$baseToPaymentRateRecord) {
                throw new RuntimeException(sprintf(
                    'No exchange rate from %s to %s configured for payroll period "%s" (employee %s). Add the rate under Payroll → Setup → Exchange Rates.',
                    $baseCurrency,
                    $paymentCurrency,
                    $periodLabel,
                    $employeePayroll->payroll_number ?? $employeePayroll->employee_id
                ));
            }

            $baseToPaymentRate = (float) $baseToPaymentRateRecord->rate;
        }

        return new PayrollCurrencyContext(
            baseCurrency: strtoupper($baseCurrency),
            salaryCurrency: strtoupper($salaryCurrency),
            paymentCurrency: strtoupper($paymentCurrency),
            salaryToBaseRate: $salaryToBaseRate,
            baseToPaymentRate: $baseToPaymentRate,
            salaryToBaseRateRecord: $salaryToBaseRateRecord,
            baseToPaymentRateRecord: $baseToPaymentRateRecord,
            requiresSalaryConversion: $requiresSalaryConversion,
            requiresPaymentConversion: $requiresPaymentConversion,
            exchangeRateDate: $this->exchangeRateService->periodSnapshotDate($period),
            payrollPeriodName: $periodLabel,
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
