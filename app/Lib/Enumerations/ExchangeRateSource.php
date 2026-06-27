<?php

namespace App\Lib\Enumerations;

class ExchangeRateSource
{
    public const MANUAL = 'manual';
    public const API = 'api';

    public static function toArray(): array
    {
        return [
            self::MANUAL => 'Manual Entry',
            self::API => 'External API',
        ];
    }
}
