<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrcreate(['name' => 'SuperAdmin']);
        Permission::firstOrcreate(['name' => 'create-users']);
        Permission::firstOrcreate(['name' => 'edit-users']);
        Permission::firstOrcreate(['name' => 'delete-users']);
        Permission::firstOrcreate(['name' => 'create-employee']);
        Permission::firstOrcreate(['name' => 'edit-employee']);
        Permission::firstOrcreate(['name' => 'delete-employee']);
        //
        Permission::firstOrcreate(['name' => 'changePassword.update']);
        Permission::firstOrcreate(['name' => 'changePassword.index']);
        Permission::firstOrcreate(['name' => 'changePassword.store']);
        Permission::firstOrcreate(['name' => 'myPayroll.myPayroll']);
        Permission::firstOrcreate(['name' => 'applyForLeave.create']);
        Permission::firstOrcreate(['name' => 'applyForLeave.store']);
        Permission::firstOrcreate(['name' => 'ess.leave.scheduled.index']);

        $superAdminRole = Role::firstOrcreate(['name' => 'SuperAdmin']);
        $hrAdmin = Role::firstOrcreate(['name' => 'HR Administrator']);
        $employee = Role::firstOrcreate(['name' => 'Employee']);

        $superAdminRole->givePermissionTo(['SuperAdmin']);
        $employee->givePermissionTo([
            'changePassword.update','changePassword.index','changePassword.store','myPayroll.myPayroll',
            'applyForLeave.create','applyForLeave.store','ess.leave.scheduled.index'
        ]);
        //give default permissions to users
        $stawiSuperUser = User::where('user_name', 'StawiHRSuperUser')->first();
        $SuperAdmin = User::where('user_name', 'SuperAdmin')->first();
        $SuperAdmin->assignRole('SuperAdmin');
        $stawiSuperUser->assignRole('SuperAdmin');


    }

}
