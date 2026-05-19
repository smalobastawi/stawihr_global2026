<?php

namespace App\Lib\Enumerations;

class EarningCategories
{
    public const SALARY = 'salary';
    public const ALLOWANCE = 'allowance';
    public const BONUS = 'bonus';
    public const OVERTIME = 'overtime';
    public const COMMISSION = 'commission';
    public const OTHER = 'other';

    public static function toArray(): array
    {
        return [
            self::SALARY => 'Salary',
            self::ALLOWANCE => 'Allowance',
            self::BONUS => 'Bonus',
            self::OVERTIME => 'Overtime',
            self::COMMISSION => 'Commission',
            self::OTHER => 'Other',
        ];
    }

    //function to get the name based on value
    public static function getName($value): string
    {
        switch ($value) {
            case self::SALARY:
                return 'Salary';
            case self::ALLOWANCE:
                return 'Allowance';
            case self::BONUS:
                return 'Bonus';
            case self::OVERTIME:
                return 'Overtime';
            case self::COMMISSION:
                return 'Commission';
            case self::OTHER:
                return 'Other';
            default:
                return 'Unknown';
        }
    }

    public static function getValue($name): string
    {
        switch ($name) {
            case 'Salary':
                return self::SALARY;
            case 'Allowance':
                return self::ALLOWANCE;
            case 'Bonus':
                return self::BONUS;
            case 'Overtime':
                return self::OVERTIME;
            case 'Commission':
                return self::COMMISSION;
            case 'Other':
                return self::OTHER;
            default:
                return '';
        }
    }

    public static function getValidationRule(): string
    {
        return 'in:' . implode(',', [
            self::SALARY,
            self::ALLOWANCE,
            self::BONUS,
            self::OVERTIME,
            self::COMMISSION,
            self::OTHER,
        ]);
    }
}
