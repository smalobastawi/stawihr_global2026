<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Lib\Enumerations;

class TerminationReasons
{
    public const RESIGNATION = 1;
    public const RETIREMENT = 2;
    public const REDUNDANCY_LAID_OFF = 3;
    public const SUMMARY_DISMISSAL = 4;
    public const DEATH = 5;
    public const UNSATISFACTORY_PERFORMANCE = 6;
    public const END_OF_CONTRACT = 7;

    public static function toArray(): array
    {
        return [
            self::RESIGNATION => 'Resignation',
            self::RETIREMENT => 'Retirement',
            self::REDUNDANCY_LAID_OFF => 'Redundancy/Laid Off',
            self::SUMMARY_DISMISSAL => 'Summary Dismissal',
            self::DEATH => 'Death',
            self::UNSATISFACTORY_PERFORMANCE => 'Unsatisfactory Performance',
            self::END_OF_CONTRACT => 'End of contract',
        ];
    }

    // Function to get the name based on id
    public static function getName($value): string
    {
        switch ($value) {
            case self::RESIGNATION:
                return 'Resignation';
            case self::RETIREMENT:
                return 'Retirement';
            case self::REDUNDANCY_LAID_OFF:
                return 'Redundancy/Laid Off';
            case self::SUMMARY_DISMISSAL:
                return 'Summary Dismissal';
            case self::DEATH:
                return 'Death';
            case self::UNSATISFACTORY_PERFORMANCE:
                return 'Unsatisfactory Performance';
            case self::END_OF_CONTRACT:
                return 'End of contract';
            default:
                return 'Unknown';
        }
    }

    public static function getValue($name): int
    {
        switch ($name) {
            case 'Resignation':
                return self::RESIGNATION;
            case 'Retirement':
                return self::RETIREMENT;
            case 'Redundancy/Laid Off':
                return self::REDUNDANCY_LAID_OFF;
            case 'Summary Dismissal':
                return self::SUMMARY_DISMISSAL;
            case 'Death':
                return self::DEATH;
            case 'Unsatisfactory Performance':
                return self::UNSATISFACTORY_PERFORMANCE;
            case 'End of contract':
                return self::END_OF_CONTRACT;
            default:
                return 0;
        }
    }
}
