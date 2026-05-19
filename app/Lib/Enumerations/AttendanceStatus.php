<?php

namespace App\Lib\Enumerations;
class AttendanceStatus
{
    public const ABSENT = 0;
    public const PRESENT = 1;
    

    public static function toArray(): array
    {
        return [
            self::PRESENT => 'Present',
            self::ABSENT => 'Absent',
        ];
    }
    public static function getName($value): string
    {
        switch ($value) {
            case self::PRESENT:
                return 'P';
            case self::ABSENT:
                return 'A';
            default:
                return 'U';
        }
    }

}
