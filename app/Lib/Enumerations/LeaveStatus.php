<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Lib\Enumerations;

class LeaveStatus
{
    public const PENDING = 1;
    public const APPROVE = 2;
    public const REJECT = 3;
    public const RECALL = 4;
    public const RECALL_APPROVED = 5;

    public static function toArray(): array
    {
        return [
            self::PENDING => 'PENDING',
            self::APPROVE => 'APPROVED',
            self::REJECT => 'REJECTED',
            self::RECALL => 'RECALLED',
            self::RECALL_APPROVED => 'RECALL APPROVED',
        ];
    }

    public static function getName($value): string
    {
        switch ($value) {
            case self::PENDING:
                return 'PENDING';
            case self::APPROVE:
                return 'APPROVED';
            case self::REJECT:
                return 'REJECTED';
            case self::RECALL:
                return 'RECALLED';
            case self::RECALL_APPROVED:
                return 'RECALL APPROVED';
            default:
                return 'UNKNOWN';
        }
    }
}