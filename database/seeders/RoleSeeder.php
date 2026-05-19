<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $time = Carbon::now();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('roles')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('roles')->insert([
            ['name' => 'SuperAdmin', 'guard_name' => 'web', 'created_at' => $time, 'updated_at' => $time],
            ['name' => 'HR Administrator', 'guard_name' => 'web', 'created_at' => $time, 'updated_at' => $time],
            ['name' => 'Employee', 'guard_name' => 'web', 'created_at' => $time, 'updated_at' => $time],
            ['name' => 'General Supervisors', 'guard_name' => 'web', 'created_at' => $time, 'updated_at' => $time],
            ['name' => 'ICT Support', 'guard_name' => 'web', 'created_at' => $time, 'updated_at' => $time],
            ['name' => 'Finance Admins', 'guard_name' => 'web', 'created_at' => $time, 'updated_at' => $time],
        ]);

        // Assign SuperAdmin role to user 1 and 2
        DB::table('model_has_roles')->insert([
            ['role_id' => 1, 'model_type' => 'App\Models\User', 'model_id' => 1],
            ['role_id' => 1, 'model_type' => 'App\Models\User', 'model_id' => 2],
        ]);
    }
}
