<?php
namespace App\Lib\Enumerations;

class ResidencyStatus
{
    public const RESIDENT = 1;
    public const NON_RESIDENT = 2;

    public static function toArray(): array
    {
        return [
            self::RESIDENT => 'RESIDENT',
            self::NON_RESIDENT => 'NON-RESIDENT',
        ];
    }

    public static function getName($value): string
    {
        switch ($value) {
            case self::RESIDENT:
                return 'RESIDENT';
            case self::NON_RESIDENT:
                return 'NON-RESIDENT';
            default:
                return 'Unknown';
        }
    }

    public static function getValue($name)
    {
        switch (strtoupper($name)) {
            case 'RESIDENT':
                return self::RESIDENT;
            case 'NON-RESIDENT':
                return self::NON_RESIDENT;
            default:
                return null;
        }
    }
}
