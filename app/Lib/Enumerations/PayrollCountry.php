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

    public static function currencyCode(int $countryId): string
    {
        return match ($countryId) {
            self::KENYA => 'KES',
            self::RWANDA => 'RWF',
            self::UGANDA => 'UGX',
            self::TANZANIA => 'TZS',
            self::SOUTH_SUDAN => 'SSP',
            self::SOMALIA => 'USD',
            self::BURUNDI => 'BIF',
            self::SOUTH_AFRICA => 'ZAR',
            default => 'KES',
        };
    }

    /** @return array<string, string> */
    public static function statutoryLabels(int $countryId): array
    {
        return match ($countryId) {
            self::KENYA => [
                'paye' => 'PAYE Tax',
                'social_security' => 'NSSF',
                'social_security_tier1' => 'NSSF Tier 1',
                'social_security_tier2' => 'NSSF Tier 2',
                'health' => 'SHIF',
                'housing' => 'Housing Levy',
                'pension' => 'Pension',
            ],
            self::RWANDA => [
                'paye' => 'PAYE',
                'social_security' => 'RSSB Pension',
                'health' => 'Medical Insurance',
                'housing' => 'Maternity Levy',
                'pension' => 'Pension',
            ],
            self::UGANDA => [
                'paye' => 'PAYE',
                'social_security' => 'NSSF',
                'health' => 'Health Insurance',
                'housing' => 'Other Levy',
                'pension' => 'Pension',
            ],
            self::TANZANIA => [
                'paye' => 'PAYE',
                'social_security' => 'NSSF',
                'health' => 'Health Insurance',
                'housing' => 'SDL (Employer)',
                'pension' => 'Pension',
            ],
            self::SOUTH_SUDAN => [
                'paye' => 'PAYE',
                'social_security' => 'SSSIF',
                'health' => 'Health Insurance',
                'housing' => 'Other Levy',
                'pension' => 'Pension',
            ],
            self::SOMALIA => [
                'paye' => 'PAYE',
                'social_security' => 'Social Security',
                'health' => 'Health Insurance',
                'housing' => 'Other Levy',
                'pension' => 'Pension',
            ],
            self::BURUNDI => [
                'paye' => 'PAYE (IPR)',
                'social_security' => 'INSS',
                'health' => 'Health Insurance',
                'housing' => 'Other Levy',
                'pension' => 'Pension',
            ],
            self::SOUTH_AFRICA => [
                'paye' => 'PAYE',
                'social_security' => 'UIF',
                'health' => 'Health Insurance',
                'housing' => 'SDL (Employer)',
                'pension' => 'Pension',
            ],
            default => [
                'paye' => 'PAYE Tax',
                'social_security' => 'Social Security',
                'health' => 'Health Levy',
                'housing' => 'Other Levy',
                'pension' => 'Pension',
            ],
        };
    }

    /** @return array<string, string> */
    public static function employerContributionLabels(int $countryId): array
    {
        return match ($countryId) {
            self::KENYA => [
                'social_security_tier1' => 'NSSF Tier 1 (Employer)',
                'social_security_tier2' => 'NSSF Tier 2 (Employer)',
                'housing' => 'Housing Levy (Employer)',
                'pension' => 'Pension (Employer)',
                'training_levy' => 'Industrial Training Levy',
            ],
            self::RWANDA => [
                'social_security_tier1' => 'RSSB Pension (Employer)',
                'housing' => 'Maternity Levy (Employer)',
                'pension' => 'Pension (Employer)',
            ],
            self::UGANDA => [
                'social_security_tier1' => 'NSSF (Employer)',
                'pension' => 'Pension (Employer)',
            ],
            self::TANZANIA => [
                'social_security_tier1' => 'NSSF (Employer)',
                'housing' => 'SDL (Employer)',
                'pension' => 'Pension (Employer)',
            ],
            self::SOUTH_SUDAN => [
                'social_security_tier1' => 'SSSIF (Employer)',
                'pension' => 'Pension (Employer)',
            ],
            self::SOMALIA => [
                'social_security_tier1' => 'Social Security (Employer)',
                'pension' => 'Pension (Employer)',
            ],
            self::BURUNDI => [
                'social_security_tier1' => 'INSS (Employer)',
                'pension' => 'Pension (Employer)',
            ],
            self::SOUTH_AFRICA => [
                'social_security_tier1' => 'UIF (Employer)',
                'housing' => 'SDL (Employer)',
                'pension' => 'Pension (Employer)',
            ],
            default => [
                'social_security_tier1' => 'Social Security (Employer)',
                'housing' => 'Employer Levy',
                'pension' => 'Pension (Employer)',
            ],
        };
    }
}
