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

    protected function enrichPayrollMetadataWithCurrency(
        array $metadata,
        ?float $basicSalaryBase = null,
        ?float $netSalaryBase = null
    ): array {
        if (!$this->payrollCurrencyContext) {
            return $metadata;
        }

        $context = $this->payrollCurrencyContext;
        $conversionService = app(CurrencyConversionService::class);

        $metadata['currency'] = [
            'base_currency' => $context->baseCurrency,
            'salary_currency' => $context->salaryCurrency,
            'payment_currency' => $context->paymentCurrency,
            'salary_to_base_rate' => $context->salaryToBaseRate,
            'base_to_payment_rate' => $context->baseToPaymentRate,
            'exchange_rate_date' => $context->exchangeRateDate,
        ];

        if ($context->requiresSalaryConversion && $basicSalaryBase !== null) {
            $metadata['currency']['basic_salary_base_currency'] = $basicSalaryBase;
            $metadata['currency']['basic_salary_salary_currency'] = $conversionService->roundForCurrency(
                $basicSalaryBase / $context->salaryToBaseRate,
                $context->salaryCurrency
            );
        }

        if ($context->requiresSalaryConversion && $netSalaryBase !== null) {
            $metadata['currency']['net_pay_base_currency'] = $netSalaryBase;
            $metadata['currency']['net_pay_salary_currency'] = $conversionService->roundForCurrency(
                $netSalaryBase / $context->salaryToBaseRate,
                $context->salaryCurrency
            );
        }

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
