<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        //DB::table('branch')->truncate();
        DB::table('company_settings')->insert(
            [
                ['legal_Name' => 'Test Company', 'legal_Address' => 'Nairobi',
                    'official_contact_number' => 'Nairobi',
                    'official_email' => 'Nairobi',
                    'company_contact_name' => 'Nairobi',
                    'representative_phone' => 'Nairobi',
                    'representative_email' => 'Nairobi',
                    'KRA_PIN' => 'Nairobi',
                    'employer_number' => 'Nairobi',
                    'NSSF_employer_number' => 'Nairobi',
                    'NHIF_employer_code' => 'Nairobi',
                    'financial_year_start' => 'Nairobi',
                    'created_at' => $time, 'updated_at' => $time],

            ]

        );
    }
}
