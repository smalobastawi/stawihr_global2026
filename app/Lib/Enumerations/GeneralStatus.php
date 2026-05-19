<?php

namespace App\Lib\Enumerations;

class GeneralStatus
{
    public const INACTIVE = 0;
    public const ACTIVE = 1;
    public const SUSPENDED = 2;

        public static function toArray(): array
    {
        return [
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::SUSPENDED => 'Suspended',

        ];
    }
    public static function getArray(): array
    {
        return [
            self::ACTIVE => __('common.active'),  // Using translations
            self::INACTIVE => __('common.inactive'),
            self::SUSPENDED => __('common.suspended'),
        ];
    }

    public static function getName($value): string
    {
        $statuses = self::getArray();
        return $statuses[$value] ?? 'Unknown';
    }

    public static function getValue($name): int
    {
        $flipped = array_flip(self::getArray());
        return $flipped[$name] ?? self::INACTIVE;
    }

    // New method for select options
    public static function getSelectOptions(): array
    {
        return ['' => __('common.select_status')] + self::getArray();
    }
}