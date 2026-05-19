<?php
namespace App\Lib\Enumerations;

class Gender
{
    public const MALE = 1;
    public const FEMALE = 2;
    public const ALL  = 3;

    public static function toArray(): array
    {
        return [
            self::ALL => 'ALL',
            self::MALE => 'MALE',
            self::FEMALE => 'FEMALE',
        ];
    }

    public static function getName($value): string
    {
        switch ($value) {
            case self::MALE:
                return 'MALE';
            case self::FEMALE:
                return 'FEMALE';
            case self::ALL:
                return 'ALL';
            default:
                return 'Unknown';
        }
    }

    public static function getValue($name)
    {
        switch (strtoupper($name)) {
            case 'MALE':
                return self::MALE;
            case 'FEMALE':
                return self::FEMALE;
            default:
                return null;
        }
    }
}
