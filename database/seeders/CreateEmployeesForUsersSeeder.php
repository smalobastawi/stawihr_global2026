<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreateEmployeesForUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder creates employee records for users who don't have linked employees.
     * It checks by email first, and if no employee exists with that email, creates one.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Starting to create employee records for users without linked employees...');

        $time = Carbon::now();

        // Get the first company
        $firstCompany = DB::table('companies')->first();

        if (!$firstCompany) {
            $this->command->error('No company found. Please run CompanySeeder first.');
            return;
        }

        $companyId = $firstCompany->id;
        $this->command->info("Assigning new employees to company: {$firstCompany->name} (ID: {$companyId})");

        // Get default department, designation, location, work_shift
        $departmentId = DB::table('department')->value('department_id') ?? 1;
        $designationId = DB::table('designation')->value('designation_id') ?? 1;
        $locationId = DB::table('location')->value('location_id') ?? 1;
        $workShiftId = DB::table('work_shift')->value('work_shift_id') ?? 1;

        // Get all users without linked employees
        $usersWithoutEmployees = DB::table('user')
            ->leftJoin('employee', 'user.id', '=', 'employee.user_id')
            ->whereNull('employee.user_id')
            ->select('user.*')
            ->get();

        if ($usersWithoutEmployees->isEmpty()) {
            $this->command->info('All users already have linked employee records.');
            return;
        }

        $createdCount = 0;
        $linkedCount = 0;

        foreach ($usersWithoutEmployees as $user) {
            // First, check if an employee with this email already exists (but not linked)
            $existingEmployee = DB::table('employee')
                ->where('email', $user->email)
                ->first();

            if ($existingEmployee) {
                // Link existing employee to this user
                DB::table('employee')
                    ->where('employee_id', $existingEmployee->employee_id)
                    ->update(['user_id' => $user->id]);

                $this->command->info("Linked existing employee {$existingEmployee->employee_id} to user {$user->email}");
                $linkedCount++;
                continue;
            }

            // Generate a unique payroll number
            $maxPayrollNumber = DB::table('employee')
                ->where('payroll_number', 'LIKE', 'EMP%')
                ->orderByRaw('CAST(SUBSTRING(payroll_number, 4) AS UNSIGNED) DESC')
                ->value('payroll_number');

            $nextNumber = 1;
            if ($maxPayrollNumber) {
                $nextNumber = (int)substr($maxPayrollNumber, 3) + 1;
            }
            $payrollNumber = 'EMP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            // Generate national ID (random for seeding)
            $nationalId = rand(10000000, 99999999);

            // Create employee record
            $employeeData = [
                'user_id' => $user->id,
                'email' => $user->email,
                'national_id' => $nationalId,
                'department_id' => $departmentId,
                'location_id' => $locationId,
                'designation_id' => $designationId,
                'work_shift_id' => $workShiftId,
                'first_name' => $user->first_name ?? explode(' ', $user->user_name)[0] ?? 'Unknown',
                'last_name' => $user->last_name ?? explode(' ', $user->user_name)[1] ?? 'User',
                'date_of_birth' => '1990-01-01',
                'date_of_joining' => $time->format('Y-m-d'),
                'gender' => 'Other',
                'phone' => $user->msisdn ?? '254700000000',
                'personal_phone' => $user->msisdn ?? '254700000000',
                'personal_email' => $user->email,
                'status' => $user->status,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => $time,
                'updated_at' => $time,
                'payroll_number' => $payrollNumber,
                'company_id' => $companyId,
                'approval_status' => 1,
                'approved_by' => 1,
                'date_approved' => $time,
            ];

            // Check if employee_id is auto-increment or manual
            $employeeId = DB::table('employee')->insertGetId($employeeData);

            $this->command->info("Created employee {$payrollNumber} for user {$user->email}");
            $createdCount++;
        }

        // Summary
        $this->command->info('----------------------------------------');
        $this->command->info('Employee creation process completed!');
        $this->command->info("Users without employees processed: {$usersWithoutEmployees->count()}");
        $this->command->info("New employees created: {$createdCount}");
        $this->command->info("Existing employees linked: {$linkedCount}");
        $this->command->info('----------------------------------------');
    }
}
