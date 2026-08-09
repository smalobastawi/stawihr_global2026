<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxRuleSeeder extends Seeder
{
    /**
     * Legacy tax_rule table — aligned to KRA monthly PAYE bands (Finance Act 2023).
     */
    public function run()
    {
        $time = Carbon::now();
        DB::table('tax_rule')->truncate();
        DB::table('tax_rule')->insert([
            ['min_amount' => 0, 'max_amount' => 24000, 'percentage_of_tax' => 10, 'gender' => 'Male', 'created_at' => $time, 'updated_at' => $time],
            ['min_amount' => 24001, 'max_amount' => 32333, 'percentage_of_tax' => 25, 'gender' => 'Male', 'created_at' => $time, 'updated_at' => $time],
            ['min_amount' => 32334, 'max_amount' => 500000, 'percentage_of_tax' => 30, 'gender' => 'Male', 'created_at' => $time, 'updated_at' => $time],
            ['min_amount' => 500001, 'max_amount' => 800000, 'percentage_of_tax' => 32.5, 'gender' => 'Male', 'created_at' => $time, 'updated_at' => $time],
            ['min_amount' => 800001, 'max_amount' => 100000000, 'percentage_of_tax' => 35, 'gender' => 'Male', 'created_at' => $time, 'updated_at' => $time],
        ]);
    }
}
