<?php

namespace Database\Seeders;

use App\Lib\Enumerations\GeneralStatus as EnumerationsGeneralStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use GeneralStatus;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $time = Carbon::now();

        // Get the first company ID
        $firstCompany = DB::table('companies')->first();

        if (!$firstCompany) {
            $this->command->error('No company found. Please run CompanySeeder first.');
            return;
        }

        $companyId = $firstCompany->id;
        $this->command->info("Assigning employees to company: {$firstCompany->name} (ID: {$companyId})");

        // First, create users with SuperAdmin role
        $users = [
            [
                'user_name' => 'smaloba3',
                'email' => 'smaloba3@gmail.com',
                'password' => Hash::make('password123'),
                'remember_token' => Str::random(10),
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => $time,
                'updated_at' => $time,
                'first_name' => 'Sam',
                'last_name' => 'Maloba'
            ],
            [
                'user_name' => 'support_stawi',
                'email' => 'support@stawitech.com',
                'password' => Hash::make('password123'),
                'remember_token' => Str::random(10),
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => $time,
                'updated_at' => $time,
                'first_name' => 'Support',
                'last_name' => 'StawiTech'
            ],
            [
                'user_name' => 'jchengasia',
                'email' => 'jchengasia@stawitech.com',
                'password' => Hash::make('password123'),
                'remember_token' => Str::random(10),
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => $time,
                'updated_at' => $time,
                'first_name' => 'Joseph',
                'last_name' => 'Chengasia'
            ],
            [
                'user_name' => 'gkoech',
                'email' => 'gkoech@stawitech.com',
                'password' => Hash::make('password123'),
                'remember_token' => Str::random(10),
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => $time,
                'updated_at' => $time,
                'first_name' => 'Grace',
                'last_name' => 'Koech'
            ],
            [
                'user_name' => 'cogara',
                'email' => 'cogara@stawitech.com',
                'password' => Hash::make('password123'),
                'remember_token' => Str::random(10),
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => $time,
                'updated_at' => $time,
                'first_name' => 'Collins',
                'last_name' => 'Ogara'
            ],

        ];

        // Insert users
        foreach ($users as $user) {
            DB::table('user')->updateOrInsert(
                ['email' => $user['email']],
                $user
            );
        }

        // Now create employee records linked to these users and assign to the first company
        $employees = [
            [
                'user_id' => DB::table('user')->where('email', 'smaloba3@gmail.com')->first()->id ?? 3,
                'email' => 'smaloba3@gmail.com',
                'national_id' => '12345678',
                'department_id' => 1,
                'location_id' => 1,
                'designation_id' => 1,
                'work_shift_id' => 1,
                'first_name' => 'Sam',
                'last_name' => 'Maloba',
                'date_of_birth' => '1990-01-01',
                'date_of_joining' => '2020-01-01',
                'gender' => 'Male',
                'phone' => '254700000001',
                'personal_phone' => '+254700000001',
                'personal_email' => 'sam.maloba@gmail.com',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => $time,
                'updated_at' => $time,
                'KRA_Pin' => 'A123456789B',
                'NSSF_no' => 'NSS123456',
                'NHIF_no' => 'NHIF123456',
                'payroll_number' => 'EMP001',
                'company_id' => $companyId, // Assign to first company
                'tribe' => 'Luhya',
                'settlement_type' => 'Urban',
                'next_of_kin' => 'Jane Maloba',
                'next_of_kin_phone' => '+254700000011',
                'bank' => 'Equity Bank',
                'bank_branch' => 'CBD',
                'bank_account_number' => '1234567890',
                'bank_account_name' => 'Sam Maloba',
                'approval_status' => 1,
                'approved_by' => 1,
                'date_approved' => $time,
            ],
            [
                'user_id' => DB::table('user')->where('email', 'support@stawitech.com')->first()->id ?? 4,
                'email' => 'support@stawitech.com',
                'national_id' => '23456789',
                'department_id' => 1,
                'location_id' => 1,
                'designation_id' => 1,
                'work_shift_id' => 1,
                'first_name' => 'Support',
                'last_name' => 'StawiTech',
                'date_of_birth' => '1985-05-15',
                'date_of_joining' => '2019-03-01',
                'gender' => 'Male',
                'phone' => '254700000002',
                'personal_phone' => '+254700000002',
                'personal_email' => 'support.personal@gmail.com',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => $time,
                'updated_at' => $time,
                'KRA_Pin' => 'B234567890C',
                'NSSF_no' => 'NSS234567',
                'NHIF_no' => 'NHIF234567',
                'payroll_number' => 'EMP002',
                'company_id' => $companyId, // Assign to first company
                'tribe' => 'Kikuyu',
                'settlement_type' => 'Urban',
                'next_of_kin' => 'Mary Support',
                'next_of_kin_phone' => '+254700000012',
                'bank' => 'KCB',
                'bank_branch' => 'Industrial Area',
                'bank_account_number' => '2345678901',
                'bank_account_name' => 'Support StawiTech',
                'approval_status' => 1,
                'approved_by' => 1,
                'date_approved' => $time,
            ],
            [
                'user_id' => DB::table('user')->where('email', 'jchengasia@stawitech.com')->first()->id ?? 5,
                'email' => 'jchengasia@stawitech.com',
                'national_id' => '34567890',
                'department_id' => 1,
                'location_id' => 1,
                'designation_id' => 1,
                'work_shift_id' => 1,
                'first_name' => 'Joseph',
                'last_name' => 'Chengasia',
                'date_of_birth' => '1988-08-20',
                'date_of_joining' => '2021-06-15',
                'gender' => 'Male',
                'phone' => '254700000003',
                'personal_phone' => '+254700000003',
                'personal_email' => 'joseph.chengasia@gmail.com',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => $time,
                'updated_at' => $time,
                'KRA_Pin' => 'C345678901D',
                'NSSF_no' => 'NSS345678',
                'NHIF_no' => 'NHIF345678',
                'payroll_number' => 'EMP003',
                'company_id' => $companyId, // Assign to first company
                'tribe' => 'Luo',
                'settlement_type' => 'Urban',
                'next_of_kin' => 'Akinyi Chengasia',
                'next_of_kin_phone' => '+254700000013',
                'bank' => 'Cooperative Bank',
                'bank_branch' => 'Town',
                'bank_account_number' => '3456789012',
                'bank_account_name' => 'Joseph Chengasia',
                'approval_status' => 1,
                'approved_by' => 1,
                'date_approved' => $time,
            ],
            [
                'user_id' => DB::table('user')->where('email', 'gkoech@stawitech.com')->first()->id ?? 6,
                'email' => 'gkoech@stawitech.com',
                'national_id' => '45678901',
                'department_id' => 1,
                'location_id' => 1,
                'designation_id' => 1,
                'work_shift_id' => 1,
                'first_name' => 'Grace',
                'last_name' => 'Koech',
                'date_of_birth' => '1992-12-10',
                'date_of_joining' => '2022-09-01',
                'gender' => 'Female',
                'phone' => '254700000004',
                'personal_phone' => '+254700000004',
                'personal_email' => 'grace.koech@gmail.com',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => $time,
                'updated_at' => $time,
                'KRA_Pin' => 'D456789012E',
                'NSSF_no' => 'NSS456789',
                'NHIF_no' => 'NHIF456789',
                'payroll_number' => 'EMP004',
                'company_id' => $companyId, // Assign to first company
                'tribe' => 'Kalenjin',
                'settlement_type' => 'Urban',
                'next_of_kin' => 'John Koech',
                'next_of_kin_phone' => '+254700000014',
                'bank' => 'Stanbic Bank',
                'bank_branch' => 'Westlands',
                'bank_account_number' => '4567890123',
                'bank_account_name' => 'Grace Koech',
                'approval_status' => 1,
                'approved_by' => 1,
                'date_approved' => $time,
            ],
            [
                'user_id' => DB::table('user')->where('email', 'cogara@stawitech.com')->first()->id ?? 7,
                'email' => 'cogara@stawitech.com',
                'national_id' => '56789012',
                'department_id' => 1,
                'location_id' => 1,
                'designation_id' => 1,
                'work_shift_id' => 1,
                'first_name' => 'Collins',
                'last_name' => 'Ogara',
                'date_of_birth' => '1987-03-25',
                'date_of_joining' => '2020-11-10',
                'gender' => 'Male',
                'phone' => '254700000005',
                'personal_phone' => '+254700000005',
                'personal_email' => 'collins.ogara@gmail.com',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => $time,
                'updated_at' => $time,
                'KRA_Pin' => 'E567890123F',
                'NSSF_no' => 'NSS567890',
                'NHIF_no' => 'NHIF567890',
                'payroll_number' => 'EMP005',
                'company_id' => $companyId, // Assign to first company
                'tribe' => 'Kisii',
                'settlement_type' => 'Urban',
                'next_of_kin' => 'Sarah Ogara',
                'next_of_kin_phone' => '+254700000015',
                'bank' => 'Absa Bank',
                'bank_branch' => 'Moi Avenue',
                'bank_account_number' => '5678901234',
                'bank_account_name' => 'Collins Ogara',
                'approval_status' => 1,
                'approved_by' => 1,
                'date_approved' => $time,
            ],
        ];

        // Insert employees
        foreach ($employees as $employee) {
            DB::table('employee')->updateOrInsert(
                ['email' => $employee['email']],
                $employee
            );
        }

        // Create payroll profiles for each employee
        $this->createPayrollProfiles($employees, $companyId, $time);

        $this->command->info('Employees created successfully and assigned to the first company.');
    }

    /**
     * Create payroll profiles for employees
     */
    private function createPayrollProfiles(array $employees, int $companyId, $time)
    {
        $payrollProfiles = [
            [
                'email' => 'smaloba3@gmail.com',
                'payroll_number' => 'EMPR0001',
                'basic_salary' => 150000.00,
                'currency' => 'KES',
                'payment_method' => 'bank_transfer',
                'bank_name' => 'Equity Bank',
                'bank_branch' => 'CBD',
                'account_number' => '1234567890',
                'account_name' => 'Sam Maloba',
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
            [
                'email' => 'support@stawitech.com',
                'payroll_number' => 'EMPR0002',
                'basic_salary' => 200000.00,
                'currency' => 'KES',
                'payment_method' => 'bank_transfer',
                'bank_name' => 'KCB',
                'bank_branch' => 'Industrial Area',
                'account_number' => '2345678901',
                'account_name' => 'Support StawiTech',
                'kra_pin' => 'B234567890C',
                'nssf_number' => 'NSS234567',
                'shif_number' => 'SHIF234567',
                'tax_status' => 'resident',
                'disability_exemption' => false,
                'is_active' => true,
                'effective_date' => '2019-03-01',
                'created_by' => 1,
                'updated_by' => 1,
                'approval_status' => 2,
                'status' => 1,
            ],
            [
                'email' => 'jchengasia@stawitech.com',
                'payroll_number' => 'EMPR0003',
                'basic_salary' => 120000.00,
                'currency' => 'KES',
                'payment_method' => 'bank_transfer',
                'bank_name' => 'Cooperative Bank',
                'bank_branch' => 'Town',
                'account_number' => '3456789012',
                'account_name' => 'Joseph Chengasia',
                'kra_pin' => 'C345678901D',
                'nssf_number' => 'NSS345678',
                'shif_number' => 'SHIF345678',
                'tax_status' => 'resident',
                'disability_exemption' => false,
                'is_active' => true,
                'effective_date' => '2021-06-15',
                'created_by' => 1,
                'updated_by' => 1,
                'approval_status' => 2,
                'status' => 1,
            ],
            [
                'email' => 'gkoech@stawitech.com',
                'payroll_number' => 'EMPR0004',
                'basic_salary' => 100000.00,
                'currency' => 'KES',
                'payment_method' => 'bank_transfer',
                'bank_name' => 'Stanbic Bank',
                'bank_branch' => 'Westlands',
                'account_number' => '4567890123',
                'account_name' => 'Grace Koech',
                'kra_pin' => 'D456789012E',
                'nssf_number' => 'NSS456789',
                'shif_number' => 'SHIF456789',
                'tax_status' => 'resident',
                'disability_exemption' => false,
                'is_active' => true,
                'effective_date' => '2022-09-01',
                'created_by' => 1,
                'updated_by' => 1,
                'approval_status' => 2,
                'status' => 1,
            ],
            [
                'email' => 'cogara@stawitech.com',
                'payroll_number' => 'EMPR0005',
                'basic_salary' => 130000.00,
                'currency' => 'KES',
                'payment_method' => 'bank_transfer',
                'bank_name' => 'Absa Bank',
                'bank_branch' => 'Moi Avenue',
                'account_number' => '5678901234',
                'account_name' => 'Collins Ogara',
                'kra_pin' => 'E567890123F',
                'nssf_number' => 'NSS567890',
                'shif_number' => 'SHIF567890',
                'tax_status' => 'resident',
                'disability_exemption' => false,
                'is_active' => true,
                'effective_date' => '2020-11-10',
                'created_by' => 1,
                'updated_by' => 1,
                'approval_status' => 2,
                'status' => 1,
            ],
        ];

        foreach ($payrollProfiles as $profile) {
            // Get employee record from the employee table
            $employee = DB::table('employee')->where('email', $profile['email'])->first();

            if ($employee) {
                // Use employee_id from the employee table (not the auto-increment id)
                $profile['employee_id'] = $employee->employee_id;
                unset($profile['email']); // Remove email as we now have employee_id

                DB::table('employee_payrolls')->updateOrInsert(
                    ['employee_id' => $profile['employee_id']],
                    $profile
                );
            }
        }

        $this->command->info('Payroll profiles created successfully for all employees.');
    }
}
