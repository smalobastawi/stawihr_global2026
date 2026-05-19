<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeaveGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();

        DB::table('leave_groups')->truncate();
        DB::table('leave_groups')->insert([
            [
                'name' => 'Male Group',
                'description' => 'Leave group for male employees',
                'is_active' => true,
                'created_at' => $time,
                'updated_at' => $time,
            ],
            [
                'name' => 'Female Group',
                'description' => 'Leave group for female employees',
                'is_active' => true,
                'created_at' => $time,
                'updated_at' => $time,
            ],
        ]);
    }
}
