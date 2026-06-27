<?php

namespace App\Services\Payroll\Concerns;

use App\Models\Payroll\EmployeePayroll;
use App\Models\Payroll\PayrollPeriod;
use App\Models\Payroll\PayrollRecord;
use App\Services\Payroll\Currency\CurrencyConversionService;
use App\Services\Payroll\Currency\ExchangeRateService;
use App\Services\Payroll\Currency\PayrollCurrencyContext;
use App\Services\Payroll\Currency\PayrollCurrencyResolver;

trait HandlesPayrollMultiCurrency
{
    protected ?PayrollCurrencyContext $payrollCurrencyContext = null;

    protected function initializePayrollCurrencyContext(
        EmployeePayroll $employeePayroll,
        PayrollPeriod $period
    ): PayrollCurrencyContext {
        $this->payrollCurrencyContext = app(PayrollCurrencyResolver::class)
            ->resolve($employeePayroll, $period);

        return $this->payrollCurrencyContext;
    }

    protected function payrollCurrencyContext(): ?PayrollCurrencyContext
    {
        return $this->payrollCurrencyContext;
    }

    protected function preparePayrollForStatutoryCalculation(EmployeePayroll $employeePayroll): EmployeePayroll
    {
        if (!$this->payrollCurrencyContext) {
            return $employeePayroll;
        }

        return app(CurrencyConversionService::class)
            ->prepareEmployeePayrollForStatutoryCalculation($employeePayroll, $this->payrollCurrencyContext);
    }

    /**
     * @param array<int, array<string, mixed>> $earnings
     * @return array<int, array<string, mixed>>
     */
    protected function convertPayrollEarningsToBaseCurrency(array $earnings): array
    {
        if (!$this->payrollCurrencyContext) {
            return $earnings;
        }

        return app(CurrencyConversionService::class)
            ->convertEarningsToBaseCurrency($earnings, $this->payrollCurrencyContext);
    }

    protected function finalizePayrollRecordCurrency(
        PayrollRecord $payrollRecord,
        float $taxableIncome
    ): PayrollRecord {
        if (!$this->payrollCurrencyContext) {
            return $payrollRecord;
        }

        return app(CurrencyConversionService::class)
            ->applyPaymentCurrencyToRecord($payrollRecord, $this->payrollCurrencyContext, $taxableIncome);
    }

    protected function enrichPayrollMetadataWithCurrency(array $metadata): array
    {
        if (!$this->payrollCurrencyContext || !$this->payrollCurrencyContext->isMultiCurrency()) {
            return $metadata;
        }

        $metadata['currency'] = [
            'base_currency' => $this->payrollCurrencyContext->baseCurrency,
            'salary_currency' => $this->payrollCurrencyContext->salaryCurrency,
            'payment_currency' => $this->payrollCurrencyContext->paymentCurrency,
            'salary_to_base_rate' => $this->payrollCurrencyContext->salaryToBaseRate,
            'base_to_payment_rate' => $this->payrollCurrencyContext->baseToPaymentRate,
            'exchange_rate_date' => $this->payrollCurrencyContext->exchangeRateDate,
        ];

        return $metadata;
    }

    protected function lockExchangeRatesForProcessedPayroll(): void
    {
        if (!$this->payrollCurrencyContext) {
            return;
        }

        $records = array_filter([
            $this->payrollCurrencyContext->salaryToBaseRateRecord,
            $this->payrollCurrencyContext->baseToPaymentRateRecord,
        ]);

        if (!empty($records)) {
            app(ExchangeRateService::class)->lockRatesForPayrollRecord(...$records);
        }
    }
}
