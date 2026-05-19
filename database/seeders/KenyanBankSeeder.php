<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KenyanBankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $banks = [
            ['bank_code' => '01', 'name' => 'Kenya Commercial Bank Limited'],
            ['bank_code' => '02', 'name' => 'Standard Chartered Bank Kenya Ltd'],
            ['bank_code' => '03', 'name' => 'Absa Bank Kenya Plc'],
            ['bank_code' => '05', 'name' => 'Bank of India'],
            ['bank_code' => '06', 'name' => 'Bank of Baroda(Kenya Ltd)'],
            ['bank_code' => '07', 'name' => 'NCBA Bank Kenya Plc'],
            ['bank_code' => '09', 'name' => 'Central Bank Of Kenya'],
            ['bank_code' => '10', 'name' => 'Prime Bank Limited'],
            ['bank_code' => '11', 'name' => 'Co-operative Bank of Kenya Limited'],
            ['bank_code' => '12', 'name' => 'National Bank Of Kenya'],
            ['bank_code' => '14', 'name' => 'M-Oriental Bank Limited'],
            ['bank_code' => '16', 'name' => 'CITI BANK N A'],
            ['bank_code' => '17', 'name' => 'Habib Bank AG Zurich'],
            ['bank_code' => '18', 'name' => 'Middle East Bank Kenya Limited'],
            ['bank_code' => '19', 'name' => 'Bank of Africa Kenya Limited'],
            ['bank_code' => '23', 'name' => 'Consolidated Bank of Kenya Ltd'],
            ['bank_code' => '25', 'name' => 'Credit Bank PLC'],
            ['bank_code' => '26', 'name' => 'Access Bank Kenya PLC'],
            ['bank_code' => '31', 'name' => 'Stanbic Bank Plc'],
            ['bank_code' => '35', 'name' => 'African Banking Corporation Limited'],
            ['bank_code' => '43', 'name' => 'ECO Bank Kenya LTD'],
            ['bank_code' => '49', 'name' => 'Spire Bank'],
            ['bank_code' => '50', 'name' => 'Paramount Bank Limited'],
            ['bank_code' => '51', 'name' => 'Kingdom Bank Limited'],
            ['bank_code' => '53', 'name' => 'Guaranty Trust Bank ( Kenya) Ltd'],
            ['bank_code' => '54', 'name' => 'Victoria Commercial Bank'],
            ['bank_code' => '55', 'name' => 'Guardian Bank Limited'],
            ['bank_code' => '57', 'name' => 'I&M BANK (KENYA) LTD'],
            ['bank_code' => '59', 'name' => 'Development Bank of Kenya Limited'],
            ['bank_code' => '60', 'name' => 'State Bank of Mauritius Kenya'],
            ['bank_code' => '61', 'name' => 'Housing Finance Bank'],
            ['bank_code' => '62', 'name' => 'Kenya Post Office Savings Bank'],
            ['bank_code' => '63', 'name' => 'Diamond Trust Bank Limited'],
            ['bank_code' => '65', 'name' => 'Commercial International Bank (CIB) Kenya'],
            ['bank_code' => '66', 'name' => 'Sidian Bank Limited'],
            ['bank_code' => '68', 'name' => 'Equity Bank Limited'],
            ['bank_code' => '70', 'name' => 'Family Bank Ltd'],
            ['bank_code' => '72', 'name' => 'Gulf African Bank Ltd'],
            ['bank_code' => '74', 'name' => 'Premier Bank Kenya Limited'],
            ['bank_code' => '75', 'name' => 'DIB Bank Kenya Limited'],
            ['bank_code' => '76', 'name' => 'UBA Kenya Bank Ltd'],
            ['bank_code' => '78', 'name' => 'Kenya Women Microfinance Bank'],
            ['bank_code' => '79', 'name' => 'Faulu Microfinance Bank Ltd'],
            ['bank_code' => '80', 'name' => 'Caritas Microfinance Bank Limited'],
        ];

        foreach ($banks as $bank) {
            DB::table('banks')->insert([
                'bank_code' => $bank['bank_code'],
                'name' => $bank['name'],
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}