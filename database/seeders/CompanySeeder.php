<?php

namespace Database\Seeders;

use App\Lib\Enumerations\PayrollCountry;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::truncate();

        Company::create([
            'name' => 'StawiTech Solutions',
            'domain' => 'stawitech.com',
            'country' => 'Kenya',
            'payroll_country' => PayrollCountry::KENYA,
            'status' => 'active',
            'address' => 'Westlands Business Park, Waiyaki Way, Nairobi',
            'official_contact_number' => '254712345678',
            'official_email' => 'hr@stawitech.com',
            'company_contact_name' => 'John Doe',
            'representative_phone' => '254712345678',
            'representative_email' => 'john.doe@stawitech.com',
            'print_head_description' => '<strong>StawiTech Solutions</strong><br>Westlands Business Park, Nairobi',
            'kra_pin' => 'P01111111A',
            'registration_number' => 'CPR/2020/123456',
            'nssf_employer_number' => '11111111',
            'shif_employer_code' => '1111111111',
            'employer_number' => '11111111',
            'nita_registration_number' => 'NITA123456',
            'ecitizen_identifier' => 'ECI-123456',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
