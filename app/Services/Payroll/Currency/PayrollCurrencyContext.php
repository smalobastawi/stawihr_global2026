<?php

namespace App\Services\Payroll\Currency;

use App\Models\Payroll\CurrencyExchangeRate;
use App\Models\Payroll\EmployeePayroll;
use App\Models\Payroll\PayrollPeriod;

/**
 * Resolved currency context for a single employee payroll run.
 */
class PayrollCurrencyContext
{
    public function __construct(
        public readonly string $baseCurrency,
        public readonly string $salaryCurrency,
        public readonly string $paymentCurrency,
        public readonly float $salaryToBaseRate,
        public readonly float $baseToPaymentRate,
        public readonly ?CurrencyExchangeRate $salaryToBaseRateRecord,
        public readonly ?CurrencyExchangeRate $baseToPaymentRateRecord,
        public readonly bool $requiresSalaryConversion,
        public readonly bool $requiresPaymentConversion,
        public readonly ?string $exchangeRateDate = null,
    ) {
    }

    public function isMultiCurrency(): bool
    {
        return $this->requiresSalaryConversion || $this->requiresPaymentConversion;
    }

    public function snapshotExchangeRate(): ?float
    {
        if (!$this->requiresPaymentConversion && !$this->requiresSalaryConversion) {
            return 1.0;
        }

        return $this->baseToPaymentRate;
    }

    public function primaryExchangeRateRecord(): ?CurrencyExchangeRate
    {
        return $this->baseToPaymentRateRecord ?? $this->salaryToBaseRateRecord;
    }

    public function conversionNotes(): ?string
    {
        if (!$this->isMultiCurrency()) {
            return null;
        }

        $parts = [];

        if ($this->requiresSalaryConversion) {
            $parts[] = sprintf(
                'Salary %s converted to statutory base %s at rate %s (effective %s).',
                $this->salaryCurrency,
                $this->baseCurrency,
                number_format($this->salaryToBaseRate, 6),
                $this->salaryToBaseRateRecord?->effective_date?->format('Y-m-d') ?? $this->exchangeRateDate ?? 'N/A'
            );
        }

        if ($this->requiresPaymentConversion) {
            $parts[] = sprintf(
                'Net pay converted from %s to payment currency %s at rate %s.',
                $this->baseCurrency,
                $this->paymentCurrency,
                number_format($this->baseToPaymentRate, 6)
            );
        }

        return implode(' ', $parts);
    }
}
