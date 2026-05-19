<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkShifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::table('work_shift')->insert(
            [
                ['shift_name' => 'Normal Shift','start_time' => '07:30:00', 'end_time' => '16:30:00','late_count_time'=>'07:50:00','overtime_count_time'=>'16:50:00','created_at' => $time, 'updated_at' => $time]
            ]
        );
    }
}
