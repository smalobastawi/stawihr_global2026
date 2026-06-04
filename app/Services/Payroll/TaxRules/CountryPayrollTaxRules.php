<?php

namespace App\Services\Payroll\TaxRules;

/**
 * Statutory PAYE bands and social rates sourced from national revenue authorities
 * (RRA, URA, TRA, SARS, NRA South Sudan, Somalia Revenue Directorate, OBR, KRA).
 * Rates should be reviewed when finance acts change.
 */
class CountryPayrollTaxRules
{
    /** Kenya – KRA monthly bands (config may override via PayrollConfiguration). */
    public static function kenyaPayeBands(): array
    {
        return [
            ['min' => 0, 'max' => 24000, 'rate' => 0.10],
            ['min' => 24001, 'max' => 32333, 'rate' => 0.25],
            ['min' => 32334, 'max' => 500000, 'rate' => 0.30],
            ['min' => 500001, 'max' => 800000, 'rate' => 0.325],
            ['min' => 800001, 'max' => null, 'rate' => 0.35],
        ];
    }

    /** Rwanda – RRA monthly PAYE (Income Tax Law No. 027/2022). */
    public static function rwandaPayeBands(): array
    {
        return [
            ['min' => 0, 'max' => 60000, 'rate' => 0.0],
            ['min' => 60001, 'max' => 100000, 'rate' => 0.10],
            ['min' => 100001, 'max' => 200000, 'rate' => 0.20],
            ['min' => 200001, 'max' => null, 'rate' => 0.30],
        ];
    }

    /** Uganda – URA resident monthly chargeable income (2025/26 handbook). */
    public static function ugandaPayeBands(): array
    {
        return [
            ['min' => 0, 'max' => 235000, 'rate' => 0.0],
            ['min' => 235001, 'max' => 335000, 'rate' => 0.10],
            ['min' => 335001, 'max' => 410000, 'rate' => 0.20],
            ['min' => 410001, 'max' => 10000000, 'rate' => 0.30],
        ];
    }

    public const UGANDA_PAYE_SURTAX_THRESHOLD = 10000000;
    public const UGANDA_PAYE_SURTAX_RATE = 0.10;
    public const UGANDA_BAND_FIXED_TAX = [
        335001 => 10000,
        410001 => 25000,
    ];

    /** Tanzania – TRA mainland monthly resident (2025/26). */
    public static function tanzaniaPayeSteps(): array
    {
        return [
            ['up_to' => 270000, 'base' => 0, 'rate' => 0, 'threshold' => 0],
            ['up_to' => 520000, 'base' => 0, 'rate' => 0.08, 'threshold' => 270000],
            ['up_to' => 760000, 'base' => 20000, 'rate' => 0.20, 'threshold' => 520000],
            ['up_to' => 1000000, 'base' => 68000, 'rate' => 0.25, 'threshold' => 760000],
            ['up_to' => null, 'base' => 128000, 'rate' => 0.30, 'threshold' => 1000000],
        ];
    }

    /** South Sudan – NRA FY 2023/24 personal income tax (unchanged per 2024/25 Financial Act). */
    public static function southSudanPayeBands(): array
    {
        return [
            ['min' => 0, 'max' => 20000, 'rate' => 0.0],
            ['min' => 20001, 'max' => 40000, 'rate' => 0.05],
            ['min' => 40001, 'max' => 57000, 'rate' => 0.10],
            ['min' => 57001, 'max' => 90000, 'rate' => 0.15],
            ['min' => 90001, 'max' => null, 'rate' => 0.20],
        ];
    }

    /** Somalia – Law No. 5/1966 payroll tax (USD monthly brackets). */
    public static function somaliaPayeBands(): array
    {
        return [
            ['min' => 0, 'max' => 200, 'rate' => 0.0],
            ['min' => 201, 'max' => 800, 'rate' => 0.06],
            ['min' => 801, 'max' => 1500, 'rate' => 0.12],
            ['min' => 1501, 'max' => null, 'rate' => 0.18],
        ];
    }

    /** Burundi – OBR progressive IPR (annual bands / 12). */
    public static function burundiPayeBands(): array
    {
        return [
            ['min' => 0, 'max' => 150000, 'rate' => 0.0],
            ['min' => 150001, 'max' => 300000, 'rate' => 0.20],
            ['min' => 300001, 'max' => null, 'rate' => 0.30],
        ];
    }

    /** South Africa – SARS 2025/26 annual brackets (applied to monthly taxable × 12). */
    public static function southAfricaAnnualTaxBrackets(): array
    {
        return [
            ['up_to' => 237100, 'base' => 0, 'rate' => 0.18, 'threshold' => 0],
            ['up_to' => 370500, 'base' => 42678, 'rate' => 0.26, 'threshold' => 237100],
            ['up_to' => 512800, 'base' => 77362, 'rate' => 0.31, 'threshold' => 370500],
            ['up_to' => 673000, 'base' => 121475, 'rate' => 0.36, 'threshold' => 512800],
            ['up_to' => 857900, 'base' => 179147, 'rate' => 0.39, 'threshold' => 673000],
            ['up_to' => 1817000, 'base' => 251258, 'rate' => 0.41, 'threshold' => 857900],
            ['up_to' => null, 'base' => 644489, 'rate' => 0.45, 'threshold' => 1817000],
        ];
    }

    public static function southAfricaTaxRebates(): array
    {
        return [
            'primary' => 17235,
            'secondary' => 9444,
            'tertiary' => 3145,
        ];
    }
}
