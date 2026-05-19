<?php

namespace App\Lib\Enumerations;

class StaffContractTypes
{
    public const INTERNSHIP = 1;
    public const  FIXED = 2;
    public const  TEMPORARY = 3;
    public const  VOLUNTEER = 4;

    public static function toArray(): array
    {
        return [
            self::INTERNSHIP => 'INTERNSHIP',
            self:: FIXED => 'FIXED',
            self:: TEMPORARY => 'TEMPORARY',
            self:: VOLUNTEER => 'VOLUNTEER',
        ];
    }

    public static function getName($value): string
    {
        switch ($value) {
            case self::INTERNSHIP:
                return 'INTERNSHIP';
            case self::FIXED:
                return 'FIXED';
            case self::TEMPORARY:
                return 'TEMPORARY';
            case self::VOLUNTEER:
                return 'VOLUNTEER';
            default:
                return 'Unknown';
        }
    }

    public static function getValue($name)
    {
        switch (strtoupper($name)) {
            case 'INTERNSHIP':
                return self::INTERNSHIP;
            case 'FIXED':
                return self:: FIXED;
            case 'TEMPORARY':
                return self:: TEMPORARY;
            case 'VOLUNTEER':
                return self:: VOLUNTEER;
            default:
                return null;
        }
    }
}
