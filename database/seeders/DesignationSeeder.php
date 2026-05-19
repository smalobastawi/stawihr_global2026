<?php


namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::table('designation')->truncate();
        DB::table('designation')->insert(
            [
                ['designation_name' => 'Admin','created_at'=>$time,'updated_at'=>$time],

            ]

        );
    }
}
