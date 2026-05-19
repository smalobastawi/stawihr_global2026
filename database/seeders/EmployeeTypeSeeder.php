<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::table('employee_types')->insert(
            [
                ['name' => 'Permanent','description'=>'Permanent Employees','created_at'=>$time,'updated_at'=>$time],
                ['name' => 'Casual','description'=>'Casual Employees','created_at'=>$time,'updated_at'=>$time],
            ]

        );
    }
}
