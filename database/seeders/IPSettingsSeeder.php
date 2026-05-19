<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IPSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::table('ip_settings')->truncate();
        DB::table('ip_settings')->insert(
            [
                ['ip_address' => '127.0.0.1', 'ip_status'=>'0', 'status'=>'0','created_at'=>$time,'updated_at'=>$time],

            ]

        );
    }
}
