<?php

namespace App\Lib\Enumerations;

class ExchangeRateStatus
{
    public const ACTIVE = 'active';
    public const LOCKED = 'locked';

    /** @deprecated Legacy records only */
    public const DRAFT = 'draft';

    /** @deprecated Legacy records only */
    public const APPROVED = 'approved';

    public static function toArray(): array
    {
        return [
            self::ACTIVE => 'Active',
            self::LOCKED => 'Locked',
        ];
    }

    public static function isEditable(?string $status): bool
    {
        return $status !== self::LOCKED;
    }

    /** Statuses that payroll may select when looking up a rate. */
    public static function usableForPayroll(): array
    {
        return [self::ACTIVE, self::DRAFT, self::APPROVED];
    }
}
