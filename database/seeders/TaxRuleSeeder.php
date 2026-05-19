<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TaxRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::table('tax_rule')->truncate();
        DB::table('tax_rule')->insert(
            [
                ['min_amount' => 0,'max_amount' => 24000,'percentage_of_tax'=>0,'gender'=>'Male','created_at'=>$time,'updated_at'=>$time],
                ['min_amount' => 24000,'max_amount' => 32333,'percentage_of_tax'=>10,'gender'=>'Male','created_at'=>$time,'updated_at'=>$time],
                ['min_amount' => 32334,'max_amount' => 50000,'percentage_of_tax'=>25,'gender'=>'Male','created_at'=>$time,'updated_at'=>$time],
                ['min_amount' => 500001,'max_amount' => 800000,'percentage_of_tax'=>32.5,'gender'=>'Male','created_at'=>$time,'updated_at'=>$time],
                ['min_amount' => 800001,'max_amount' => 100000000,'percentage_of_tax'=>35,'gender'=>'Male','created_at'=>$time,'updated_at'=>$time],

                ]

        );
    }
}
