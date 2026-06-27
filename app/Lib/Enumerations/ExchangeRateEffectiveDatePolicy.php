<?php

namespace App\Lib\Enumerations;

class ExchangeRateEffectiveDatePolicy
{
    public const PAYROLL_PERIOD_END = 'payroll_period_end';
    public const PAYROLL_PERIOD_START = 'payroll_period_start';
    public const PAYMENT_DATE = 'payment_date';
    public const LATEST_APPROVED = 'latest_approved';

    public static function toArray(): array
    {
        return [
            self::PAYROLL_PERIOD_END => 'Payroll Period End Date',
            self::PAYROLL_PERIOD_START => 'Payroll Period Start Date',
            self::PAYMENT_DATE => 'Payment Date',
            self::LATEST_APPROVED => 'Latest Approved Rate On Or Before Period End',
        ];
    }
}
