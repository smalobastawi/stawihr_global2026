<?php 

namespace App\Lib\Enumerations;

class SurveyStatus
{
    public const DRAFT = 1;
    public const PUBLISHED = 2;
    public const CLOSED = 3;
    public const CANCELED = 0; //TERMINATED

    public static function toArray(): array
    {
        return [
            self::DRAFT => 'DRAFT',
            self::PUBLISHED => 'PUBLISHED',
            self::CLOSED => 'CLOSED',
            self::CANCELED => 'CANCELED'
        ];
    }

    public static function getValue($name)
    {
        switch (strtoupper($name)) {
            case 'DRAFT':
                return self::DRAFT;
            case 'PUBLISHED':
                return self::PUBLISHED;
            case 'CLOSED':
                return self::CLOSED;
            case 'CANCELED':
                return self::CANCELED;
            default:
                return null;
        }
    }


}