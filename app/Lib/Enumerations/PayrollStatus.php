<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Lib\Enumerations;

class PayrollStatus
{
    public const DRAFT = -1;
    public const CALCULATED = 0;
    public const SUBMITTED = 1;
    public const APPROVED = 2;
    public const PAID = 3;
    public const REJECTED = 4;
    public const CANCELLED = 5;

    public static function toArray(): array
    {
        return [
            self::DRAFT => 'Draft',
            self::CALCULATED => 'Calculated',
            self::SUBMITTED => 'Submitted for Approval',
            self::APPROVED => 'Approved',
            self::PAID => 'Paid',
            self::REJECTED => 'Rejected',
            self::CANCELLED => 'Cancelled',
        ];
    }

    // Function to get the name based on id
    public static function getName($value): string
    {
        switch ($value) {
            case self::DRAFT:
                return 'Draft';
            case self::CALCULATED:
                return 'Calculated';
            case self::SUBMITTED:
                return 'Submitted for Approval';
            case self::APPROVED:
                return 'Approved';
            case self::PAID:
                return 'Paid';
            case self::REJECTED:
                return 'Rejected';
            case self::CANCELLED:
                return 'Cancelled';
            default:
                return 'Unknown';
        }
    }

    public static function getValue($name): int
    {
        switch ($name) {
            case 'Draft':
                return self::DRAFT;
            case 'Calculated':
                return self::CALCULATED;
            case 'Submitted for Approval':
            case 'Submitted':
                return self::SUBMITTED;
            case 'Approved':
                return self::APPROVED;
            case 'Paid':
                return self::PAID;
            case 'Rejected':
                return self::REJECTED;
            case 'Cancelled':
                return self::CANCELLED;
            default:
                return self::DRAFT; // Default to draft for unknown statuses
        }
    }

    // Get the appropriate CSS class for the status badge
    public static function getBadgeClass($value): string
    {
        switch ($value) {
            case self::DRAFT:
                return 'label-default';
            case self::CALCULATED:
                return 'label-info';
            case self::SUBMITTED:
                return 'label-primary';
            case self::APPROVED:
                return 'label-warning';
            case self::PAID:
                return 'label-success';
            case self::REJECTED:
                return 'label-danger';
            case self::CANCELLED:
                return 'label-default';
            default:
                return 'label-default';
        }
    }

    // Check if status allows editing
    public static function isEditable($value): bool
    {
        return in_array($value, [
            self::DRAFT,
            self::CALCULATED,
            self::REJECTED // Can be edited after rejection
        ]);
    }

    // Check if status is final (no further changes expected)
    public static function isFinalStatus($value): bool
    {
        return in_array($value, [
            self::PAID,
            self::CANCELLED
        ]);
    }

    // Check if status requires approval
    public static function requiresApproval($value): bool
    {
        return in_array($value, [
            self::SUBMITTED
        ]);
    }

    // Get next valid statuses for workflow progression
    public static function getNextValidStatuses($currentStatus): array
    {
        switch ($currentStatus) {
            case self::DRAFT:
                return [self::CALCULATED, self::SUBMITTED];
            case self::CALCULATED:
                return [self::SUBMITTED, self::DRAFT];
            case self::SUBMITTED:
                return [self::APPROVED, self::REJECTED, self::CANCELLED];
            case self::APPROVED:
                return [self::PAID, self::CANCELLED];
            case self::REJECTED:
                return [self::DRAFT, self::SUBMITTED];
            case self::PAID:
                return []; // No further changes after paid
            case self::CANCELLED:
                return [self::DRAFT]; // Can restart from draft
            default:
                return [];
        }
    }
}