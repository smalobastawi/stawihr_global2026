<?php

namespace App\Lib\Enumerations;

class CalculationTypes
{
    public const FIXED_AMOUNT = 'fixed_amount';
    public const PERCENTAGE_OF_BASIC = 'percentage_of_basic';
    public const PERCENTAGE_OF_GROSS = 'percentage_of_gross';
    public const DAILY_RATE = 'daily_rate';

    public static function toArray(): array
    {
        return [
            self::FIXED_AMOUNT => 'Fixed Amount',
            self::PERCENTAGE_OF_BASIC => 'Percentage of Basic Income',
            self::PERCENTAGE_OF_GROSS => 'Percentage of Gross Salary',
            self::DAILY_RATE => 'Daily Rate',
        ];
    }

    //function to get the name based on value
    public static function getName($value): string
    {
        switch ($value) {
            case self::FIXED_AMOUNT:
                return 'Fixed Amount';
            case self::PERCENTAGE_OF_BASIC:
                return 'Percentage of Basic Income';
            case self::PERCENTAGE_OF_GROSS:
                return 'Percentage of Gross Salary';
            case self::DAILY_RATE:
                return 'Daily Rate';
            default:
                return 'Unknown';
        }
    }

    public static function getValue($name): string
    {
        switch ($name) {
            case 'Fixed Amount':
                return self::FIXED_AMOUNT;
            case 'Percentage of Basic Income':
                return self::PERCENTAGE_OF_BASIC;
            case 'Percentage of Gross Salary':
                return self::PERCENTAGE_OF_GROSS;
            case 'Daily Rate':
                return self::DAILY_RATE;
            default:
                return '';
        }
    }

    public static function getValidationRule(): string
    {
        return 'in:' . implode(',', [
            self::FIXED_AMOUNT,
            self::PERCENTAGE_OF_BASIC,
            self::PERCENTAGE_OF_GROSS,
            self::DAILY_RATE,
        ]);
    }
}