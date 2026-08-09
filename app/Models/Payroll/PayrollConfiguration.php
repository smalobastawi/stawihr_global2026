<?php

namespace App\Models\Payroll;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollConfiguration extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'config_key',
        'config_value',
        'config_type',
        'description',
        'effective_date',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'config_value' => 'json',
        'effective_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * KRA Individual Income Tax monthly bands (Finance Act 2023 — still in force 2025/2026).
     * Stored as progressive slices: "on the first / on the next" widths from KRA PAYE guide.
     *
     * @see https://kra.go.ke/individual/filing-paying/types-of-taxes/paye
     */
    const PAYE_SLICES = [
        ['width' => 24000, 'rate' => 0.10],   // first 24,000
        ['width' => 8333, 'rate' => 0.25],    // next 8,333  → cumulative 32,333
        ['width' => 467667, 'rate' => 0.30],  // next 467,667 → cumulative 500,000
        ['width' => 300000, 'rate' => 0.325], // next 300,000 → cumulative 800,000
        ['width' => null, 'rate' => 0.35],    // above 800,000
    ];

    /**
     * Equivalent min/max representation (for UI / DB seeding). Widths match PAYE_SLICES.
     */
    const PAYE_BANDS = [
        ['min' => 0, 'max' => 24000, 'rate' => 0.10],
        ['min' => 24001, 'max' => 32333, 'rate' => 0.25],
        ['min' => 32334, 'max' => 500000, 'rate' => 0.30],
        ['min' => 500001, 'max' => 800000, 'rate' => 0.325],
        ['min' => 800001, 'max' => null, 'rate' => 0.35],
    ];

    /**
     * NSSF Act Cap 258 phased limits (employee + employer each pay 6%).
     * Year 4 effective February 2026: LEL 9,000 / UEL 108,000.
     */
    const NSSF_PHASES = [
        // Year 4 — from Feb 2026
        ['effective_from' => '2026-02-01', 'lel' => 9000, 'uel' => 108000],
        // Year 3 — Feb 2025 to Jan 2026
        ['effective_from' => '2025-02-01', 'lel' => 8000, 'uel' => 72000],
        // Year 2 — Feb 2024 to Jan 2025
        ['effective_from' => '2024-02-01', 'lel' => 7000, 'uel' => 36000],
        // Fallback pre-phase
        ['effective_from' => '1970-01-01', 'lel' => 6000, 'uel' => 18000],
    ];

    /** @deprecated Use NSSF_PHASES / getNssfLimitsForDate() */
    const NSSF_RATES = [
        'tier_1' => ['min' => 0, 'max' => 9000, 'employee_rate' => 0.06, 'employer_rate' => 0.06],
        'tier_2' => ['min' => 9001, 'max' => 108000, 'employee_rate' => 0.06, 'employer_rate' => 0.06],
    ];

    const HOUSING_LEVY_RATE = 0.015; // 1.5% of gross salary
    const SHIF_RATE = 0.0275; // 2.75% of gross
    const SHIF_MINIMUM = 300;

    const PERSONAL_RELIEF = 2400; // Monthly personal relief (KRA)
    const PERSONAL_RELIEF_ANNUAL = 28800;
    const INSURANCE_RELIEF_RATE = 0.15;
    const INSURANCE_RELIEF_LIMIT = 5000; // Monthly insurance relief limit
    const MORTGAGE_RELIEF_LIMIT = 30000; // Owner-occupied interest from Dec 2024
    const PENSION_CONTRIBUTION_CAP = 30000; // Monthly from Dec 2024
    const PRMF_CAP = 15000; // Post-retirement medical fund from Dec 2024

    public static function getConfig($key, $default = null)
    {
        $config = self::where('config_key', $key)
            ->where('is_active', true)
            ->where('effective_date', '<=', now())
            ->orderBy('effective_date', 'desc')
            ->first();

        return $config ? $config->config_value : $default;
    }

    public static function setConfig($key, $value, $description = null, $effectiveDate = null)
    {
        return self::create([
            'config_key' => $key,
            'config_value' => $value,
            'config_type' => gettype($value),
            'description' => $description,
            'effective_date' => $effectiveDate ?? now(),
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);
    }

    public static function getPayeBands()
    {
        return self::getConfig('paye_bands', self::PAYE_BANDS);
    }

    /**
     * @return array<int, array{width: int|null, rate: float}>
     */
    public static function getPayeSlices(): array
    {
        $configured = self::getConfig('paye_slices');
        if (is_array($configured) && $configured !== []) {
            return $configured;
        }

        // Derive slices from min/max bands if only those are configured.
        $bands = self::getPayeBands();
        $slices = [];
        foreach ($bands as $band) {
            $min = (float) ($band['min'] ?? 0);
            $max = $band['max'] ?? null;
            $rate = (float) ($band['rate'] ?? 0);
            if ($max === null) {
                $slices[] = ['width' => null, 'rate' => $rate];
                continue;
            }
            $width = $min <= 0 ? (float) $max : ((float) $max - $min + 1);
            $slices[] = ['width' => $width, 'rate' => $rate];
        }

        return $slices !== [] ? $slices : self::PAYE_SLICES;
    }

    public static function getNssfRates()
    {
        return self::getConfig('nssf_rates', self::NSSF_RATES);
    }

    /**
     * @return array{lel: float, uel: float, employee_rate: float, employer_rate: float}
     */
    public static function getNssfLimitsForDate(?string $date = null): array
    {
        $configured = self::getConfig('nssf_limits');
        if (is_array($configured) && isset($configured['lel'], $configured['uel'])) {
            return [
                'lel' => (float) $configured['lel'],
                'uel' => (float) $configured['uel'],
                'employee_rate' => (float) ($configured['employee_rate'] ?? 0.06),
                'employer_rate' => (float) ($configured['employer_rate'] ?? 0.06),
            ];
        }

        $ref = $date ? substr($date, 0, 10) : now()->format('Y-m-d');
        foreach (self::NSSF_PHASES as $phase) {
            if ($ref >= $phase['effective_from']) {
                return [
                    'lel' => (float) $phase['lel'],
                    'uel' => (float) $phase['uel'],
                    'employee_rate' => 0.06,
                    'employer_rate' => 0.06,
                ];
            }
        }

        $fallback = self::NSSF_PHASES[array_key_last(self::NSSF_PHASES)];

        return [
            'lel' => (float) $fallback['lel'],
            'uel' => (float) $fallback['uel'],
            'employee_rate' => 0.06,
            'employer_rate' => 0.06,
        ];
    }

    public static function getShifRates()
    {
        return self::getConfig('shif_rates', [
            'rate' => self::SHIF_RATE,
            'minimum' => self::SHIF_MINIMUM,
        ]);
    }

    public static function getHousingLevyRate()
    {
        return self::getConfig('housing_levy_rate', self::HOUSING_LEVY_RATE);
    }

    public static function getPersonalRelief()
    {
        return self::getConfig('personal_relief', self::PERSONAL_RELIEF);
    }

    public static function getInsuranceReliefLimit()
    {
        return self::getConfig('insurance_relief_limit', self::INSURANCE_RELIEF_LIMIT);
    }

    public static function getMortgageReliefLimit()
    {
        return self::getConfig('mortgage_relief_limit', self::MORTGAGE_RELIEF_LIMIT);
    }

    public static function getPensionContributionCap()
    {
        return self::getConfig('pension_contribution_cap', self::PENSION_CONTRIBUTION_CAP);
    }
}
