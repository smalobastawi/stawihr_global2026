<?php 

namespace App\Lib\Enumerations;
class JobTypes
{
    public const REMOTE = 1;
    public const HYBRID = 2;
    public const ON_PREMISES = 3; // short answer

    public static function toArray(): array
    {
        return [
            self::REMOTE => 'REMOTE',
            self::HYBRID => 'HYBRID',
            self::ON_PREMISES => 'ON PREMISES',
        ];
    }
    public static function getName($value): string
    {
        switch ($value) {
            case self::REMOTE:
                return 'REMOTE';
            case self::HYBRID:
                return 'HYBRID';
            case self::ON_PREMISES:
                return 'ON PREMISES';
            default:
                return 'UNKNOWN';
        }
    }
}