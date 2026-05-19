<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */
namespace App\Lib\Enumerations;

class DisciplinaryCaseStatus 
{
    public const PENDING = 0;
    public const OPEN = 1;
    public const REVIEWED = 2;
    public const ACTIONED = 3;
    public const CLOSED = 4;
    public const REOPENED = 4;
    public const ESCALATED = 5;

    public static function toArray(): array
    {
        return [
            self::PENDING => 'Pending',
            self::OPEN => 'Open',
            self::REVIEWED => 'Reviewed',
            self::ACTIONED => 'Actioned',
            self::CLOSED => 'Closed',
            self::REOPENED => 'Re-Opened',
            self::ESCALATED => 'Escalated',
        ];
    }

    // Function to get the name based on id
    public static function getName($value): string
    {
        switch ($value) {
            case self::PENDING:
                return 'Pending';
            case self::OPEN:
                return 'Open';
                case self::REVIEWED:
                    return 'Reviewed';
            case self::ACTIONED:
                return 'Actioned';
            case self::CLOSED:
                return 'Closed';
            case self::REOPENED:
                    return 'Re-Opened';
            case self::ESCALATED:
                    return 'Escalated';
            default:
                return 'Unknown';
        }
    }

    public static function getValue($name): int
    {
        switch ($name) {
            case 'Pending':
                return self::PENDING;
            case 'Open':
                return self::OPEN;
                case 'Reviewed':
                    return self::REVIEWED;
            case 'Actioned':
                return self::ACTIONED;
            case 'Closed':
                return self::CLOSED;
            case 'Re-Opened':
                return self::REOPENED;
            case 'Escalated':
                return self::ESCALATED;
            default:
                return 0;
        }
    }
}
