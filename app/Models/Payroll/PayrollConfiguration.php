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
        'updated_by'
    ];

    protected $casts = [
        'config_value' => 'json',
        'effective_date' => 'date',
        'is_active' => 'boolean'
    ];

    // Kenyan Payroll Configuration Constants
    const PAYE_BANDS = [
        ['min' => 0, 'max' => 24000, 'rate' => 0.10],
        ['min' => 24001, 'max' => 32333, 'rate' => 0.25],
        ['min' => 32334, 'max' => 500000, 'rate' => 0.30],
        ['min' => 500001, 'max' => 800000, 'rate' => 0.325],
        ['min' => 800001, 'max' => null, 'rate' => 0.35]
    ];

    const NSSF_RATES = [
        'tier_1' => ['min' => 0, 'max' => 7000, 'employee_rate' => 0.06, 'employer_rate' => 0.06],
        'tier_2' => ['min' => 7001, 'max' => 36000, 'employee_rate' => 0.06, 'employer_rate' => 0.06]
    ];



    const HOUSING_LEVY_RATE = 0.015; // 1.5% of gross salary

    // Personal Relief and other allowances
    const PERSONAL_RELIEF = 2400; // Monthly personal relief
    const INSURANCE_RELIEF_LIMIT = 5000; // Monthly insurance relief limit
    const MORTGAGE_RELIEF_LIMIT = 25000; // Monthly mortgage interest relief limit

    /**
     * Get configuration value by key
     */
    public static function getConfig($key, $default = null)
    {
        $config = self::where('config_key', $key)
                     ->where('is_active', true)
                     ->where('effective_date', '<=', now())
                     ->orderBy('effective_date', 'desc')
                     ->first();

        return $config ? $config->config_value : $default;
    }

    /**
     * Set configuration value
     */
    public static function setConfig($key, $value, $description = null, $effectiveDate = null)
    {
        return self::create([
            'config_key' => $key,
            'config_value' => $value,
            'config_type' => gettype($value),
            'description' => $description,
            'effective_date' => $effectiveDate ?? now(),
            'is_active' => true,
            'created_by' => auth()->id()
        ]);
    }

    /**
     * Get PAYE tax bands
     */
    public static function getPayeBands()
    {
        return self::getConfig('paye_bands', self::PAYE_BANDS);
    }

    /**
     * Get NSSF rates
     */
    public static function getNssfRates()
    {
        return self::getConfig('nssf_rates', self::NSSF_RATES);
    }

    /**
     * Get SHIF rates
     */
    public static function getShifRates()
    {
        return self::getConfig('shif_rates', null);
    }

    /**
     * Get Housing Levy rate
     */
    public static function getHousingLevyRate()
    {
        return self::getConfig('housing_levy_rate', self::HOUSING_LEVY_RATE);
    }

    /**
     * Get Personal Relief amount
     */
    public static function getPersonalRelief()
    {
        return self::getConfig('personal_relief', self::PERSONAL_RELIEF);
    }
}