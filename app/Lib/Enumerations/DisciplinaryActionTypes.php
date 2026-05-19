<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */
namespace App\Lib\Enumerations;

class DisciplinaryActionTypes 
{
    public const PENDING = 0;
    public const WARNING = 1;
    public const SUSPENSION = 2;
    public const TERMINATION = 3;
    public const REPRIMAND = 4;
    public const CLOSED = 5;

    public static function toArray(): array
    {
        return [
            self::PENDING => 'Pending',
            self::WARNING => 'Warning',
            self::SUSPENSION => 'Suspension',
            self::TERMINATION => 'Termination',
            self::REPRIMAND => 'Reprimand',
            self::CLOSED => 'Closed',
        ];
    }

    // Function to get the name based on id
    public static function getName($value): string
    {
        switch ($value) {
            case self::PENDING:
                return 'Pending';
            case self::WARNING:
                return 'Warning';
            case self::SUSPENSION:
                return 'Suspension';
            case self::TERMINATION:
                return 'Termination';
            case self::REPRIMAND:
                return 'Reprimand';
            case self::CLOSED:
                return 'Closed';
            default:
                return 'Unknown';
        }
    }

    public static function getValue($name): int
    {
        switch ($name) {
            case 'Pending':
                return self::PENDING;
            case 'Warning':
                return self::WARNING;
            case 'Suspension':
                return self::SUSPENSION;
            case 'Termination':
                return self::TERMINATION;
            case 'Reprimand':
                return self::REPRIMAND;
            case 'Closed':
                    return self::CLOSED;
            default:
                return 0;
        }
    }
}
