<?php

namespace App\Services\DummyData;

use App\Lib\Enumerations\DisciplinaryCaseStatus;
use App\Lib\Enumerations\GeneralStatus;
use App\Lib\Enumerations\TrainingAttendanceStatus;
use App\Models\Company;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Location;
use App\Models\Payroll\PayrollPeriod;
use App\Models\Payroll\PayrollRecord;
use App\Models\User;
use App\Models\WorkShift;
use App\Support\DummyData\DummyDataRegistry;
use App\Services\DummyData\DummyDataBatchCleanup;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DummyDataGeneratorService
{
    public const EMPLOYEE_COUNT = 15;
    public const PAYROLL_PERIOD_COUNT = 3;
    public const EMAIL_DOMAIN = 'stawihr-dummy.test';

    public function __construct(
        private readonly DummyDataRegistry $registry
    ) {
    }

    public function generate(int $userId): array
    {
        if ($this->registry->activeBatch()) {
            throw new \RuntimeException('Dummy data already exists. Remove the current test data before generating again.');
        }

        $context = $this->resolveContext();

        DB::beginTransaction();

        try {
            $this->registry->startBatch($userId);

            $reference = $this->ensureReferenceData($context);
            $employees = $this->createEmployees($context);
            $this->createContracts($employees, $context);
            $payrollProfiles = $this->createPayrollProfiles($employees, $context);
            $periods = $this->resolvePayrollPeriods();
            $payrollRecords = $this->createPayrollRecords($employees, $payrollProfiles, $periods, $context);
            $disciplinaryCases = $this->createDisciplinaryCases($employees, $reference['disciplinary_category_id'], $context);
            $feedbacks = $this->createFeedbacks($employees, $reference['feedback_category_id'], $context);
            $trainings = $this->createTrainings($reference, $context);
            $trainingAttendants = $this->createTrainingAttendants($employees, $trainings, $context);
            $leaveApplications = $this->createLeaveApplications($employees, $context);
            $attendanceRecords = $this->createAttendanceRecords($employees, $context);

            $summary = [
                'employees' => count($employees),
                'users' => count($employees),
                'staff_contracts' => count($employees),
                'employee_payrolls' => count($payrollProfiles),
                'payroll_records' => $payrollRecords,
                'payroll_periods_used' => count($periods),
                'disciplinary_cases' => $disciplinaryCases,
                'employee_feedback' => $feedbacks,
                'trainings' => count($trainings),
                'training_attendants' => $trainingAttendants,
                'leave_applications' => $leaveApplications,
                'attendance_records' => $attendanceRecords,
                'employee_leavegroups' => count($employees),
                'generated_at' => now()->toDateTimeString(),
            ];

            $batch = $this->registry->finishBatch($summary);

            DB::commit();

            return [
                'batch' => $batch,
                'summary' => $summary,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            DummyDataBatchCleanup::removeIncompleteBatch($this->registry);

            throw $e;
        }
    }

    private function resolveContext(): array
    {
        $company = Company::query()->first();
        $department = Department::query()->first();
        $designation = Designation::query()->first();
        $location = Location::query()->first();
        $workShift = WorkShift::query()->first();
        $financialYear = DB::table('financial_years')->value('id');
        $leaveType = DB::table('leave_type')->value('leave_type_id');
        $employeeRole = Role::query()->where('name', 'Employee')->first();

        if (!$company || !$department || !$designation || !$location || !$workShift) {
            throw new \RuntimeException('Core seed data is missing. Run database seeders before generating dummy data.');
        }

        return [
            'company_id' => $company->id,
            'department_id' => $department->department_id,
            'designation_id' => $designation->designation_id,
            'location_id' => $location->location_id,
            'work_shift_id' => $workShift->work_shift_id,
            'financial_year_id' => $financialYear,
            'leave_type_id' => $leaveType,
            'employee_role_id' => $employeeRole?->id,
            'created_by' => auth()->id() ?? 1,
        ];
    }

    private function ensureReferenceData(array $context): array
    {
        $disciplinaryCategoryId = DB::table('disciplinary_categories')->value('id');
        if (!$disciplinaryCategoryId) {
            $disciplinaryCategoryId = DB::table('disciplinary_categories')->insertGetId([
                'name' => 'Dummy Test Category',
                'description' => 'Auto-created for dummy data generation',
                'category_code' => 'DUM-CAT',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->registry->track('disciplinary_categories', $disciplinaryCategoryId);
        }

        $feedbackCategoryId = DB::table('feedback_categories')->value('id');
        if (!$feedbackCategoryId) {
            $feedbackCategoryId = DB::table('feedback_categories')->insertGetId([
                'name' => 'Dummy Test Feedback',
                'description' => 'Auto-created for dummy data generation',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->registry->track('feedback_categories', $feedbackCategoryId);
        }

        $trainingTypeId = DB::table('training_type')->value('training_type_id');
        if (!$trainingTypeId) {
            $trainingTypeId = DB::table('training_type')->insertGetId([
                'training_type_name' => 'Dummy Test Training Type',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->registry->track('training_type', $trainingTypeId);
        }

        $facilitatorId = DB::table('training_facilitators')->value('id');
        if (!$facilitatorId) {
            $facilitatorId = DB::table('training_facilitators')->insertGetId([
                'name' => 'Dummy Test Facilitator',
                'contact_email' => 'facilitator@' . self::EMAIL_DOMAIN,
                'contact_phone' => '254700000099',
                'type' => 'internal',
                'expertise' => 'General Skills',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->registry->track('training_facilitators', $facilitatorId);
        }

        return [
            'disciplinary_category_id' => $disciplinaryCategoryId,
            'feedback_category_id' => $feedbackCategoryId,
            'training_type_id' => $trainingTypeId,
            'facilitator_id' => $facilitatorId,
        ];
    }

    private function createEmployees(array $context): array
    {
        $employees = [];
        $genders = ['Male', 'Female'];
        $firstNames = ['Alex', 'Brian', 'Carol', 'Diana', 'Eric', 'Faith', 'George', 'Hannah', 'Ian', 'Jane', 'Kevin', 'Laura', 'Martin', 'Nina', 'Oscar'];
        $lastNames = ['Kamau', 'Wanjiru', 'Otieno', 'Akinyi', 'Mutua', 'Njeri', 'Ochieng', 'Achieng', 'Mwangi', 'Wambui', 'Kipchoge', 'Chebet', 'Njoroge', 'Muthoni', 'Barasa'];

        for ($i = 1; $i <= self::EMPLOYEE_COUNT; $i++) {
            $email = "dummy.employee{$i}@" . self::EMAIL_DOMAIN;
            $gender = $genders[$i % 2];
            $firstName = $firstNames[$i - 1];
            $lastName = $lastNames[$i - 1];

            $userId = DB::table('user')->insertGetId([
                'user_name' => strtolower("dummy{$i}"),
                'email' => $email,
                'password' => Hash::make('password123'),
                'remember_token' => Str::random(10),
                'status' => GeneralStatus::ACTIVE,
                'company_id' => $context['company_id'],
                'password_changed_at' => now(),
                'created_by' => $context['created_by'],
                'updated_by' => $context['created_by'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->registry->track('user', $userId);

            if ($context['employee_role_id']) {
                DB::table('model_has_roles')->insert([
                    'role_id' => $context['employee_role_id'],
                    'model_type' => User::class,
                    'model_id' => $userId,
                ]);
            }

            $employeeId = DB::table('employee')->insertGetId([
                'user_id' => $userId,
                'email' => $email,
                'national_id' => sprintf('DUM%010d', $i),
                'company_id' => $context['company_id'],
                'department_id' => $context['department_id'],
                'location_id' => $context['location_id'],
                'designation_id' => $context['designation_id'],
                'work_shift_id' => $context['work_shift_id'],
                'first_name' => $firstName,
                'last_name' => $lastName,
                'date_of_birth' => Carbon::now()->subYears(25 + ($i % 10))->format('Y-m-d'),
                'date_of_joining' => Carbon::now()->subMonths(12 + $i)->format('Y-m-d'),
                'gender' => $gender,
                'phone' => '2547' . str_pad((string) (1000000 + $i), 7, '0', STR_PAD_LEFT),
                'personal_phone' => '+2547' . str_pad((string) (1000000 + $i), 7, '0', STR_PAD_LEFT),
                'personal_email' => "personal{$i}@" . self::EMAIL_DOMAIN,
                'status' => GeneralStatus::ACTIVE,
                'payroll_number' => sprintf('DUM%03d', $i),
                'KRA_Pin' => sprintf('A%09dB', $i),
                'NSSF_no' => sprintf('NSSDUM%03d', $i),
                'NHIF_no' => sprintf('NHIFDUM%03d', $i),
                'bank' => 'Equity Bank',
                'bank_branch' => 'Dummy Branch',
                'bank_account_number' => sprintf('990000%05d', $i),
                'bank_account_name' => "{$firstName} {$lastName}",
                'approval_status' => 1,
                'approved_by' => $context['created_by'],
                'date_approved' => now(),
                'created_by' => $context['created_by'],
                'updated_by' => $context['created_by'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->registry->track('employee', $employeeId);

            $leaveGroupId = strtolower($gender) === 'female' ? 2 : 1;
            if (DB::table('leave_groups')->where('id', $leaveGroupId)->exists()) {
                $leaveGroupRowId = DB::table('employee_leavegroups')->insertGetId([
                    'leave_group_id' => $leaveGroupId,
                    'employee_id' => $employeeId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->registry->track('employee_leavegroups', $leaveGroupRowId);
            }

            $employees[] = [
                'employee_id' => $employeeId,
                'user_id' => $userId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'gender' => $gender,
                'email' => $email,
            ];
        }

        return $employees;
    }

    private function createContracts(array $employees, array $context): void
    {
        foreach ($employees as $index => $employee) {
            $startDate = Carbon::now()->subMonths(12 + $index);
            $contractId = DB::table('staff_contracts')->insertGetId([
                'employee_id' => $employee['employee_id'],
                'hire_date' => $startDate->format('Y-m-d'),
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $startDate->copy()->addYears(2)->format('Y-m-d'),
                'probation_start_date' => $startDate->format('Y-m-d'),
                'probation_end_date' => $startDate->copy()->addMonths(3)->format('Y-m-d'),
                'contract_type' => 'Permanent',
                'status' => 1,
                'location_id' => $context['location_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->registry->track('staff_contracts', $contractId);
        }
    }

    private function createPayrollProfiles(array $employees, array $context): array
    {
        $profiles = [];

        foreach ($employees as $index => $employee) {
            $basicSalary = 80000 + ($index * 5000);
            $profileId = DB::table('employee_payrolls')->insertGetId([
                'employee_id' => $employee['employee_id'],
                'payroll_number' => sprintf('DUMPR%03d', $index + 1),
                'basic_salary' => $basicSalary,
                'currency' => 'KES',
                'payment_method' => 'bank_transfer',
                'bank_name' => 'Equity Bank',
                'bank_branch' => 'Dummy Branch',
                'account_number' => sprintf('990000%05d', $index + 1),
                'account_name' => $employee['first_name'] . ' ' . $employee['last_name'],
                'kra_pin' => sprintf('A%09dB', $index + 1),
                'nssf_number' => sprintf('NSSDUM%03d', $index + 1),
                'shif_number' => sprintf('SHIFDUM%03d', $index + 1),
                'tax_status' => 'resident',
                'disability_exemption' => false,
                'is_active' => true,
                'effective_date' => Carbon::now()->subYear()->format('Y-m-d'),
                'status' => GeneralStatus::ACTIVE,
                'approval_status' => 2,
                'created_by' => $context['created_by'],
                'updated_by' => $context['created_by'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->registry->track('employee_payrolls', $profileId);

            $profiles[$employee['employee_id']] = [
                'id' => $profileId,
                'basic_salary' => $basicSalary,
            ];
        }

        return $profiles;
    }

    private function resolvePayrollPeriods(): array
    {
        $periods = PayrollPeriod::query()
            ->orderByDesc('start_date')
            ->limit(self::PAYROLL_PERIOD_COUNT)
            ->get()
            ->sortBy('start_date')
            ->values();

        if ($periods->count() < self::PAYROLL_PERIOD_COUNT) {
            throw new \RuntimeException('At least ' . self::PAYROLL_PERIOD_COUNT . ' payroll periods are required. Run PayrollPeriodSeeder first.');
        }

        return $periods->all();
    }

    private function createPayrollRecords(array $employees, array $profiles, array $periods, array $context): int
    {
        $count = 0;

        foreach ($employees as $employee) {
            $profile = $profiles[$employee['employee_id']];
            $basicSalary = (float) $profile['basic_salary'];

            foreach ($periods as $period) {
                $grossSalary = $basicSalary * 1.05;
                $paye = round($grossSalary * 0.15, 2);
                $nssf = round($grossSalary * 0.06, 2);
                $shif = 500;
                $housingLevy = round($grossSalary * 0.015, 2);
                $totalDeductions = $paye + $nssf + $shif + $housingLevy;
                $netSalary = round($grossSalary - $totalDeductions, 2);

                $recordId = DB::table('payroll_records')->insertGetId([
                    'employee_id' => $employee['employee_id'],
                    'employee_payroll_id' => $profile['id'],
                    'payroll_period_id' => $period->id,
                    'basic_salary' => $basicSalary,
                    'total_allowances' => round($basicSalary * 0.05, 2),
                    'gross_salary' => $grossSalary,
                    'total_deductions' => $totalDeductions,
                    'statutory_deductions' => $totalDeductions,
                    'non_statutory_deductions' => 0,
                    'paye_tax' => $paye,
                    'nssf_contribution' => $nssf,
                    'shif_contribution' => $shif,
                    'housing_levy' => $housingLevy,
                    'pension_contribution' => 0,
                    'net_salary' => $netSalary,
                    'payment_method' => 'bank_transfer',
                    'payment_date' => $period->pay_date ?? $period->end_date,
                    'status' => PayrollRecord::STATUS_APPROVED,
                    'payroll_record_status' => 1,
                    'processed_by' => $context['created_by'],
                    'approved_by' => $context['created_by'],
                    'created_by' => $context['created_by'],
                    'updated_by' => $context['created_by'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->registry->track('payroll_records', $recordId);
                $count++;
            }
        }

        return $count;
    }

    private function createDisciplinaryCases(array $employees, int $categoryId, array $context): int
    {
        $count = 0;

        foreach (array_slice($employees, 0, 8) as $index => $employee) {
            $caseId = DB::table('disciplinary_cases')->insertGetId([
                'case_number' => 'DUM-CASE-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                'description' => 'Dummy disciplinary case for testing workflows and reports.',
                'category_id' => $categoryId,
                'employee_id' => $employee['employee_id'],
                'assigned_officer' => $employees[0]['employee_id'],
                'location' => 'Head Office',
                'location_id' => $context['location_id'],
                'date_of_incident' => Carbon::now()->subDays(20 + $index)->format('Y-m-d'),
                'date_of_report' => Carbon::now()->subDays(18 + $index)->format('Y-m-d'),
                'reporter_id' => $employees[0]['employee_id'],
                'status' => $index % 2 === 0 ? DisciplinaryCaseStatus::OPEN : DisciplinaryCaseStatus::CLOSED,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->registry->track('disciplinary_cases', $caseId);
            $count++;
        }

        return $count;
    }

    private function createFeedbacks(array $employees, int $categoryId, array $context): int
    {
        $count = 0;

        foreach (array_slice($employees, 0, 10) as $index => $employee) {
            $feedbackId = DB::table('employee_feedback')->insertGetId([
                'employee_id' => $employee['employee_id'],
                'location_id' => $context['location_id'],
                'category_id' => $categoryId,
                'title' => 'Dummy feedback #' . ($index + 1),
                'content' => 'Sample employee feedback entry generated for system testing.',
                'status' => $index % 3,
                'created_by' => $employee['user_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->registry->track('employee_feedback', $feedbackId);
            $count++;
        }

        return $count;
    }

    private function createTrainings(array $reference, array $context): array
    {
        $trainingIds = [];

        for ($i = 1; $i <= 3; $i++) {
            $trainingId = DB::table('trainings')->insertGetId([
                'training_type_id' => $reference['training_type_id'],
                'facilitator_id' => $reference['facilitator_id'],
                'subject' => "Dummy Training Session {$i}",
                'attendance_type' => $i % 2 === 0 ? 'online' : 'physical',
                'attendance_link' => $i % 2 === 0 ? 'https://example.com/training' : null,
                'attendance_location' => $i % 2 === 0 ? null : 'Training Room ' . $i,
                'start_date' => Carbon::now()->subDays(30 - ($i * 5))->format('Y-m-d'),
                'end_date' => Carbon::now()->subDays(28 - ($i * 5))->format('Y-m-d'),
                'description' => 'Auto-generated training for dummy data testing.',
                'created_by' => $context['created_by'],
                'updated_by' => $context['created_by'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->registry->track('trainings', $trainingId);
            $trainingIds[] = $trainingId;
        }

        return $trainingIds;
    }

    private function createTrainingAttendants(array $employees, array $trainingIds, array $context): int
    {
        $count = 0;

        foreach ($trainingIds as $trainingId) {
            foreach (array_slice($employees, 0, 10) as $employee) {
                $attendantId = DB::table('training_attendants')->insertGetId([
                    'employee_id' => $employee['employee_id'],
                    'training_id' => $trainingId,
                    'status' => TrainingAttendanceStatus::CONFIRMED,
                    'responded_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->registry->track('training_attendants', $attendantId);
                $count++;
            }
        }

        return $count;
    }

    private function createLeaveApplications(array $employees, array $context): int
    {
        if (!$context['leave_type_id'] || !$context['financial_year_id']) {
            return 0;
        }

        $count = 0;

        foreach (array_slice($employees, 0, 12) as $index => $employee) {
            $fromDate = Carbon::now()->subMonths(2)->addDays($index * 2);
            $toDate = $fromDate->copy()->addDays(2);

            $leaveId = DB::table('leave_application')->insertGetId([
                'employee_id' => $employee['employee_id'],
                'leave_type_id' => $context['leave_type_id'],
                'application_from_date' => $fromDate->format('Y-m-d'),
                'application_to_date' => $toDate->format('Y-m-d'),
                'application_date' => $fromDate->copy()->subDays(5)->format('Y-m-d'),
                'number_of_day' => 3,
                'is_half_day' => false,
                'purpose' => 'Dummy leave application for testing',
                'status' => 1,
                'final_status' => 1,
                'approve_date' => $fromDate->copy()->subDays(4)->format('Y-m-d'),
                'approve_by' => $employees[0]['employee_id'],
                'financial_year_id' => $context['financial_year_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->registry->track('leave_application', $leaveId);
            $count++;
        }

        return $count;
    }

    private function createAttendanceRecords(array $employees, array $context): int
    {
        $count = 0;

        foreach ($employees as $empIndex => $employee) {
            for ($day = 1; $day <= 10; $day++) {
                $date = Carbon::now()->subDays($day);
                if ($date->isWeekend()) {
                    continue;
                }

                $attendanceId = DB::table('attendances')->insertGetId([
                    'date' => $date->format('Y-m-d'),
                    'month' => $date->format('Y-m'),
                    'employee_id' => $employee['employee_id'],
                    'department_id' => $context['department_id'],
                    'work_shift_id' => $context['work_shift_id'],
                    'location_id' => $context['location_id'],
                    'payroll_number' => sprintf('DUM%03d', $empIndex + 1),
                    'time_in' => $date->copy()->setTime(8, 0)->format('Y-m-d H:i:s'),
                    'time_out' => $date->copy()->setTime(17, 0)->format('Y-m-d H:i:s'),
                    'is_late' => $day % 5 === 0 ? 1 : 0,
                    'late_time' => $day % 5 === 0 ? '00:15:00' : null,
                    'working_time' => '08:00:00',
                    'workingHours' => 8,
                    'total_time_worked' => '08:00:00',
                    'presence_status' => 1,
                    'approval_status' => 1,
                    'created_by' => $context['created_by'],
                    'updated_by' => $context['created_by'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->registry->track('attendances', $attendanceId);
                $count++;
            }
        }

        return $count;
    }
}
