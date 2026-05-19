<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::table('leave_type')->delete();
        DB::table('leave_type')->insert(
            [
                ['leave_type_name' => 'Annual Leave','num_of_day'=>'0','created_at'=>$time,'updated_at'=>$time],
                ['leave_type_name' => 'Casual Leave','num_of_day'=>'22','created_at'=>$time,'updated_at'=>$time],
                ['leave_type_name' => 'Sick Leave','num_of_day'=>'20','created_at'=>$time,'updated_at'=>$time],
                //
                ['leave_type_name' => 'Paternity Leave','num_of_day'=>'20','created_at'=>$time,'updated_at'=>$time],
                ['leave_type_name' => 'Maternity Leave','num_of_day'=>'20','created_at'=>$time,'updated_at'=>$time],
                ['leave_type_name' => 'Off Day','num_of_day'=>'20','created_at'=>$time,'updated_at'=>$time],
                ['leave_type_name' => 'Training','num_of_day'=>'20','created_at'=>$time,'updated_at'=>$time],

            ]
        );
    }
}
