<?php 

namespace App\Lib\Enumerations;

class TrainingInvitationStatus
{
    public const SENT = 0;
    public const ACCEPTED = 1;
    public const DECLINED = 2;//TERMINATED

    public static function toArray(): array
    {
        return [
            self::SENT => 'SENT',
            self::ACCEPTED => 'ACCEPTED',
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
            if (in_array(strtoupper($value), ['SENT', 'ACCEPTED', 'DECLINED'])) {
                return strtoupper($value);
            }
            return 'UNKNOWN';
        }

        // Handle numeric values
        switch (intval($value)) {
            case self::SENT: return 'SENT';
            case self::ACCEPTED: return 'ACCEPTED';
            case self::DECLINED: return 'DECLINED';
            default: return 'UNKNOWN';
        }
    }

    public static function getValue($name)
    {
        switch (strtoupper($name)) {
            case 'SENT':
                return self::SENT;
            case 'ACCEPTED':
                return self::ACCEPTED;
            case 'DECLINED':
                return self::DECLINED;
            default:
                return null;
        }
    }
}