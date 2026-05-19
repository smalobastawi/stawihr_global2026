<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::table('department')->truncate();
        DB::table('department')->insert(
            [
                ['department_name' => 'Human Resource','created_at'=>$time,'updated_at'=>$time],
            ]

        );
    }
}
