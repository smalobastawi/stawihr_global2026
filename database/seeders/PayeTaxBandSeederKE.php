<?php

namespace Database\Seeders;

use App\Lib\Enumerations\Nationality;
use App\Models\PayeTaxBand;
use Illuminate\Database\Seeder;

class PayeTaxBandSeederKE extends Seeder
{
    /**
     * KRA Individual Income Tax bands (Finance Act 2023 — current PAYE guide).
     * Monthly: first 24,000 @10%; next 8,333 @25%; next 467,667 @30%;
     * next 300,000 @32.5%; above 800,000 @35%.
     */
    public function run()
    {
        $countryId = Nationality::getValue('Kenya');

        PayeTaxBand::query()->where('country_id', $countryId)->delete();

        $kenyaBands = [
            [
                'country_id' => $countryId,
                'country_name' => 'Kenya',
                'band_order' => 1,
                'monthly_lower_bound' => 0,
                'monthly_upper_bound' => 24000,
                'annual_lower_bound' => 0,
                'annual_upper_bound' => 288000,
                'tax_rate' => 10.0,
            ],
            [
                'country_id' => $countryId,
                'country_name' => 'Kenya',
                'band_order' => 2,
                'monthly_lower_bound' => 24001,
                'monthly_upper_bound' => 32333,
                'annual_lower_bound' => 288001,
                'annual_upper_bound' => 388000,
                'tax_rate' => 25.0,
            ],
            [
                'country_id' => $countryId,
                'country_name' => 'Kenya',
                'band_order' => 3,
                'monthly_lower_bound' => 32334,
                'monthly_upper_bound' => 500000,
                'annual_lower_bound' => 388001,
                'annual_upper_bound' => 6000000,
                'tax_rate' => 30.0,
            ],
            [
                'country_id' => $countryId,
                'country_name' => 'Kenya',
                'band_order' => 4,
                'monthly_lower_bound' => 500001,
                'monthly_upper_bound' => 800000,
                'annual_lower_bound' => 6000001,
                'annual_upper_bound' => 9600000,
                'tax_rate' => 32.5,
            ],
            [
                'country_id' => $countryId,
                'country_name' => 'Kenya',
                'band_order' => 5,
                'monthly_lower_bound' => 800001,
                'monthly_upper_bound' => null,
                'annual_lower_bound' => 9600001,
                'annual_upper_bound' => null,
                'tax_rate' => 35.0,
            ],
        ];

        PayeTaxBand::insert($kenyaBands);
    }
}
