<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VehicleManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        // Add menu items for Vehicle Management
        $menus = [
            // Main menu group
            ['parent_id' => 0, 'action' => 'pmMenu__Vehicle Management', 'name' => 'Vehicle Management', 'menu_url' => null, 'module_id' => '12', 'status' => 1],
            // Sub menus
            ['parent_id' => 0, 'action' => 'vehicle.index', 'name' => 'Vehicles', 'menu_url' => 'vehicle.index', 'module_id' => '12', 'status' => 1],
            ['parent_id' => 0, 'action' => 'vehicle.assignment.index', 'name' => 'Assignment History', 'menu_url' => 'vehicle.assignment.index', 'module_id' => '12', 'status' => 1],
        ];

        // Check if menu already exists to avoid duplicates
        foreach ($menus as $menu) {
            $exists = DB::table('menus')->where('action', $menu['action'])->first();
            if (!$exists) {
                DB::table('menus')->insert($menu);
            }
        }

        // Add permissions for Vehicle Management
        $permissions = [
            // Vehicle permissions
            ['name' => 'vehicle.index', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'vehicle.create', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'vehicle.store', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'vehicle.edit', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'vehicle.update', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'vehicle.delete', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'vehicle.show', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'vehicle.import', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'vehicle.download_template', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'vehicle.assign_driver', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'vehicle.unassign_driver', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            // Assignment permissions
            ['name' => 'vehicle.assignment.index', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'vehicle.assignment.vehicle_history', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'vehicle.assignment.employee_history', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
            // Menu permission
            ['name' => 'pmMenu__Vehicle Management', 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now],
        ];

        // Check if permission already exists to avoid duplicates
        foreach ($permissions as $permission) {
            $exists = DB::table('permissions')->where('name', $permission['name'])->first();
            if (!$exists) {
                DB::table('permissions')->insert($permission);
            }
        }

        $this->command->info('Vehicle Management menu items and permissions seeded successfully!');
    }
}
