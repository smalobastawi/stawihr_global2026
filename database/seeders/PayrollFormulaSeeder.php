<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PayrollFormula;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PayrollFormulaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Schema::hasTable('paye_tax_bands')) {
            $kenya = DB::table('paye_tax_bands')->where('country_name', 'Kenya')->first();

            if ($kenya) {
                PayrollFormula::updateOrCreate(
                    ['name' => 'PAYE', 'country_id' => $kenya->country_id],
                    ['formula' => '(gross_salary - nssf) > 24000 ? ( ( (gross_salary - nssf) - 24000 > 8333 ? ( ( (gross_salary - nssf) - 32333 > 435334 ? ( ( (gross_salary - nssf) - 467667 > 332333 ? (2400 + 2083.25 + 130600.2 + ( (gross_salary - nssf) - 800000) * 0.35) : (2400 + 2083.25 + ( (gross_salary - nssf) - 32333) * 0.3) ) : (2400 + 2083.25 + ( (gross_salary - nssf) - 32333) * 0.3) ) : (2400 + ( (gross_salary - nssf) - 24000) * 0.25) ) ) : ( (gross_salary - nssf) * 0.1) ) - 2400']
                );

                PayrollFormula::updateOrCreate(
                    ['name' => 'NSSF', 'country_id' => $kenya->country_id],
                    ['formula' => 'min( (gross_salary > 7000 ? ( (gross_salary - 7000) * 0.06 + 420 ) : (gross_salary * 0.06) ), 2160)']
                );

                PayrollFormula::updateOrCreate(
                    ['name' => 'SHIF', 'country_id' => $kenya->country_id],
                    ['formula' => 'gross_salary * 0.0275']
                );

                PayrollFormula::updateOrCreate(
                    ['name' => 'AHL', 'country_id' => $kenya->country_id],
                    ['formula' => 'gross_salary * 0.015']
                );
            }
        }
    }
}
