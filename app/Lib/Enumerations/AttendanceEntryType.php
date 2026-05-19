<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */
namespace App\Lib\Enumerations;

class AttendanceEntryType
{
    public const BIOMETRIC = 0;
    public const MOBILE_APP = 1;
    public const WEB = 2;

    public static function toArray(): array
    {
        return [
            self::BIOMETRIC => 'Biometric',
            self::MOBILE_APP => 'Mobile App',
            self::WEB => 'Web',
            
        ];
    }

    // Function to get the name based on id
    public static function getName($value): string
    {
        switch ($value) {
            case self::BIOMETRIC:
                return 'Biometric';
            case self::MOBILE_APP:
                return 'Mobile App';
            case self::WEB:
                return 'Web';

            default:
                return 'Unknown';
        }
    }

    public static function getValue($name): int
    {
        switch ($name) {
            case 'Biometric':
                return self::BIOMETRIC;
            case 'Mobile App':
                return self::MOBILE_APP;
            case 'Web':
                return self::WEB;
            default:
                return 0;
        }
    }
}
