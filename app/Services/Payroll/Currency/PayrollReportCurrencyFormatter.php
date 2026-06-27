<?php

namespace App\Services\Payroll\Currency;

use App\Models\Payroll\PayrollRecord;

class PayrollReportCurrencyFormatter
{
    public function __construct(
        protected CurrencyConversionService $conversionService
    ) {
    }

    public function statutoryAmount(PayrollRecord $record, string $field): float
    {
        return (float) ($record->{$field} ?? 0);
    }

    public function paymentAmount(PayrollRecord $record, string $baseField): float
    {
        $paymentFieldMap = [
            'gross_salary' => 'gross_payment_currency',
            'total_deductions' => 'total_deductions_payment_currency',
            'net_salary' => 'net_pay_payment_currency',
        ];

        if (isset($paymentFieldMap[$baseField]) && $record->{$paymentFieldMap[$baseField]} !== null) {
            return (float) $record->{$paymentFieldMap[$baseField]};
        }

        return (float) ($record->{$baseField} ?? 0);
    }

    public function formatStatutory(PayrollRecord $record, string $field): string
    {
        $currency = $record->base_currency ?? $record->employeePayroll?->getDisplayCurrency() ?? 'KES';

        return $this->conversionService->format($this->statutoryAmount($record, $field), $currency);
    }

    public function formatPayment(PayrollRecord $record, string $baseField = 'net_salary'): string
    {
        $currency = $record->payment_currency ?? $record->base_currency ?? 'KES';

        return $this->conversionService->format($this->paymentAmount($record, $baseField), $currency);
    }

    public function isMultiCurrencyRecord(PayrollRecord $record): bool
    {
        return $record->base_currency
            && $record->payment_currency
            && strtoupper($record->base_currency) !== strtoupper($record->payment_currency);
    }

    /**
     * @param iterable<PayrollRecord> $records
     * @return array<string, array<int, PayrollRecord>>
     */
    public function groupByPaymentCurrency(iterable $records): array
    {
        $grouped = [];

        foreach ($records as $record) {
            $currency = strtoupper($record->payment_currency ?? $record->base_currency ?? 'KES');
            $grouped[$currency][] = $record;
        }

        ksort($grouped);

        return $grouped;
    }
}
