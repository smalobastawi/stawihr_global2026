<?php

namespace Database\Seeders;

use App\Models\ModuleDepartment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ModuleDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $moduleDepartments = [
            'Recruitment and Hiring',
            'Employee Management',
            'Attendance and Leave Management',
            'Performance Management',
            'Payroll and Compensation',
            'Expense Management',
            'Training and Development',
            'Separation and Offboarding',
        ];

        foreach ($moduleDepartments as $department) {
            ModuleDepartment::create([
                'name' => $department,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}



