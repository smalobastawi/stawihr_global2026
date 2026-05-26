<?php


namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run()
    {
        $time = Carbon::now();
        DB::table('user')->delete();
        DB::statement("ALTER TABLE `user` AUTO_INCREMENT = 1");

        DB::table('user')->insert([
            [
                'user_name' => 'StawiHRSuperUser',
                'email' => 'support@stawitech.com',
                'password' => bcrypt('Ex22R2*2025'),
                'remember_token' => Str::random(10),
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => $time,
                'updated_at' => $time,
                'first_name' => 'Super',
                'last_name' => 'Admin'
            ],
            [
                'user_name' => 'SuperAdmin',
                'email' => 'admin@testrunner.co.ke',
                'password' => bcrypt('94opAsT@UM'),
                'remember_token' => Str::random(10),
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => $time,
                'updated_at' => $time,
                'first_name' => 'Super',
                'last_name' => 'Admin'
            ],
        ]);
    }
}
