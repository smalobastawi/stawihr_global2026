<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::table('location')->updateOrInsert(
            ['location_name' => 'Nairobi'],
            ['created_at' => $time, 'updated_at' => $time]
        );
    }
}