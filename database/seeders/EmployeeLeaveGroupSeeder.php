<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EmployeeLeaveGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();

        // Get all employees and assign them to leave groups based on gender
        $employees = DB::table('employee')->select('employee_id', 'gender')->get();

        $leaveGroupAssignments = [];

        foreach ($employees as $employee) {
            // Determine leave_group_id based on gender
            // Male employees -> leave_group_id = 1
            // Female employees -> leave_group_id = 2
            if (strtolower($employee->gender) === 'male') {
                $leaveGroupId = 1;
            } elseif (strtolower($employee->gender) === 'female') {
                $leaveGroupId = 2;
            } else {
                // For any other gender value, assign to 'all' or skip
                // Default to male group if gender is not specified
                continue;
            }

            $leaveGroupAssignments[] = [
                'leave_group_id' => $leaveGroupId,
                'employee_id' => $employee->employee_id,
                'created_at' => $time,
                'updated_at' => $time,
            ];
        }

        // Insert assignments (truncate first to avoid duplicates)
        if (!empty($leaveGroupAssignments)) {
            DB::table('employee_leavegroups')->truncate();
            DB::table('employee_leavegroups')->insert($leaveGroupAssignments);
        }
    }
}
