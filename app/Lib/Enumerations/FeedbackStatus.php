<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */
namespace App\Lib\Enumerations;

class FeedbackStatus
{
    public const PENDING = 0;
    public const REVIEWED = 1;
    public const ACTIONED = 2;
    public const CLOSED = 3;

    public static function toArray(): array
    {
        return [
            self::PENDING => 'Pending',
            self::REVIEWED => 'Reviewed',
            self::ACTIONED => 'Actioned',
            self::CLOSED => 'Closed',
        ];
    }

    // Function to get the name based on id
    public static function getName($value): string
    {
        switch ($value) {
            case self::PENDING:
                return 'Pending';
            case self::REVIEWED:
                return 'Reviewed';
            case self::ACTIONED:
                return 'Actioned';
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
            case 'Reviewed':
                return self::REVIEWED;
            case 'Actioned':
                return self::ACTIONED;
            case 'Closed':
                return self::CLOSED;
            default:
                return 0;
        }
    }
}
