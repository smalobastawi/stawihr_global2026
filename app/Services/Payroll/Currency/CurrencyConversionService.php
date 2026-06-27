<?php

namespace App\Services\Payroll\Currency;

use App\Lib\Enumerations\Currency;
use App\Models\Payroll\EmployeePayroll;
use App\Models\Payroll\PayrollRecord;

class CurrencyConversionService
{
    /** @var array<string, int> */
    protected static array $decimalPlaces = [
        'BIF' => 0,
        'CLP' => 0,
        'DJF' => 0,
        'GNF' => 0,
        'ISK' => 0,
        'JPY' => 0,
        'KMF' => 0,
        'KRW' => 0,
        'PYG' => 0,
        'RWF' => 0,
        'UGX' => 0,
        'VND' => 0,
        'VUV' => 0,
        'XAF' => 0,
        'XOF' => 0,
        'XPF' => 0,
        'BHD' => 3,
        'IQD' => 3,
        'JOD' => 3,
        'KWD' => 3,
        'LYD' => 3,
        'OMR' => 3,
        'TND' => 3,
    ];

    public function convert(float $amount, float $rate, ?string $targetCurrency = null): float
    {
        $converted = $amount * $rate;

        if ($targetCurrency) {
            return $this->roundForCurrency($converted, $targetCurrency);
        }

        return round($converted, 2);
    }

    public function roundForCurrency(float $amount, string $currency): float
    {
        $decimals = $this->decimalPlacesFor($currency);

        return round($amount, $decimals);
    }

    public function decimalPlacesFor(string $currency): int
    {
        $code = strtoupper($currency);

        return self::$decimalPlaces[$code] ?? 2;
    }

    public function format(float $amount, string $currency, bool $includeCode = true): string
    {
        $formatted = number_format($amount, $this->decimalPlacesFor($currency));

        return $includeCode ? "{$formatted} {$currency}" : $formatted;
    }

    /**
     * Prepare employee payroll inputs expressed in base currency for statutory calculation.
     */
    public function prepareEmployeePayrollForStatutoryCalculation(
        EmployeePayroll $employeePayroll,
        PayrollCurrencyContext $context
    ): EmployeePayroll {
        if (!$context->requiresSalaryConversion) {
            return $employeePayroll;
        }

        $prepared = clone $employeePayroll;
        $prepared->basic_salary = $this->convert(
            (float) $employeePayroll->basic_salary,
            $context->salaryToBaseRate,
            $context->baseCurrency
        );

        return $prepared;
    }

    /**
     * Convert fixed-amount earnings that were entered in salary currency.
     *
     * @param array<int, array<string, mixed>> $earnings
     * @return array<int, array<string, mixed>>
     */
    public function convertEarningsToBaseCurrency(array $earnings, PayrollCurrencyContext $context): array
    {
        if (!$context->requiresSalaryConversion) {
            return $earnings;
        }

        return array_map(function (array $earning) use ($context) {
            $calculationType = $earning['metadata']['calculation_type'] ?? null;

            if (in_array($calculationType, ['percentage', 'daily_rate'], true)) {
                return $earning;
            }

            $earning['amount'] = $this->convert(
                (float) $earning['amount'],
                $context->salaryToBaseRate,
                $context->baseCurrency
            );

            if (isset($earning['calculation_basis'])) {
                $earning['calculation_basis'] = $this->convert(
                    (float) $earning['calculation_basis'],
                    $context->salaryToBaseRate,
                    $context->baseCurrency
                );
            }

            return $earning;
        }, $earnings);
    }

    /**
     * Apply payment currency amounts to a payroll record after statutory calculation.
     */
    public function applyPaymentCurrencyToRecord(
        PayrollRecord $record,
        PayrollCurrencyContext $context,
        float $taxableIncome
    ): PayrollRecord {
        $grossBase = (float) $record->gross_salary;
        $deductionsBase = (float) $record->total_deductions;
        $netBase = (float) $record->net_salary;

        $grossPayment = $context->requiresPaymentConversion
            ? $this->convert($grossBase, $context->baseToPaymentRate, $context->paymentCurrency)
            : $grossBase;

        $deductionsPayment = $context->requiresPaymentConversion
            ? $this->convert($deductionsBase, $context->baseToPaymentRate, $context->paymentCurrency)
            : $deductionsBase;

        $netPayment = $context->requiresPaymentConversion
            ? $this->convert($netBase, $context->baseToPaymentRate, $context->paymentCurrency)
            : $netBase;

        $primaryRate = $context->primaryExchangeRateRecord();

        $record->fill([
            'base_currency' => $context->baseCurrency,
            'payment_currency' => $context->paymentCurrency,
            'exchange_rate_used' => $context->isMultiCurrency() ? $context->snapshotExchangeRate() : 1,
            'exchange_rate_date' => $context->exchangeRateDate,
            'exchange_rate_id' => $primaryRate?->id,
            'taxable_income_base_currency' => $this->roundForCurrency($taxableIncome, $context->baseCurrency),
            'gross_payment_currency' => $grossPayment,
            'total_deductions_payment_currency' => $deductionsPayment,
            'net_pay_payment_currency' => $netPayment,
            'currency_conversion_notes' => $context->conversionNotes(),
        ]);

        $record->save();

        return $record;
    }
}
