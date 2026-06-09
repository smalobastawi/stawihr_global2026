<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
       // Artisan::call('permission:create-permission-routes'); // Create permissions for all routes

        $this->call(CompanySeeder::class);
        $this->call(RegionSeeder::class);
        $this->call(LocationSeeder::class);
        $this->call(DepartmentSeeder::class);
        $this->call(DesignationSeeder::class);
        $this->call(EmployeeTypeSeeder::class);

       
        $this->call(WorkShifSeeder::class);

        // Leave and attendance
        $this->call(LeaveTypeSeeder::class);
        $this->call(LeaveGroupSeeder::class);
        $this->call(LeaveGroupSettingSeeder::class);
        $this->call(EmployeeLeaveGroupSeeder::class);
        // $this->call(PublicHolidaysSeeder::class);

        // Payroll and financial
        $this->call(FinancialYearSeeder::class);
        $this->call(PayrollFormulaSeeder::class);
        $this->call(PayrollPeriodSeeder::class);

        // Roles
        $this->call(RoleSeeder::class);

        // Users and roles
        $this->call(UserSeeder::class);
        $this->call(EmployeeSeeder::class);
        $this->call(EthnicitySeeder::class);

        // Performance Management
        $this->call(PerformanceSeeder::class);

        // PIP (Performance Improvement Plan)
        $this->call(PipSeeder::class);

        // Frontend settings
        $this->call(FrontSettingsSeeder::class);
        Artisan::call('permission:create-permission-routes'); // Create permissions for all routes

        Schema::enableForeignKeyConstraints();
    }
}
