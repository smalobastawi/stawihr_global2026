<?php

namespace App\Services;

use App\Models\PayeTaxBand;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PayeTaxCalculator
{
    /**
     * Calculate monthly PAYE tax for a given country and income
     *
     * @param string $countryCode
     * @param float $monthlyIncome
     * @return float
     * @throws InvalidArgumentException
     */
    public function calculateMonthlyTax(string $countryCode, float $monthlyIncome): float
    {
        $this->validateInputs($countryCode, $monthlyIncome);

        $taxBands = $this->getTaxBandsForCountry($countryCode);
        $remainingIncome = $monthlyIncome;
        $totalTax = 0.0;

        foreach ($taxBands as $band) {
            if ($remainingIncome <= 0) {
                break;
            }

            $taxableAmount = $this->calculateTaxableAmount(
                $remainingIncome,
                $band->monthly_lower_bound,
                $band->monthly_upper_bound
            );

            if ($taxableAmount > 0) {
                $totalTax += $this->calculateBandTax($taxableAmount, $band->tax_rate);
                $remainingIncome -= $taxableAmount;
            }
        }

        return round($totalTax, 2);
    }

    /**
     * Calculate annual PAYE tax for a given country and income
     *
     * @param string $countryCode
     * @param float $annualIncome
     * @return float
     * @throws InvalidArgumentException
     */
    public function calculateAnnualTax(string $countryCode, float $annualIncome): float
    {
        $this->validateInputs($countryCode, $annualIncome);

        $taxBands = $this->getTaxBandsForCountry($countryCode);
        $remainingIncome = $annualIncome;
        $totalTax = 0.0;

        foreach ($taxBands as $band) {
            if ($remainingIncome <= 0) {
                break;
            }

            $taxableAmount = $this->calculateTaxableAmount(
                $remainingIncome,
                $band->annual_lower_bound,
                $band->annual_upper_bound
            );

            if ($taxableAmount > 0) {
                $totalTax += $this->calculateBandTax($taxableAmount, $band->tax_rate);
                $remainingIncome -= $taxableAmount;
            }
        }

        return round($totalTax, 2);
    }

    /**
     * Get all tax bands for a country
     *
     * @param string $countryCode
     * @return array
     * @throws InvalidArgumentException
     */
    public function getTaxBands(string $countryCode): array
    {
        $taxBands = $this->getTaxBandsForCountry($countryCode);

        return $taxBands->map(function ($band) {
            return [
                'monthly_lower' => $band->monthly_lower_bound,
                'monthly_upper' => $band->monthly_upper_bound,
                'annual_lower' => $band->annual_lower_bound,
                'annual_upper' => $band->annual_upper_bound,
                'rate' => $band->tax_rate,
                'band_order' => $band->band_order,
            ];
        })->toArray();
    }

    /**
     * Add tax bands for a country
     *
     * @param string $countryCode
     * @param string $countryName
     * @param array $bands
     * @return void
     * @throws InvalidArgumentException
     */
    public function addCountryTaxBands(int $countryCode, string $countryName, array $bands): void
    {
        
        $this->validateBandsInput($countryCode, $bands);

        $bandsToInsert = $this->prepareBandsForInsert($countryCode, $countryName, $bands);

        DB::transaction(function () use ($bandsToInsert) {
            PayeTaxBand::insert($bandsToInsert);
        });
    }

    /**
     * Update existing tax bands for a country
     *
     * @param string $countryCode
     * @param array $bands
     * @return void
     * @throws InvalidArgumentException
     */
    public function updateCountryTaxBands(string $countryCode, array $bands): void
    {
        $this->validateBandsInput($countryCode, $bands);

        DB::transaction(function () use ($countryCode, $bands) {
            foreach ($bands as $band) {
                if (empty($band['id'])) {
                    throw new InvalidArgumentException("Band ID is required for updates");
                }

                PayeTaxBand::where('id', $band['id'])
                    ->where('country_id', $countryCode)
                    ->update([
                        'monthly_lower_bound' => $band['monthly_lower'],
                        'monthly_upper_bound' => $band['monthly_upper'] ?? null,
                        'annual_lower_bound' => $band['annual_lower'],
                        'annual_upper_bound' => $band['annual_upper'] ?? null,
                        'tax_rate' => $band['rate'],
                        'updated_at' => now(),
                    ]);
            }
        });
    }

    /**
     * Delete all tax bands for a country
     *
     * @param string $countryCode
     * @return void
     */
    public function deleteCountryTaxBands(int $countryCode): void
    {
        PayeTaxBand::where('country_id', $countryCode)->delete();
    }

    /**
     * Validate input parameters
     *
     * @param string $countryCode
     * @param float $income
     * @throws InvalidArgumentException
     */
    private function validateInputs(string $countryCode, float $income): void
    {
        if (empty($countryCode)) {
            throw new InvalidArgumentException("Country code is required");
        }

        if ($income < 0) {
            throw new InvalidArgumentException("Income cannot be negative");
        }
    }

    /**
     * Validate bands input structure
     *
     * @param string|int $countryCode
     * @param array $bands
     * @throws InvalidArgumentException
     */
    private function validateBandsInput($countryCode, array $bands): void
    {
        if (empty($countryCode) && $countryCode !== 0 && $countryCode !== '0') {
            throw new InvalidArgumentException("Country code is required");
        }

        if (empty($bands)) {
            throw new InvalidArgumentException("At least one tax band is required");
        }

        foreach ($bands as $index => $band) {
            // Check monthly_lower exists and is numeric (0 is a valid value, so use isset + strict check)
            if (!isset($band['monthly_lower']) || $band['monthly_lower'] === '' || $band['monthly_lower'] === null) {
                throw new InvalidArgumentException("Row " . ($index + 1) . ": Monthly lower bound is required");
            }
            if (!is_numeric($band['monthly_lower'])) {
                throw new InvalidArgumentException("Row " . ($index + 1) . ": Monthly lower bound must be numeric");
            }

            // Check annual_lower exists and is numeric
            if (!isset($band['annual_lower']) || $band['annual_lower'] === '' || $band['annual_lower'] === null) {
                throw new InvalidArgumentException("Row " . ($index + 1) . ": Annual lower bound is required");
            }
            if (!is_numeric($band['annual_lower'])) {
                throw new InvalidArgumentException("Row " . ($index + 1) . ": Annual lower bound must be numeric");
            }

            // Check tax rate exists and is numeric (0 is valid for non-taxable bands)
            if (!isset($band['rate']) || $band['rate'] === '' || $band['rate'] === null) {
                throw new InvalidArgumentException("Row " . ($index + 1) . ": Tax rate is required");
            }
            if (!is_numeric($band['rate'])) {
                throw new InvalidArgumentException("Row " . ($index + 1) . ": Tax rate must be numeric");
            }
        }
    }

    /**
     * Get tax bands for a country ordered by band order
     *
     * @param string|int $countryCode
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws InvalidArgumentException
     */
    private function getTaxBandsForCountry($countryCode)
    {
        $taxBands = PayeTaxBand::where('country_id', $countryCode)
            ->orderBy('band_order')
            ->get();

        if ($taxBands->isEmpty()) {
            throw new InvalidArgumentException("Country ID {$countryCode} not supported");
        }

        return $taxBands;
    }

    /**
     * Calculate taxable amount for a band
     *
     * @param float $remainingIncome
     * @param float $lowerBound
     * @param float|null $upperBound
     * @return float
     */
    private function calculateTaxableAmount(float $remainingIncome, float $lowerBound, ?float $upperBound): float
    {
        if (is_null($upperBound)) {
            return max(0, $remainingIncome - $lowerBound + 1);
        }

        $bandWidth = $upperBound - $lowerBound + 1;
        return min($bandWidth, max(0, $remainingIncome - $lowerBound + 1));
    }

    /**
     * Calculate tax for a single band
     *
     * @param float $taxableAmount
     * @param float $taxRate
     * @return float
     */
    private function calculateBandTax(float $taxableAmount, float $taxRate): float
    {
        return $taxableAmount * ($taxRate / 100);
    }

    /**
     * Prepare bands data for database insertion
     *
     * @param string $countryCode
     * @param string $countryName
     * @param array $bands
     * @return array
     */
    private function prepareBandsForInsert(int $countryID, string $countryName, array $bands): array
    {
        $bandsToInsert = [];
        $bandOrder = 1;

        foreach ($bands as $band) {
            $bandsToInsert[] = [
                'country_id' => $countryID,
                'country_name' => $countryName,
                'band_order' => $bandOrder++,
                'monthly_lower_bound' => $band['monthly_lower'],
                'monthly_upper_bound' => $band['monthly_upper'] ?? null,
                'annual_lower_bound' => $band['annual_lower'],
                'annual_upper_bound' => $band['annual_upper'] ?? null,
                'tax_rate' => $band['rate'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return $bandsToInsert;
    }
}