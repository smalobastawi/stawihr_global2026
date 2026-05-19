<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Lib\Enumerations;

class OvertimeCalculationType
{

    public const OVERTIME_1 = 'overtime_1';
    public const OVERTIME_1_5 = 'overtime_1_5';
    public const OVERTIME_2 = 'overtime_2';

    public static function toArray(): array
    {
        return [

            self::OVERTIME_1 => 'Overtime 1x',
            self::OVERTIME_1_5 => 'Overtime 1.5x',
            self::OVERTIME_2 => 'Overtime 2x',
        ];
    }

    public static function getName($value): string
    {
        switch ($value) {

            case self::OVERTIME_1:
                return 'Overtime 1x';
            case self::OVERTIME_1_5:
                return 'Overtime 1.5x';
            case self::OVERTIME_2:
                return 'Overtime 2x';
            default:
                return 'Unknown';
        }
    }

    public static function getValue($name): string
    {
        switch ($name) {

            case 'Overtime 1x':
                return self::OVERTIME_1;
            case 'Overtime 1.5x':
                return self::OVERTIME_1_5;
            case 'Overtime 2x':
                return self::OVERTIME_2;
            default:
                return 'Unknown';
        }
    }
}
