<?php 

namespace App\Lib\Enumerations;

class TrainingAttendanceStatus
{
    public const PENDING = 0;
    public const CONFIRMED = 1;
    public const DECLINED = 2;//TERMINATED

    public static function toArray(): array
    {
        return [
            self::PENDING => 'PENDING',
            self::CONFIRMED => 'CONFIRMED',
            self::DECLINED => 'DECLINED'
        ];
    }

    public static function getName($value) : string
    {
        // Handle null case
        if ($value === null) {
            return 'PENDING';
        }

        // Handle string values
        if (is_string($value)) {
            if (in_array(strtoupper($value), ['PENDING', 'CONFIRMED', 'DECLINED'])) {
                return strtoupper($value);
            }
            return 'UNKNOWN';
        }

        // Handle numeric values
        switch (intval($value)) {
            case self::PENDING: return 'PENDING';
            case self::CONFIRMED: return 'CONFIRMED';
            case self::DECLINED: return 'DECLINED';
            default: return 'UNKNOWN';
        }
    }

    public static function getValue($name)
    {
        switch (strtoupper($name)) {
            case 'PENDING':
                return self::PENDING;
            case 'CONFIRMED':
                return self::CONFIRMED;
            case 'DECLINED':
                return self::DECLINED;
            default:
                return null;
        }
    }
}