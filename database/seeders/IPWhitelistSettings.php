<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class IPWhitelistSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::table('white_listed_ips')->truncate();
        DB::table('white_listed_ips')->insert(
            [
                ['ip_setting_id' => '1', 'white_listed_ip'=>'127.0.0.1','created_at'=>$time,'updated_at'=>$time],

            ]

        );
    }
}
