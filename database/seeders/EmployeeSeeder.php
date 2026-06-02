<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $time = Carbon::now();

        $firstCompany = DB::table('companies')->first();

        if (!$firstCompany) {
            $this->command->error('No company found. Please run CompanySeeder first.');
            return;
        }

        $companyId = $firstCompany->id;
        $this->command->info("Assigning employees to company: {$firstCompany->name} (ID: {$companyId})");

        $testUserEmail = 'testuser@stawitech.com';

        $user = [
            'user_name' => 'testuser',
            'email' => $testUserEmail,
            'password' => Hash::make('password123'),
            'remember_token' => Str::random(10),
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => $time,
            'updated_at' => $time,
            'first_name' => 'Test',
            'last_name' => 'User',
        ];

        DB::table('user')->updateOrInsert(
            ['email' => $testUserEmail],
            $user
        );

        $userId = DB::table('user')->where('email', $testUserEmail)->value('id');

        $employees = [
            [
                'user_id' => $userId,
                'email' => $testUserEmail,
                'national_id' => '12345678',
                'department_id' => 1,
                'location_id' => 1,
                'designation_id' => 1,
                'work_shift_id' => 1,
                'first_name' => 'Test',
                'last_name' => 'User',
                'date_of_birth' => '1990-01-01',
                'date_of_joining' => '2020-01-01',
                'gender' => 'Male',
                'phone' => '254700000001',
                'personal_phone' => '+254700000001',
                'personal_email' => 'test.user@gmail.com',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => $time,
                'updated_at' => $time,
                'KRA_Pin' => 'A123456789B',
                'NSSF_no' => 'NSS123456',
                'NHIF_no' => 'NHIF123456',
                'payroll_number' => 'EMP001',
                'company_id' => $companyId,
                'tribe' => 'Luhya',
                'settlement_type' => 'Urban',
                'next_of_kin' => 'Jane Test',
                'next_of_kin_phone' => '+254700000011',
                'bank' => 'Equity Bank',
                'bank_branch' => 'CBD',
                'bank_account_number' => '1234567890',
                'bank_account_name' => 'Test User',
                'approval_status' => 1,
                'approved_by' => 1,
                'date_approved' => $time,
            ],
        ];

        foreach ($employees as $employee) {
            DB::table('employee')->updateOrInsert(
                ['email' => $employee['email']],
                $employee
            );
        }

        $this->createPayrollProfiles($employees, $companyId, $time);

        $this->command->info('Employee and linked user account created successfully.');
    }

    /**
     * Create payroll profiles for employees
     */
    private function createPayrollProfiles(array $employees, int $companyId, $time)
    {
        $payrollProfiles = [
            [
                'email' => 'testuser@stawitech.com',
                'payroll_number' => 'EMPR0001',
                'basic_salary' => 150000.00,
                'currency' => 'KES',
                'payment_method' => 'bank_transfer',
                'bank_name' => 'Equity Bank',
                'bank_branch' => 'CBD',
                'account_number' => '1234567890',
                'account_name' => 'Test User',
                'kra_pin' => 'A123456789B',
                'nssf_number' => 'NSS123456',
                'shif_number' => 'SHIF123456',
                'tax_status' => 'resident',
                'disability_exemption' => false,
                'is_active' => true,
                'effective_date' => '2020-01-01',
                'created_by' => 1,
                'updated_by' => 1,
                'approval_status' => 2,
                'status' => 1,
            ],
        ];

        foreach ($payrollProfiles as $profile) {
            $employee = DB::table('employee')->where('email', $profile['email'])->first();

            if ($employee) {
                $profile['employee_id'] = $employee->employee_id;
                unset($profile['email']);

                DB::table('employee_payrolls')->updateOrInsert(
                    ['employee_id' => $profile['employee_id']],
                    $profile
                );
            }
        }

        $this->command->info('Payroll profile created successfully for test employee.');
    }
}
