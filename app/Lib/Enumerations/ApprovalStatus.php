<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Lib\Enumerations;

class ApprovalStatus
{
    public const DRAFT = -1;
    public const PENDING = 0;
    public const APPROVED = 1;
    public const REJECTED = 2;
    public const CANCELLED = 3;
    public const NOT_APPLICABLE = 4;

    public static function toArray(): array
    {
        return [
            self::DRAFT => 'Draft',
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::CANCELLED => 'Cancelled',
            self::NOT_APPLICABLE => 'Not Applicable',
        ];
    }

    // Function to get the name based on id
    public static function getName($value): string
    {
        switch ($value) {
            case self::DRAFT:
                return 'Draft';
            case self::APPROVED:
                return 'Approved';
            case self::PENDING:
                return 'Pending';
            case self::REJECTED:
                return 'Rejected';
            case self::CANCELLED:
                return 'Cancelled';
            case self::NOT_APPLICABLE:
                return 'Not Applicable';
            default:
                return 'Unknown';
        }
    }

    public static function getValue($name): int
    {
        switch ($name) {
            case 'Draft':
                return self::DRAFT;
            case 'Approved':
                return self::APPROVED;
            case 'Pending':
                return self::PENDING;
            case 'Rejected':
                return self::REJECTED;
            case 'Cancelled':
                return self::CANCELLED;
            case 'Not Applicable':
                return self::NOT_APPLICABLE;
            default:
                return self::PENDING; // Default to pending for unknown statuses
        }
    }

    // Optional: Helper method to check if status is final (no further changes expected)
    public static function isFinalStatus($value): bool
    {
        return in_array($value, [
            self::APPROVED,
            self::REJECTED,
            self::CANCELLED
        ]);
    }
}
