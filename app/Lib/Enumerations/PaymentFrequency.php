<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */
namespace App\Lib\Enumerations;

class PaymentFrequency
{
    public const MONTHLY = 'monthly';
    public const WEEKLY = 'weekly';
    public const BI_WEEKLY = 'bi_weekly';
    public const QUARTERLY = 'quarterly';
    public const ANNUALLY = 'annually';
    public const ONE_TIME = 'one_time';

    public static function toArray(): array
    {
        return [
            self::MONTHLY => 'Monthly',
            self::WEEKLY => 'Weekly',
            self::BI_WEEKLY => 'Bi-Weekly',
            self::QUARTERLY => 'Quarterly',
            self::ANNUALLY => 'Annually',
            self::ONE_TIME => 'One Time',
        ];
    }

    public static function getName($value): string
    {
        switch ($value) {
            case self::MONTHLY:
                return 'Monthly';
            case self::WEEKLY:
                return 'Weekly';
            case self::BI_WEEKLY:
                return 'Bi-Weekly';
            case self::QUARTERLY:
                return 'Quarterly';
            case self::ANNUALLY:
                return 'Annually';
            case self::ONE_TIME:
                return 'One Time';
            default:
                return 'Unknown';
        }
    }

    public static function getValue($name): string
    {
        switch ($name) {
            case 'Monthly':
                return self::MONTHLY;
            case 'Weekly':
                return self::WEEKLY;
            case 'Bi-Weekly':
                return self::BI_WEEKLY;
            case 'Quarterly':
                return self::QUARTERLY;
            case 'Annually':
                return self::ANNUALLY;
            case 'One Time':
                return self::ONE_TIME;
            default:
                return self::MONTHLY;
        }
    }

    public static function getDaysCount($frequency): int
    {
        switch ($frequency) {
            case self::WEEKLY:
                return 7;
            case self::BI_WEEKLY:
                return 14;
            case self::MONTHLY:
                return 30; // Approximate
            case self::QUARTERLY:
                return 90; // Approximate
            case self::ANNUALLY:
                return 365;
            default:
                return 0;
        }
    }
}