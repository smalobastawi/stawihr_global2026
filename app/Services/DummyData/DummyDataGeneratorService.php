<?php

namespace App\Services\DummyData;

use App\Lib\Enumerations\DisciplinaryCaseStatus;
use App\Lib\Enumerations\GeneralStatus;
use App\Lib\Enumerations\JobStatus;
use App\Lib\Enumerations\TrainingAttendanceStatus;
use App\Models\Company;
use App\Models\Department;
use App\Models\Designation;
use App\Models\JobRequisition;
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
    public const JOB_REQUISITION_COUNT = 10;
    public const JOB_POST_COUNT = 10;
    public const JOB_APPLICATION_COUNT = 10;
    public const VEHICLE_COUNT = 10;
    public const VEHICLE_ASSIGNMENT_COUNT = 10;
    public const TRAINING_COUNT = 10;
    public const ANONYMOUS_FEEDBACK_COUNT = 10;
    public const NOTICE_COUNT = 10;
    public const APPRAISAL_COUNT = 10;
    public const PIP_COUNT = 5;
    public const PDP_COUNT = 8;
    public const EMAIL_DOMAIN = 'stawihr-dummy.test';

    private const POSITION_TITLES = [
        'Software Developer',
        'HR Officer',
        'Accountant',
        'Marketing Specialist',
        'Operations Manager',
        'Customer Support Lead',
        'Data Analyst',
        'Procurement Officer',
        'Sales Executive',
        'IT Support Technician',
    ];

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
            $anonymousFeedbacks = $this->createAnonymousFeedbacks($reference['feedback_category_id'], $context);
            $trainings = $this->createTrainings($reference, $context);
            $trainingInvitees = $this->createTrainingInvitees($employees, $trainings, $context);
            $trainingAttendants = $this->createTrainingAttendants($employees, $trainings, $context);
            $leaveApplications = $this->createLeaveApplications($employees, $context);
            $attendanceRecords = $this->createAttendanceRecords($employees, $context);
            $jobRequisitions = $this->createJobRequisitions($employees, $context);
            $jobs = $this->createJobPosts($jobRequisitions, $context);
            $jobApplications = $this->createJobApplications($jobs, $employees, $context);
            $vehicles = $this->createVehicles($reference, $context);
            $vehicleAssignments = $this->createVehicleAssignments($vehicles, $employees, $context);
            $notices = $this->createNotices($employees, $context);
            $performancePolicy = $this->ensurePerformancePolicy($context);
            $appraisals = $this->createPerformanceAppraisals($employees, $performancePolicy, $context);
            $pips = $this->createPerformanceImprovementPlans($employees, $appraisals, $performancePolicy, $context);
            $pdps = $this->createPersonalDevelopmentPlans($employees, $performancePolicy, $context);

            $summary = [
                'employees' => count($employees),
                'users' => count($employees),
                'staff_contracts' => count($employees),
                'employee_payrolls' => count($payrollProfiles),
                'payroll_records' => $payrollRecords,
                'payroll_periods_used' => count($periods),
                'disciplinary_cases' => $disciplinaryCases,
                'employee_feedback' => $feedbacks,
                'anonymous_feedback' => $anonymousFeedbacks,
                'trainings' => count($trainings),
                'training_invitees' => $trainingInvitees,
                'training_attendants' => $trainingAttendants,
                'leave_applications' => $leaveApplications,
                'attendance_records' => $attendanceRecords,
                'employee_leavegroups' => count($employees),
                'job_requisitions' => count($jobRequisitions),
                'job_posts' => count($jobs),
                'job_applications' => $jobApplications,
                'vehicles' => count($vehicles),
                'vehicle_assignments' => $vehicleAssignments,
                'notices' => $notices,
                'review_periods' => $performancePolicy['review_periods_created'],
                'performance_rating_scales' => $performancePolicy['rating_scales_created'],
                'performance_behavioral_items' => $performancePolicy['behavioral_items_created'],
                'pdp_settings' => $performancePolicy['pdp_settings_created'],
                'performance_appraisals' => $appraisals['appraisals'],
                'performance_appraisal_scores' => $appraisals['scores'],
                'pip_plans' => $pips['plans'],
                'pip_goals' => $pips['goals'],
                'pdp_plans' => $pdps['plans'],
                'pdp_goals' => $pdps['goals'],
                'pdp_progress_entries' => $pdps['progress_entries'],
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

        $vehicleTypeId = DB::table('vehicle_types')->value('id');
        if (!$vehicleTypeId) {
            $vehicleTypeId = DB::table('vehicle_types')->insertGetId([
                'name' => 'Dummy Sedan',
                'code' => 'DUM-SEDAN',
                'description' => 'Auto-created for dummy data generation',
                'company_id' => $context['company_id'],
                'status' => 1,
                'created_by' => $context['created_by'],
                'updated_by' => $context['created_by'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->registry->track('vehicle_types', $vehicleTypeId);
        }

        return [
            'disciplinary_category_id' => $disciplinaryCategoryId,
            'feedback_category_id' => $feedbackCategoryId,
            'training_type_id' => $trainingTypeId,
            'facilitator_id' => $facilitatorId,
            'vehicle_type_id' => $vehicleTypeId,
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

    private function createAnonymousFeedbacks(int $categoryId, array $context): int
    {
        $count = 0;
        $statuses = ['pending', 'resolved', 'rejected'];

        for ($i = 1; $i <= self::ANONYMOUS_FEEDBACK_COUNT; $i++) {
            $feedbackId = DB::table('anonymous_feedback')->insertGetId([
                'title' => 'Anonymous suggestion #' . $i,
                'category_id' => $categoryId,
                'content' => 'Anonymous feedback entry generated for system testing. Suggestion number ' . $i . '.',
                'status' => $statuses[($i - 1) % count($statuses)],
                'action_type' => $i % 3 === 0 ? 'escalate' : null,
                'action_description' => $i % 3 === 0 ? 'Escalated for management review.' : null,
                'company_id' => $context['company_id'],
                'created_at' => now()->subDays($i),
                'updated_at' => now()->subDays($i),
            ]);
            $this->registry->track('anonymous_feedback', $feedbackId);
            $count++;
        }

        return $count;
    }

    private function createTrainings(array $reference, array $context): array
    {
        $trainingIds = [];
        $subjects = [
            'Workplace Safety Essentials',
            'Customer Service Excellence',
            'Leadership Fundamentals',
            'Data Protection Awareness',
            'Effective Communication',
            'Project Management Basics',
            'Conflict Resolution',
            'Time Management Skills',
            'Cybersecurity Awareness',
            'Performance Management',
        ];

        for ($i = 1; $i <= self::TRAINING_COUNT; $i++) {
            $isOnline = $i % 2 === 0;
            $startDate = Carbon::now()->subDays(40 - ($i * 3));

            $trainingId = DB::table('trainings')->insertGetId([
                'training_type_id' => $reference['training_type_id'],
                'facilitator_id' => $reference['facilitator_id'],
                'subject' => $subjects[$i - 1],
                'attendance_type' => $isOnline ? 'online' : 'physical',
                'attendance_link' => $isOnline ? 'https://example.com/training/' . $i : null,
                'attendance_location' => $isOnline ? null : 'Training Room ' . $i,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $startDate->copy()->addDays(1)->format('Y-m-d'),
                'start_time' => '09:00:00',
                'end_time' => '16:00:00',
                'description' => 'Auto-generated training for dummy data testing.',
                'status' => GeneralStatus::ACTIVE,
                'company_id' => $context['company_id'],
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

    private function createTrainingInvitees(array $employees, array $trainingIds, array $context): int
    {
        $count = 0;

        foreach ($trainingIds as $trainingId) {
            foreach (array_slice($employees, 0, 8) as $employee) {
                $inviteeId = DB::table('training_invitees')->insertGetId([
                    'employee_id' => $employee['employee_id'],
                    'training_id' => $trainingId,
                    'status' => TrainingAttendanceStatus::PENDING,
                    'sent_by' => $context['created_by'],
                    'company_id' => $context['company_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->registry->track('training_invitees', $inviteeId);
                $count++;
            }
        }

        return $count;
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

    private function createJobRequisitions(array $employees, array $context): array
    {
        $requisitions = [];
        $jobTypes = [
            JobRequisition::JOB_TYPE_IT,
            JobRequisition::JOB_TYPE_HR,
            JobRequisition::JOB_TYPE_FINANCE,
            JobRequisition::JOB_TYPE_MARKETING,
            JobRequisition::JOB_TYPE_MANAGEMENT,
            JobRequisition::JOB_TYPE_SUPPORT,
            JobRequisition::JOB_TYPE_PROFESSIONAL,
            JobRequisition::JOB_TYPE_TECHNICAL,
            JobRequisition::JOB_TYPE_SALES,
            JobRequisition::JOB_TYPE_SUPPORT,
        ];
        $employmentTypes = [
            JobRequisition::EMPLOYMENT_FULL_TIME,
            JobRequisition::EMPLOYMENT_CONTRACT,
            JobRequisition::EMPLOYMENT_PART_TIME,
        ];
        $urgencies = [
            JobRequisition::URGENCY_LOW,
            JobRequisition::URGENCY_NORMAL,
            JobRequisition::URGENCY_HIGH,
            JobRequisition::URGENCY_CRITICAL,
        ];
        $requesterUserId = $employees[0]['user_id'] ?? $context['created_by'];

        for ($i = 1; $i <= self::JOB_REQUISITION_COUNT; $i++) {
            $title = self::POSITION_TITLES[$i - 1];
            $minSalary = 50000 + ($i * 5000);
            $isApproved = $i <= 8;

            $requisitionId = DB::table('job_requisitions')->insertGetId([
                'requisition_number' => sprintf('DUM-REQ-%04d', $i),
                'position_title' => $title,
                'job_description' => "Dummy requisition for {$title}. Responsibilities include day-to-day delivery of role outcomes.",
                'job_requirements' => 'Relevant experience, strong communication skills, and team collaboration.',
                'key_responsibilities' => "Deliver assigned {$title} duties and support departmental goals.",
                'minimum_qualifications' => 'Bachelor degree or equivalent experience',
                'experience_required' => (2 + ($i % 5)) . ' years',
                'skills_competencies' => 'Communication, problem-solving, domain expertise',
                'number_of_positions' => 1 + ($i % 2),
                'job_type' => $jobTypes[$i - 1],
                'employment_type' => $employmentTypes[($i - 1) % count($employmentTypes)],
                'location_id' => $context['location_id'],
                'department_id' => $context['department_id'],
                'work_location' => 'Head Office',
                'minimum_salary' => $minSalary,
                'maximum_salary' => $minSalary + 30000,
                'currency' => 'KES',
                'other_benefits' => 'Medical cover, leave entitlement',
                'required_by_date' => Carbon::now()->addDays(30 + $i)->format('Y-m-d'),
                'proposed_start_date' => Carbon::now()->addDays(45 + $i)->format('Y-m-d'),
                'urgency_level' => $urgencies[($i - 1) % count($urgencies)],
                'reason_for_requisition' => 'Workload growth and coverage for operational needs.',
                'requisition_type' => JobRequisition::REQUISITION_TYPE_NEW_POSITION,
                'justification_for_hire' => 'Required to support business continuity and service delivery.',
                'reporting_manager' => $employees[0]['first_name'] . ' ' . $employees[0]['last_name'],
                'recruitment_source' => JobRequisition::SOURCE_BOTH,
                'status' => $isApproved ? JobRequisition::STATUS_APPROVED : JobRequisition::STATUS_PENDING_APPROVAL,
                'requested_by' => $requesterUserId,
                'approved_by' => $isApproved ? $context['created_by'] : null,
                'approved_at' => $isApproved ? now()->subDays($i) : null,
                'company_id' => $context['company_id'],
                'is_converted_to_job' => 0,
                'created_at' => now()->subDays(20 - $i),
                'updated_at' => now()->subDays(20 - $i),
            ], 'job_requisition_id');
            $this->registry->track('job_requisitions', $requisitionId);

            $requisitions[] = [
                'job_requisition_id' => $requisitionId,
                'position_title' => $title,
                'job_type' => $jobTypes[$i - 1],
                'employment_type' => $employmentTypes[($i - 1) % count($employmentTypes)],
                'minimum_salary' => $minSalary,
                'maximum_salary' => $minSalary + 30000,
                'status' => $isApproved ? JobRequisition::STATUS_APPROVED : JobRequisition::STATUS_PENDING_APPROVAL,
            ];
        }

        return $requisitions;
    }

    private function createJobPosts(array $requisitions, array $context): array
    {
        $jobs = [];

        for ($i = 1; $i <= self::JOB_POST_COUNT; $i++) {
            $requisition = $requisitions[$i - 1] ?? null;
            $title = $requisition['position_title'] ?? self::POSITION_TITLES[$i - 1];
            $publishDate = Carbon::now()->subDays(15 - $i);
            $linkRequisition = $requisition && $requisition['status'] === JobRequisition::STATUS_APPROVED;

            $jobId = DB::table('job')->insertGetId([
                'job_requisition_id' => $linkRequisition ? $requisition['job_requisition_id'] : null,
                'job_title' => $title,
                'job_type' => $requisition['job_type'] ?? JobRequisition::JOB_TYPE_PROFESSIONAL,
                'employment_type' => $requisition['employment_type'] ?? JobRequisition::EMPLOYMENT_FULL_TIME,
                'job_description' => "Dummy job post for {$title}. Join our team and contribute to organizational success.",
                'job_requirements' => 'Relevant qualifications and experience for the role.',
                'key_responsibilities' => "Execute core {$title} responsibilities and collaborate with stakeholders.",
                'minimum_qualifications' => 'Bachelor degree or equivalent experience',
                'experience_required' => (2 + ($i % 5)) . ' years',
                'skills_competencies' => 'Communication, analytical thinking, teamwork',
                'number_of_positions' => 1 + ($i % 2),
                'minimum_salary' => $requisition['minimum_salary'] ?? (50000 + ($i * 5000)),
                'maximum_salary' => $requisition['maximum_salary'] ?? (80000 + ($i * 5000)),
                'other_benefits' => 'Medical cover, leave entitlement',
                'publish_date' => $publishDate->format('Y-m-d'),
                'application_end_date' => $publishDate->copy()->addDays(30)->format('Y-m-d'),
                'location_id' => $context['location_id'],
                'department_id' => $context['department_id'],
                'audience_type' => $i % 3 === 0 ? 'internal' : ($i % 3 === 1 ? 'external' : 'both'),
                'status' => 1,
                'company_id' => $context['company_id'],
                'created_by' => $context['created_by'],
                'updated_by' => $context['created_by'],
                'created_at' => now(),
                'updated_at' => now(),
            ], 'job_id');
            $this->registry->track('job', $jobId);

            if ($linkRequisition) {
                DB::table('job_requisitions')
                    ->where('job_requisition_id', $requisition['job_requisition_id'])
                    ->update([
                        'is_converted_to_job' => 1,
                        'converted_job_id' => $jobId,
                        'converted_at' => now(),
                        'converted_by' => $context['created_by'],
                        'updated_at' => now(),
                    ]);
            }

            $jobs[] = [
                'job_id' => $jobId,
                'job_title' => $title,
            ];
        }

        return $jobs;
    }

    private function createJobApplications(array $jobs, array $employees, array $context): int
    {
        $count = 0;
        $statuses = [
            JobStatus::$Apply,
            JobStatus::$SHORTLIST,
            JobStatus::$REJECT,
            JobStatus::$CALL_FOR_INTERVIEW,
            JobStatus::$HIRE,
        ];
        $externalNames = [
            'Peter Otieno', 'Grace Wanjiku', 'Samuel Kariuki', 'Mercy Achieng', 'Daniel Mwangi',
            'Lucy Chebet', 'Joseph Barasa', 'Ann Njeri', 'Paul Kiprono', 'Esther Moraa',
        ];

        for ($i = 0; $i < self::JOB_APPLICATION_COUNT; $i++) {
            $job = $jobs[$i % count($jobs)];
            $isInternal = $i % 2 === 0;
            $employee = $employees[$i % count($employees)];
            $applicantName = $isInternal
                ? $employee['first_name'] . ' ' . $employee['last_name']
                : $externalNames[$i];
            $applicantEmail = $isInternal
                ? $employee['email']
                : 'applicant' . ($i + 1) . '@' . self::EMAIL_DOMAIN;

            $applicantId = DB::table('job_applicant')->insertGetId([
                'job_id' => $job['job_id'],
                'employee_id' => $isInternal ? $employee['employee_id'] : null,
                'applicant_name' => $applicantName,
                'applicant_email' => $applicantEmail,
                'phone' => '2547' . str_pad((string) (2000000 + $i), 7, '0', STR_PAD_LEFT),
                'cover_letter' => 'I am interested in the ' . $job['job_title'] . ' role and believe I am a strong fit.',
                'application_date' => Carbon::now()->subDays(10 - $i)->format('Y-m-d H:i:s'),
                'status' => $statuses[$i % count($statuses)],
                'location_id' => $context['location_id'],
                'years_of_experience' => 1 + ($i % 8),
                'highest_qualification' => $i % 2 === 0 ? 'Bachelor Degree' : 'Diploma',
                'application_source' => $isInternal ? 'internal' : 'external',
                'gender' => $i % 2 === 0 ? 'Male' : 'Female',
                'nationality' => 'Kenyan',
                'city' => 'Nairobi',
                'country' => 'Kenya',
                'expected_salary' => 70000 + ($i * 3000),
                'company_id' => $context['company_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ], 'job_applicant_id');
            $this->registry->track('job_applicant', $applicantId);
            $count++;
        }

        return $count;
    }

    private function createVehicles(array $reference, array $context): array
    {
        $vehicles = [];
        $makes = [
            ['Toyota', 'Corolla'],
            ['Toyota', 'Hilux'],
            ['Nissan', 'X-Trail'],
            ['Mazda', 'CX-5'],
            ['Honda', 'CR-V'],
            ['Isuzu', 'D-Max'],
            ['Mitsubishi', 'Outlander'],
            ['Subaru', 'Forester'],
            ['Ford', 'Ranger'],
            ['Volkswagen', 'Tiguan'],
        ];
        $colors = ['White', 'Silver', 'Black', 'Blue', 'Grey'];
        $fuelTypes = ['petrol', 'diesel', 'hybrid'];

        for ($i = 1; $i <= self::VEHICLE_COUNT; $i++) {
            [$make, $model] = $makes[$i - 1];
            $vehicleId = DB::table('vehicles')->insertGetId([
                'registration_number' => sprintf('KDM %03dD', $i),
                'make' => $make,
                'model' => $model,
                'year_of_manufacture' => 2018 + ($i % 6),
                'color' => $colors[($i - 1) % count($colors)],
                'chassis_number' => sprintf('DUMCHASSIS%06d', $i),
                'engine_number' => sprintf('DUMENG%06d', $i),
                'vehicle_type_id' => $reference['vehicle_type_id'],
                'fuel_type' => $fuelTypes[($i - 1) % count($fuelTypes)],
                'fuel_capacity' => 50 + ($i * 2),
                'purchase_date' => Carbon::now()->subYears(2)->addMonths($i)->format('Y-m-d'),
                'purchase_price' => 1500000 + ($i * 100000),
                'ownership_status' => $i % 5 === 0 ? 'leased' : 'company',
                'location_id' => $context['location_id'],
                'status' => GeneralStatus::ACTIVE,
                'remarks' => 'Dummy vehicle for testing fleet workflows.',
                'company_id' => $context['company_id'],
                'created_by' => $context['created_by'],
                'updated_by' => $context['created_by'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->registry->track('vehicles', $vehicleId);
            $vehicles[] = $vehicleId;
        }

        return $vehicles;
    }

    private function createVehicleAssignments(array $vehicleIds, array $employees, array $context): int
    {
        $count = 0;

        foreach (array_slice($vehicleIds, 0, self::VEHICLE_ASSIGNMENT_COUNT) as $index => $vehicleId) {
            $employee = $employees[$index % count($employees)];
            $assignedFrom = Carbon::now()->subMonths(3)->addWeeks($index);
            $isCurrent = $index < 7;

            $assignmentId = DB::table('vehicle_assignments')->insertGetId([
                'vehicle_id' => $vehicleId,
                'employee_id' => $employee['employee_id'],
                'assigned_from' => $assignedFrom->format('Y-m-d'),
                'assigned_to' => $isCurrent ? null : $assignedFrom->copy()->addMonths(1)->format('Y-m-d'),
                'assignment_reason' => 'Dummy fleet assignment for operational use.',
                'return_reason' => $isCurrent ? null : 'Assignment period ended.',
                'assigned_by' => $context['created_by'],
                'returned_by' => $isCurrent ? null : $context['created_by'],
                'returned_at' => $isCurrent ? null : $assignedFrom->copy()->addMonths(1)->format('Y-m-d H:i:s'),
                'company_id' => $context['company_id'],
                'created_by' => $context['created_by'],
                'updated_by' => $context['created_by'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->registry->track('vehicle_assignments', $assignmentId);
            $count++;
        }

        return $count;
    }

    private function createNotices(array $employees, array $context): int
    {
        $count = 0;
        $titles = [
            'Office Closure Notice',
            'Health and Safety Reminder',
            'Payroll Processing Schedule',
            'Annual Leave Policy Update',
            'IT System Maintenance Window',
            'Staff Town Hall Invitation',
            'Dress Code Reminder',
            'Fire Drill Announcement',
            'New Employee Benefits Brief',
            'Public Holiday Working Hours',
        ];

        for ($i = 1; $i <= self::NOTICE_COUNT; $i++) {
            $noticeId = DB::table('notice')->insertGetId([
                'title' => $titles[$i - 1],
                'description' => 'Dummy notice content for testing notice board workflows. Notice #' . $i . '.',
                'status' => $i % 4 === 0 ? 'Unpublished' : 'Published',
                'created_by' => $context['created_by'],
                'updated_by' => $context['created_by'],
                'publish_date' => Carbon::now()->subDays(12 - $i)->format('Y-m-d'),
                'target_gender' => 3, // Gender::ALL
                'location_id' => $context['location_id'],
                'company_id' => $context['company_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ], 'notice_id');
            $this->registry->track('notice', $noticeId);

            $departmentPivotId = DB::table('notice_departments')->insertGetId([
                'notice_id' => $noticeId,
                'department_id' => $context['department_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->registry->track('notice_departments', $departmentPivotId);

            $locationPivotId = DB::table('notice_locations')->insertGetId([
                'notice_id' => $noticeId,
                'location_id' => $context['location_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->registry->track('notice_locations', $locationPivotId);

            if ($i <= 5) {
                $employee = $employees[$i - 1];
                $employeePivotId = DB::table('notice_employees')->insertGetId([
                    'notice_id' => $noticeId,
                    'employee_id' => $employee['employee_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->registry->track('notice_employees', $employeePivotId);
            }

            $count++;
        }

        return $count;
    }

    private function ensurePerformancePolicy(array $context): array
    {
        $ratingScalesCreated = 0;
        if (DB::table('performance_rating_scales')->count() === 0) {
            $scales = [
                [5, 'Outstanding', 'Exceptional performance exceeding all expectations', '90% - 100%'],
                [4, 'Exceeds Expectations', 'Performance exceeds expectations in most areas', '80% - 89%'],
                [3, 'Meets Expectations', 'Performance meets the required standards', '60% - 79%'],
                [2, 'Needs Improvement', 'Performance below expected standards', '40% - 59%'],
                [1, 'Unsatisfactory', 'Performance significantly below standards', '0% - 39%'],
            ];

            foreach ($scales as $scale) {
                $scaleId = DB::table('performance_rating_scales')->insertGetId([
                    'points' => $scale[0],
                    'rating_label' => $scale[1],
                    'description' => $scale[2],
                    'definition' => $scale[2],
                    'score_range' => $scale[3],
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], 'rating_scale_id');
                $this->registry->track('performance_rating_scales', $scaleId);
                $ratingScalesCreated++;
            }
        }

        $goalIds = DB::table('performance_goals')->where('is_active', 1)->limit(6)->pluck('goal_id')->all();
        if (empty($goalIds)) {
            $focusAreaId = DB::table('performance_focus_areas')->insertGetId([
                'focus_area_name' => 'Dummy General Performance',
                'weight' => 100.00,
                'description' => 'Auto-created for dummy data generation',
                'department_id' => null,
                'designation_id' => null,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ], 'focus_area_id');
            $this->registry->track('performance_focus_areas', $focusAreaId);

            for ($i = 1; $i <= 4; $i++) {
                $goalId = DB::table('performance_goals')->insertGetId([
                    'focus_area_id' => $focusAreaId,
                    'strategic_objective' => "Dummy strategic objective {$i}",
                    'performance_metric' => "Metric {$i}",
                    'performance_target' => "Achieve target {$i}",
                    'key_initiatives' => 'Dummy initiatives for testing',
                    'itemized_weighting' => 25.00,
                    'sort_order' => $i,
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], 'goal_id');
                $this->registry->track('performance_goals', $goalId);
                $goalIds[] = $goalId;
            }
        }

        $behavioralItemIds = DB::table('performance_behavioral_items')->where('is_active', 1)->pluck('behavioral_item_id')->all();
        $behavioralItemsCreated = 0;
        if (empty($behavioralItemIds)) {
            $items = [
                ['Teamwork', 25.00, 'Collaborates effectively with colleagues'],
                ['Communication', 25.00, 'Communicates clearly and professionally'],
                ['Accountability', 25.00, 'Takes ownership of assigned responsibilities'],
                ['Customer Focus', 25.00, 'Delivers quality service to internal and external customers'],
            ];

            foreach ($items as $index => $item) {
                $itemId = DB::table('performance_behavioral_items')->insertGetId([
                    'item_name' => $item[0],
                    'weight' => $item[1],
                    'description' => $item[2],
                    'sort_order' => $index + 1,
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], 'behavioral_item_id');
                $this->registry->track('performance_behavioral_items', $itemId);
                $behavioralItemIds[] = $itemId;
                $behavioralItemsCreated++;
            }
        }

        $reviewPeriods = [];
        $reviewPeriodsCreated = 0;
        $existingPeriods = DB::table('review_periods')->where('is_active', 1)->orderBy('sort_order')->get();
        if ($existingPeriods->isEmpty()) {
            $periodDefs = [
                ['H1 ' . now()->year, now()->copy()->startOfYear(), now()->copy()->month(6)->endOfMonth()],
                ['H2 ' . now()->year, now()->copy()->month(7)->startOfMonth(), now()->copy()->endOfYear()],
            ];

            foreach ($periodDefs as $index => $period) {
                $periodId = DB::table('review_periods')->insertGetId([
                    'period_name' => $period[0],
                    'start_date' => $period[1]->format('Y-m-d'),
                    'end_date' => $period[2]->format('Y-m-d'),
                    'description' => 'Dummy review period for testing performance workflows',
                    'is_active' => 1,
                    'sort_order' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], 'period_id');
                $this->registry->track('review_periods', $periodId);
                $reviewPeriods[] = [
                    'period_id' => $periodId,
                    'period_name' => $period[0],
                    'start_date' => $period[1]->format('Y-m-d'),
                    'end_date' => $period[2]->format('Y-m-d'),
                ];
                $reviewPeriodsCreated++;
            }
        } else {
            foreach ($existingPeriods as $period) {
                $reviewPeriods[] = [
                    'period_id' => $period->period_id,
                    'period_name' => $period->period_name,
                    'start_date' => $period->start_date,
                    'end_date' => $period->end_date,
                ];
            }
        }

        $pdpSettingsCreated = 0;
        $pdpSettingId = DB::table('pdp_settings')->value('pdp_setting_id');
        if (!$pdpSettingId) {
            $pdpSettingId = DB::table('pdp_settings')->insertGetId([
                'company_id' => $context['company_id'],
                'default_review_frequency' => 'quarterly',
                'allow_employee_self_service' => true,
                'require_supervisor_approval' => true,
                'require_hr_review' => false,
                'policy_notes' => 'Dummy PDP policy: staff should define SMART goals and update progress each quarter. Supervisors review and provide feedback.',
                'created_at' => now(),
                'updated_at' => now(),
            ], 'pdp_setting_id');
            $this->registry->track('pdp_settings', $pdpSettingId);
            $pdpSettingsCreated = 1;
        }

        return [
            'goal_ids' => $goalIds,
            'behavioral_item_ids' => $behavioralItemIds,
            'review_periods' => $reviewPeriods,
            'pdp_setting_id' => $pdpSettingId,
            'review_periods_created' => $reviewPeriodsCreated,
            'rating_scales_created' => $ratingScalesCreated,
            'behavioral_items_created' => $behavioralItemsCreated,
            'pdp_settings_created' => $pdpSettingsCreated,
        ];
    }

    private function createPerformanceAppraisals(array $employees, array $policy, array $context): array
    {
        $statuses = ['draft', 'self_review', 'supervisor_review', 'hod_review', 'finalized', 'closed'];
        $supervisorId = $employees[0]['employee_id'];
        $period = $policy['review_periods'][0];
        $goalIds = $policy['goal_ids'];
        $behavioralItemIds = $policy['behavioral_item_ids'];
        $appraisalCount = 0;
        $scoreCount = 0;
        $appraisals = [];

        foreach (array_slice($employees, 0, self::APPRAISAL_COUNT) as $index => $employee) {
            $status = $statuses[$index % count($statuses)];
            $isFinalized = in_array($status, ['finalized', 'closed'], true);
            $selfBase = 55 + (($index * 4) % 40);
            $reviewBase = max(40, $selfBase - 5 + ($index % 10));

            $appraisalId = DB::table('performance_appraisals')->insertGetId([
                'employee_id' => $employee['employee_id'],
                'supervisor_id' => $supervisorId,
                'review_period' => $period['period_name'],
                'review_start_date' => $period['start_date'],
                'review_end_date' => $period['end_date'],
                'status' => $status,
                'total_itemized_weighting' => 100,
                'total_self_weighting' => $selfBase,
                'total_review_weighting' => $reviewBase,
                'employee_comments' => 'Dummy employee self-assessment comments.',
                'supervisor_comments' => $status === 'draft' ? null : 'Dummy supervisor review comments.',
                'hod_comments' => in_array($status, ['hod_review', 'finalized', 'closed'], true) ? 'Dummy HOD comments.' : null,
                'employee_signed' => !in_array($status, ['draft'], true),
                'employee_sign_date' => !in_array($status, ['draft'], true) ? now()->subDays(10 - $index) : null,
                'supervisor_signed' => in_array($status, ['supervisor_review', 'hod_review', 'finalized', 'closed'], true),
                'supervisor_sign_date' => in_array($status, ['supervisor_review', 'hod_review', 'finalized', 'closed'], true) ? now()->subDays(8 - $index) : null,
                'hod_signed' => $isFinalized,
                'hod_sign_date' => $isFinalized ? now()->subDays(5 - $index) : null,
                'finalized_by' => $isFinalized ? $supervisorId : null,
                'finalized_at' => $isFinalized ? now()->subDays(4 - $index) : null,
                'created_at' => now()->subDays(20 - $index),
                'updated_at' => now()->subDays(20 - $index),
            ], 'appraisal_id');
            $this->registry->track('performance_appraisals', $appraisalId);
            $appraisalCount++;

            $firstScoreId = null;
            foreach ($goalIds as $goalIndex => $goalId) {
                $weight = DB::table('performance_goals')->where('goal_id', $goalId)->value('itemized_weighting') ?? 25;
                $scoreId = DB::table('performance_appraisal_scores')->insertGetId([
                    'appraisal_id' => $appraisalId,
                    'goal_id' => $goalId,
                    'itemized_weighting' => $weight,
                    'self_weighting' => min(100, $selfBase + ($goalIndex * 2)),
                    'review_weighting' => min(100, $reviewBase + $goalIndex),
                    'self_comments' => 'Dummy self rating for goal.',
                    'review_comments' => $status === 'draft' ? null : 'Dummy reviewer rating for goal.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ], 'score_id');
                $this->registry->track('performance_appraisal_scores', $scoreId);
                $scoreCount++;
                if ($firstScoreId === null) {
                    $firstScoreId = $scoreId;
                }
            }

            foreach ($behavioralItemIds as $itemIndex => $itemId) {
                $weight = DB::table('performance_behavioral_items')->where('behavioral_item_id', $itemId)->value('weight') ?? 25;
                $behavioralScoreId = DB::table('performance_appraisal_behavioral_scores')->insertGetId([
                    'appraisal_id' => $appraisalId,
                    'behavioral_item_id' => $itemId,
                    'itemized_weighting' => $weight,
                    'self_weighting' => min(100, $selfBase + $itemIndex),
                    'review_weighting' => min(100, $reviewBase + $itemIndex),
                    'self_comments' => 'Dummy behavioral self rating.',
                    'review_comments' => $status === 'draft' ? null : 'Dummy behavioral review rating.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ], 'behavioral_score_id');
                $this->registry->track('performance_appraisal_behavioral_scores', $behavioralScoreId);
                $scoreCount++;
            }

            $appraisals[] = [
                'appraisal_id' => $appraisalId,
                'employee_id' => $employee['employee_id'],
                'review_score' => $reviewBase,
                'first_score_id' => $firstScoreId,
                'status' => $status,
            ];
        }

        return [
            'appraisals' => $appraisalCount,
            'scores' => $scoreCount,
            'records' => $appraisals,
        ];
    }

    private function createPerformanceImprovementPlans(array $employees, array $appraisalsResult, array $policy, array $context): array
    {
        $planCount = 0;
        $goalCount = 0;
        $statuses = ['draft', 'active', 'in_review', 'completed', 'extended'];
        $supervisorId = $employees[0]['employee_id'];
        $lowScoreAppraisals = array_values(array_filter(
            $appraisalsResult['records'] ?? [],
            fn ($appraisal) => ($appraisal['review_score'] ?? 100) < 70
        ));

        for ($i = 0; $i < self::PIP_COUNT; $i++) {
            $employee = $employees[$i + 1] ?? $employees[$i];
            $status = $statuses[$i % count($statuses)];
            $linkedAppraisal = $lowScoreAppraisals[$i] ?? ($appraisalsResult['records'][$i] ?? null);
            $startDate = Carbon::now()->subDays(45 - ($i * 5));
            $isCompleted = $status === 'completed';

            $pipId = DB::table('pip_plans')->insertGetId([
                'employee_id' => $employee['employee_id'],
                'supervisor_id' => $supervisorId,
                'hr_manager_id' => $supervisorId,
                'appraisal_id' => $linkedAppraisal['appraisal_id'] ?? null,
                'position' => self::POSITION_TITLES[$i % count(self::POSITION_TITLES)],
                'department_id' => $context['department_id'],
                'designation_id' => $context['designation_id'],
                'plan_period_start' => $startDate->format('Y-m-d'),
                'plan_period_end' => $startDate->copy()->addDays(60)->format('Y-m-d'),
                'purpose' => 'Dummy PIP created to address below-threshold performance and track improvement actions.',
                'trigger_score' => $linkedAppraisal['review_score'] ?? (50 + $i),
                'trigger_type' => $i % 2 === 0 ? 'automatic' : 'manual_supervisor',
                'status' => $status,
                'outcome' => $isCompleted ? 'successful_completion' : 'pending',
                'outcome_notes' => $isCompleted ? 'Employee met agreed improvement targets.' : null,
                'employee_acknowledged' => $status !== 'draft',
                'employee_ack_date' => $status !== 'draft' ? $startDate->copy()->addDay() : null,
                'supervisor_signed' => !in_array($status, ['draft'], true),
                'supervisor_sign_date' => !in_array($status, ['draft'], true) ? $startDate->copy()->addDays(2) : null,
                'hr_validated' => in_array($status, ['active', 'in_review', 'completed', 'extended'], true),
                'hr_validation_date' => in_array($status, ['active', 'in_review', 'completed', 'extended'], true) ? $startDate->copy()->addDays(3) : null,
                'is_locked' => $isCompleted,
                'created_by' => $supervisorId,
                'created_at' => now(),
                'updated_at' => now(),
            ], 'pip_id');
            $this->registry->track('pip_plans', $pipId);
            $planCount++;

            $concernId = DB::table('pip_concerns')->insertGetId([
                'pip_id' => $pipId,
                'goal_id' => $policy['goal_ids'][0] ?? null,
                'behavioral_item_id' => $policy['behavioral_item_ids'][0] ?? null,
                'appraisal_score_id' => $linkedAppraisal['first_score_id'] ?? null,
                'description' => 'Dummy concern: performance below expected standards in key deliverables.',
                'actual_score' => $linkedAppraisal['review_score'] ?? 48,
                'target_score' => 80,
                'created_at' => now(),
                'updated_at' => now(),
            ], 'concern_id');
            $this->registry->track('pip_concerns', $concernId);

            $pipGoalDefs = [
                ['Improve output quality', 'Use peer review checklist before submission', '>= 90% quality score', 'in_progress'],
                ['Meet delivery deadlines', 'Plan weekly priorities with supervisor', '100% on-time delivery', 'pending'],
            ];

            foreach ($pipGoalDefs as $goalDef) {
                $pipGoalId = DB::table('pip_goals')->insertGetId([
                    'pip_id' => $pipId,
                    'objective' => $goalDef[0],
                    'action_required' => $goalDef[1],
                    'target_kpi' => $goalDef[2],
                    'deadline' => $startDate->copy()->addDays(30)->format('Y-m-d'),
                    'status' => $isCompleted ? 'completed' : $goalDef[3],
                    'progress_notes' => $isCompleted ? 'Target achieved.' : 'Dummy progress notes for PIP goal.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ], 'goal_id');
                $this->registry->track('pip_goals', $pipGoalId);
                $goalCount++;
            }

            $supportId = DB::table('pip_support_resources')->insertGetId([
                'pip_id' => $pipId,
                'support_type' => 'mentorship',
                'description' => 'Weekly mentoring sessions with supervisor.',
                'provider' => 'supervisor',
                'scheduled_date' => $startDate->copy()->addDays(7)->format('Y-m-d'),
                'status' => $isCompleted ? 'completed' : 'in_progress',
                'created_at' => now(),
                'updated_at' => now(),
            ], 'resource_id');
            $this->registry->track('pip_support_resources', $supportId);

            $stages = ['First Review', 'Second Review', 'Final Review'];
            foreach ($stages as $stageIndex => $stageName) {
                $scheduleId = DB::table('pip_review_schedules')->insertGetId([
                    'pip_id' => $pipId,
                    'review_stage' => $stageName,
                    'stage_number' => $stageIndex + 1,
                    'scheduled_date' => $startDate->copy()->addWeeks(($stageIndex + 1) * 2)->format('Y-m-d'),
                    'status' => $stageIndex === 0 || $isCompleted ? 'completed' : 'pending',
                    'comments' => $stageIndex === 0 ? 'Initial improvement observed.' : null,
                    'findings' => $stageIndex === 0 ? 'Positive early progress.' : null,
                    'conducted_by' => $stageIndex === 0 || $isCompleted ? $supervisorId : null,
                    'conducted_at' => $stageIndex === 0 || $isCompleted ? $startDate->copy()->addWeeks(($stageIndex + 1) * 2) : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], 'schedule_id');
                $this->registry->track('pip_review_schedules', $scheduleId);
            }
        }

        return [
            'plans' => $planCount,
            'goals' => $goalCount,
        ];
    }

    private function createPersonalDevelopmentPlans(array $employees, array $policy, array $context): array
    {
        $planCount = 0;
        $goalCount = 0;
        $progressCount = 0;
        $supervisorId = $employees[0]['employee_id'];
        $currentYear = (int) now()->year;
        $goalCatalog = [
            ['Complete leadership fundamentals course', 'Leadership', 'high'],
            ['Improve presentation and facilitation skills', 'Communication', 'medium'],
            ['Earn role-related professional certification', 'Technical Excellence', 'high'],
            ['Build cross-functional collaboration habits', 'Teamwork', 'low'],
        ];

        for ($i = 0; $i < self::PDP_COUNT; $i++) {
            $employee = $employees[$i + 1] ?? $employees[$i];
            $isCompleted = $i >= 5;
            $planYear = $isCompleted ? $currentYear - 1 : $currentYear;
            $startDate = Carbon::create($planYear, 1, 1);
            $endDate = Carbon::create($planYear, 12, 31);
            $status = $isCompleted ? 'completed' : ($i === 0 ? 'draft' : 'active');
            $progressBase = $isCompleted ? 100 : (20 + ($i * 12));

            $planId = DB::table('pdp_plans')->insertGetId([
                'employee_id' => $employee['employee_id'],
                'supervisor_id' => $supervisorId,
                'department_id' => $context['department_id'],
                'designation_id' => $context['designation_id'],
                'plan_title' => $planYear . ' Professional Development Plan',
                'plan_year' => $planYear,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'review_frequency' => 'quarterly',
                'development_focus' => 'Leadership, communication, and technical excellence.',
                'career_aspirations' => 'Progress toward a senior specialist role within two years.',
                'status' => $status,
                'employee_acknowledged' => $status !== 'draft',
                'employee_ack_date' => $status !== 'draft' ? $startDate->copy()->addDays(5) : null,
                'employee_comments' => $status !== 'draft' ? 'Committed to completing development goals.' : null,
                'supervisor_approved' => in_array($status, ['active', 'completed'], true),
                'supervisor_approve_date' => in_array($status, ['active', 'completed'], true) ? $startDate->copy()->addDays(7) : null,
                'supervisor_comments' => in_array($status, ['active', 'completed'], true) ? 'Plan approved with quarterly check-ins.' : null,
                'hr_reviewed' => $isCompleted,
                'hr_review_date' => $isCompleted ? $endDate->copy()->subDays(10) : null,
                'hr_comments' => $isCompleted ? 'Development objectives completed successfully.' : null,
                'overall_summary' => $isCompleted
                    ? 'All development goals completed for the plan year.'
                    : 'Dummy PDP in progress with tracked quarterly updates.',
                'created_by' => $supervisorId,
                'created_at' => now(),
                'updated_at' => now(),
            ], 'pdp_plan_id');
            $this->registry->track('pdp_plans', $planId);
            $planCount++;

            $goalsForPlan = array_slice($goalCatalog, 0, $isCompleted ? 3 : 2);
            foreach ($goalsForPlan as $goalIndex => $goalDef) {
                $goalProgress = $isCompleted ? 100 : min(95, $progressBase + ($goalIndex * 10));
                $goalStatus = $isCompleted
                    ? 'completed'
                    : ($goalProgress >= 70 ? 'on_track' : ($goalProgress >= 40 ? 'in_progress' : 'not_started'));

                $goalId = DB::table('pdp_goals')->insertGetId([
                    'pdp_plan_id' => $planId,
                    'goal_title' => $goalDef[0],
                    'smart_objective' => 'By end of ' . $planYear . ', ' . strtolower($goalDef[0]) . ' with measurable outcomes.',
                    'competency_area' => $goalDef[1],
                    'success_criteria' => 'Demonstrate capability through assessment or applied workplace evidence.',
                    'development_actions' => 'Attend training, practice on the job, and request supervisor feedback.',
                    'resources_needed' => 'Learning materials and protected study time.',
                    'target_completion_date' => $endDate->copy()->subMonths(1)->format('Y-m-d'),
                    'priority' => $goalDef[2],
                    'status' => $goalStatus,
                    'overall_progress' => $goalProgress,
                    'sort_order' => $goalIndex + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], 'pdp_goal_id');
                $this->registry->track('pdp_goals', $goalId);
                $goalCount++;

                if ($status === 'draft') {
                    continue;
                }

                $quarters = $isCompleted ? [1, 2, 3, 4] : [1, 2];
                foreach ($quarters as $quarter) {
                    $quarterProgress = $isCompleted
                        ? min(100, (int) round(($quarter / 4) * 100))
                        : min(100, (int) round(($quarter / 2) * $goalProgress));
                    $progressStatus = $quarter < end($quarters) ? 'reviewed' : ($isCompleted ? 'reviewed' : 'submitted');

                    $progressId = DB::table('pdp_progress_entries')->insertGetId([
                        'pdp_plan_id' => $planId,
                        'pdp_goal_id' => $goalId,
                        'review_frequency' => 'quarterly',
                        'review_year' => $planYear,
                        'review_quarter' => $quarter,
                        'review_half' => null,
                        'review_period_label' => 'Q' . $quarter . ' ' . $planYear,
                        'progress_percentage' => $quarterProgress,
                        'achievement_summary' => "Dummy Q{$quarter} achievements toward {$goalDef[0]}.",
                        'challenges' => $isCompleted ? null : 'Competing workload during peak periods.',
                        'support_needed' => $isCompleted ? null : 'Protected Friday study time.',
                        'next_steps' => $isCompleted ? 'Maintain skills through practice.' : 'Continue planned development activities.',
                        'status' => $progressStatus,
                        'entered_by' => $employee['employee_id'],
                        'reviewed_by' => $progressStatus === 'reviewed' ? $supervisorId : null,
                        'supervisor_comments' => $progressStatus === 'reviewed' ? 'Progress reviewed and acknowledged.' : null,
                        'submitted_at' => $startDate->copy()->month($quarter * 3)->endOfMonth(),
                        'reviewed_at' => $progressStatus === 'reviewed' ? $startDate->copy()->month($quarter * 3)->endOfMonth()->addDays(3) : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ], 'pdp_progress_id');
                    $this->registry->track('pdp_progress_entries', $progressId);
                    $progressCount++;
                }
            }
        }

        return [
            'plans' => $planCount,
            'goals' => $goalCount,
            'progress_entries' => $progressCount,
        ];
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
