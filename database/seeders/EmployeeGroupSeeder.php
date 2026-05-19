<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::table('employee_groups')->insert(
            [
                ['name' => 'Default Group', 'description' => 'Default employee group', 'location_id' => 1, 'created_at' => $time, 'updated_at' => $time],
            ]

        );
    }
}
