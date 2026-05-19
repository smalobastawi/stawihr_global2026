<?php

namespace App\Lib\Enumerations;

class EarningFrequencies
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

    //function to get the name based on value
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
                return '';
        }
    }

    public static function getValidationRule(): string
    {
        return 'in:' . implode(',', [
            self::MONTHLY,
            self::WEEKLY,
            self::BI_WEEKLY,
            self::QUARTERLY,
            self::ANNUALLY,
            self::ONE_TIME,
        ]);
    }
}