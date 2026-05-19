<?php

namespace App\Lib\Enumerations;
class IdentityType
{
    public const NATIONAL_ID = 'national_id';
    public const PASSPORT = 'passport';
    public const MILITARY_ID = 'military_id';
    public const DRIVING_LICENCE = 'driving_licence';
    public const ALIEN_ID = 'alien_id';
    public const DIPLOMATIC_ID = 'diplomatic_id';

    public static function toArray(): array
    {
        return [
            self::NATIONAL_ID => 'National ID',
            self::PASSPORT => 'Passport',
            self::MILITARY_ID => 'Military ID',
            self::DRIVING_LICENCE => 'Driving Licence',
            self::ALIEN_ID => 'Alien ID',
            self::DIPLOMATIC_ID => 'Diplomatic ID',
        ];
    }
}