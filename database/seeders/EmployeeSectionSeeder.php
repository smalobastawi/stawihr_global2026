<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::table('employee_sections')->truncate();
        DB::table('employee_sections')->insert([
            [
                'name' => 'Human Resources Section',
                'description' => 'Handles HR operations and employee welfare',
                'section_head_id' => null,
                'location_id' => null,
                'created_at' => $time,
                'updated_at' => $time
            ],
            [
                'name' => 'Finance Section',
                'description' => 'Handles financial operations and accounting',
                'section_head_id' => null,
                'location_id' => null,
                'created_at' => $time,
                'updated_at' => $time
            ],
            [
                'name' => 'IT Section',
                'description' => 'Handles information technology and systems',
                'section_head_id' => null,
                'location_id' => null,
                'created_at' => $time,
                'updated_at' => $time
            ],
            [
                'name' => 'Operations Section',
                'description' => 'Handles day-to-day business operations',
                'section_head_id' => null,
                'location_id' => null,
                'created_at' => $time,
                'updated_at' => $time
            ],
            [
                'name' => 'Marketing Section',
                'description' => 'Handles marketing and communications',
                'section_head_id' => null,
                'location_id' => null,
                'created_at' => $time,
                'updated_at' => $time
            ],
        ]);
    }
}
