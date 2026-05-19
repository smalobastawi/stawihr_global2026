<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MorphoDevicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::table('morpho_devices')->truncate();
        DB::table('morpho_devices')->insert(
            [
                ['device_ip_address' => '192.168.140.87','device_serial'=>'1902I009651','port'=>'11010','timeout'=>'500','created_at'=>$time,'updated_at'=>$time],
                ['device_ip_address' => '192.168.1.76','device_serial'=>'0683468','port'=>'11010','timeout'=>'500','created_at'=>$time,'updated_at'=>$time],
            ]
        );
    }
}
