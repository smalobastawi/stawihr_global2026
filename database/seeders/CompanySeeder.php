<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;
use Carbon\Carbon;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        Company::truncate();

        $companies = [
            [
                'name' => 'StawiTech Solutions',
                'domain' => 'stawitech.com',
                'country' => 'United States',
                'status' => 'active',
                'company_id' => null, // Parent company
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],


        ];

        // Insert main companies
        foreach ($companies as $companyData) {
            $company = Company::create($companyData);
        }
    }
}
