<?php

namespace App\Lib\Enumerations;

/**
 * Supported payroll jurisdictions for multi-country calculation.
 */
class PayrollCountry
{
    public const KENYA = 1;
    public const RWANDA = 2;
    public const UGANDA = 3;
    public const TANZANIA = 4;
    public const SOUTH_SUDAN = 5;
    public const SOMALIA = 6;
    public const BURUNDI = 7;
    public const SOUTH_AFRICA = 8;

    public static function toArray(): array
    {
        return [
            self::KENYA => 'Kenya',
            self::RWANDA => 'Rwanda',
            self::UGANDA => 'Uganda',
            self::TANZANIA => 'Tanzania',
            self::SOUTH_SUDAN => 'South Sudan',
            self::SOMALIA => 'Somalia',
            self::BURUNDI => 'Burundi',
            self::SOUTH_AFRICA => 'South Africa',
        ];
    }

    public static function getName($value): string
    {
        return self::toArray()[$value] ?? 'Unknown';
    }

    public static function getValue($name)
    {
        $normalized = strtolower(trim((string) $name));

        foreach (self::toArray() as $id => $label) {
            if (strtolower($label) === $normalized) {
                return $id;
            }
        }

        $aliases = [
            'ke' => self::KENYA,
            'rw' => self::RWANDA,
            'ug' => self::UGANDA,
            'tz' => self::TANZANIA,
            'ss' => self::SOUTH_SUDAN,
            'so' => self::SOMALIA,
            'bi' => self::BURUNDI,
            'za' => self::SOUTH_AFRICA,
        ];

        return $aliases[$normalized] ?? null;
    }

    public static function supportedIds(): array
    {
        return array_keys(self::toArray());
    }

    public static function isSupported($value): bool
    {
        return in_array((int) $value, self::supportedIds(), true);
    }
}
