<?php

namespace App\Http\Controllers\Ess;

use Exception;
use Carbon\Carbon;
use App\Models\Job;
use App\Models\User;
use App\Models\Survey;
use App\Models\Employee;
use App\Models\Training;
use App\Models\LeaveType;
use App\Models\HrDocument;
use Illuminate\Support\Str;
use Termwind\Components\Hr;
use App\Models\DocumentView;
use App\Models\DocumentConsent;
use App\Models\JobApplicant;
use App\Models\TrainingInfo;
use App\Models\TrainingType;
use Illuminate\Http\Request;
use App\Models\EmployeeAward;
use App\Models\FinancialYear;
use App\Models\SalaryDetails;
use App\Models\ApprovalRequest;
use App\Models\TrainingInvitee;
use App\Lib\Enumerations\Gender;
use App\Models\DisciplinaryCase;
use App\Models\WorkShift;
use App\Models\LeaveApplication;
use App\Models\TrainingAttendant;
use App\Models\LeaveJustification;
use App\Models\LeaveSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\TrainingFacilitator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Events\LeaveApplicationEvent;
use App\Lib\Enumerations\LeaveStatus;
use App\Repositories\LeaveRepository;
use App\Mail\TrainingConfirmationMail;
use App\Models\DisciplinaryCaseAction;
use App\Repositories\CommonRepository;
use App\Mail\JobApplicationConfirmation;
use App\Mail\Leave\StaffLeaveRecallMail;
use App\Mail\NewApplicationHrNotification;
use App\Http\Requests\ApplyForLeaveRequest;
use App\Mail\Leave\HR_LeaveApplicationMail;
use App\Mail\Leave\StaffLeaveApplicationMail;
use App\Mail\Leave\SupervisorLeaveRecallMail;
use App\Notifications\LeaveApplicationSubmitted;
use App\Lib\Enumerations\TrainingAttendanceStatus;
use App\Lib\Enumerations\TrainingInvitationStatus;
use App\Mail\Leave\SupervisorLeaveApplicationMail;
use App\Http\Requests\InternalJobApplicationRequest;
use App\Lib\Enumerations\PayrollStatus;
use App\Models\ApprovalAssignment;
use App\Models\ApprovalLog;
use App\Models\CompanyAddressSetting;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use App\Models\LeaveJustification as ModelsLeaveJustification;
use App\Models\Notice;
use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ApprovalDelegation;
use App\Http\Requests\StoreApprovalDelegationRequest;
use App\Http\Requests\UpdateApprovalDelegationRequest;
use App\Models\ApprovalWorkflow;
use App\Models\Vehicle\VehicleAssignment;
use App\Models\Performance\PerformanceAppraisal;
use App\Models\Performance\PerformanceAppraisalScore;
use App\Models\Performance\PerformanceAppraisalBehavioralScore;
use App\Models\Pip\PipPlan;

class EssIndexController extends Controller
{


    protected $user;
    protected $employee;
    protected $role;
    protected $supervisor;
    protected $hr;
    protected $commonRepository;
    protected $leaveRepository;

    public function __construct(CommonRepository $commonRepository, LeaveRepository $leaveRepository)
    {
        // Assign the user  globally for all methods
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            $this->role = $this->user->roles()->first();
            $this->employee = $this->user->employeeDetails ?? new Employee();
            $this->supervisor = $this->employee->supervisor;
            $this->hr = $this->employee->hr();

            view()->share(['user', $this->user, 'hr' => $this->hr, 'role' => $this->role, 'supervisor' => $this->supervisor]);
            return $next($request);
        });

        $this->commonRepository = $commonRepository;
        $this->leaveRepository = $leaveRepository;
    }

    public function index()
    {
        // Use $this->authenticatedUser for authenticated user details
        return view('admin.ess.index', [
            'user' => $this->user, // Pass user to the view
        ]);
    }

    public function appraisal(Request $request)
    {
        return redirect()->route('ess.index');
    }

    public function approval()
    {
        $currentUserId = auth()->id();
        $perPage = 500;

        // Use delegation-aware methods from the trait
        $allApprovals = \App\Traits\HasApprovalWorkflow::getPendingApprovalsForUserWithDelegates($currentUserId);

        // Filter out payroll records for regular approvals
        $approvalsQuery = $allApprovals->filter(function ($item) {
            return $item->approvable_type !== 'App\Models\Payroll\PayrollRecord';
        })->sortByDesc('created_at')->values();

        // Get submissions with delegation support
        $allSubmissions = \App\Traits\HasApprovalWorkflow::getSubmissionsForUserWithDelegates($currentUserId);
        $submissionsQuery = $allSubmissions->filter(function ($item) {
            return $item->approvable_type !== 'App\Models\Payroll\PayrollRecord';
        })->sortByDesc('created_at')->values();

        // Manually paginate the collections
        $approvals = new \Illuminate\Pagination\LengthAwarePaginator(
            $approvalsQuery->forPage(\Illuminate\Pagination\Paginator::resolveCurrentPage(), $perPage),
            $approvalsQuery->count(),
            $perPage,
            \Illuminate\Pagination\Paginator::resolveCurrentPage(),
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        $submissions = new \Illuminate\Pagination\LengthAwarePaginator(
            $submissionsQuery->forPage(\Illuminate\Pagination\Paginator::resolveCurrentPage(), $perPage),
            $submissionsQuery->count(),
            $perPage,
            \Illuminate\Pagination\Paginator::resolveCurrentPage(),
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        // For payroll records (special handling) - Get both direct and delegated approvals
        // Get all user IDs that this user can act on behalf of (including delegations)
        $delegateUserIds = [$currentUserId];

        // Get delegations where current user is the delegate
        $delegations = ApprovalDelegation::where('delegate_to_user_id', $currentUserId)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->where(function ($query) {
                $query->where('delegation_type', 'all')
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('delegation_type', 'specific')
                            ->where('model_type', 'App\Models\Payroll\PayrollRecord');
                    });
            })
            ->pluck('user_id')
            ->toArray();

        $delegateUserIds = array_merge($delegateUserIds, $delegations);

        $payrollRecordApprovalsQuery = ApprovalLog::with([
            'approvable.employee',
            'approvable.payrollPeriod',
            'approvable.details',
            'approvable.employee.designation',
            'approvable.employee.branch',
            'approvable.employee.department',
            'approvable.employeePayroll',
            'step.assignments.user',
            'user'
        ])
            ->whereIn('user_id', $delegateUserIds)
            ->whereIn('action', [null, 'pending', 'queued'])
            ->where('approvable_type', 'App\Models\Payroll\PayrollRecord')
            ->whereHas('approvable')
            ->orderBy('created_at', 'desc');

        $payrollRecordSubmissionsQuery = ApprovalLog::with([
            'approvable.employee',
            'approvable.payrollPeriod',
            'approvable.details',
            'approvable.employee.designation',
            'approvable.employee.branch',
            'approvable.employee.department',
            'approvable.employeePayroll',
            'step.assignments.user',
            'user'
        ])
            ->where('action', 'submitted')
            ->where('approvable_type', 'App\Models\Payroll\PayrollRecord')
            ->whereHas('approvable', function ($query) use ($delegateUserIds) {
                $query->whereIn('user_id', $delegateUserIds);
            })
            ->orderBy('created_at', 'desc');

        // Use database pagination for payroll
        $payrollRecordApprovals = $payrollRecordApprovalsQuery->paginate($perPage);
        $payrollRecordSubmissions = $payrollRecordSubmissionsQuery->paginate($perPage);

        // Define payroll record column mappings
        $payrollColumns = [
            // Employee basic info
            'employee_code' => 'Employee Code',
            'employee_surname' => 'Employee Surname',
            'employee_first_name' => 'Employee First name',
            'employee_second_name' => 'Employee Second name',
            'job_title' => 'Job Title',
            'location' => 'Locations (HA)',
            'sub_program' => 'Sub_Programs (HA)',
            'income_frequency' => 'Income Frequency',

            // ===== EARNINGS SECTION =====
            'basic_income' => 'Basic Income (Earning)',
            'overtime_totals' => 'OT_Totals (Earning)',

            'earning_total' => 'Earning Total',
            'unpaid' => 'Unpaid Leave/Deductions',
            'effective_earning' => 'Effective Earning',

            // ===== COMPANY CONTRIBUTIONS SECTION =====
            'nita' => 'NITA',
            'nssf_tier1_company' => 'NSSF Tier I (CompanyContribution)',
            'nssf_tier2_company' => 'NSSF Tier II (CompanyContribution)',
            'total_nssf_company' => 'TOTAL NSSF (CompanyContribution)',
            'housing_levy_company' => 'Affordable Housing Levy (Company Contribution)',

            'pension_employer_total' => 'Pension Employer Total',
            'company_contribution_total' => 'CompanyContribution Total',
            'total_cost' => 'Total Cost',

            // ===== DEDUCTIONS SECTION =====
            'paye' => 'PAYE (Deduction)',
            'nssf_tier1_deduction' => 'NSSF Tier I (Deduction)',
            'nssf_tier2_deduction' => 'NSSF Tier II (Deduction)',
            'total_nssf_deduction' => 'TOTAL NSSF (Deduction)',
            'shif_deduction' => 'SHIF (Deduction)',
            'housing_levy_deduction' => 'Affordable Housing Levy (Deduction)',
            'pension_employee_total' => 'Pension Employee Total',
            'salary_advance' => 'Salary Advance (Deduction)',
            'total_deductions' => 'Total Deductions',

            // ===== FINAL VALUES SECTION =====
            'netpay' => 'NetPay',
            'deductions_vs_earnings' => 'Deductions vs Earnings (%)',
            'payment_reference' => 'Payment Reference',

            // ===== PROJECT ALLOCATIONS SECTION =====
            'primary_grant' => 'PRIMARY GRANT',
            'primary_loe' => 'LOE',
            'sec_grant' => 'SEC. GRANT',
            'sec_loe' => 'LOE',
            'tertiary_project' => 'Tertiary project',
            'tertiary_loe' => 'LOE',

            // Status
            'status' => 'Status'
        ];

        // Define which columns should be summed (numeric columns)
        $summableColumns = [
            'basic_income',
            'overtime_totals',
            'earning_total',
            'unpaid',
            'effective_earning',
            'nita',
            'nssf_tier1_company',
            'nssf_tier2_company',
            'total_nssf_company',
            'housing_levy_company',
            'pension_employer_total',
            'company_contribution_total',
            'total_cost',
            'paye',
            'nssf_tier1_deduction',
            'nssf_tier2_deduction',
            'total_nssf_deduction',
            'shif_deduction',
            'housing_levy_deduction',
            'pension_employee_total',
            'salary_advance',
            'total_deductions',
            'netpay'
        ];

        // Initialize totals array
        $totals = array_fill_keys($summableColumns, 0);
        $totalDeductionsVsEarnings = 0;
        $recordCount = 0;

        // Process payroll records to extract data for table display
        $processedPayrollRecords = [];
        foreach ($payrollRecordApprovals as $approval) {
            // Mark approval as delegated if user_id is not the current user
            $approval->is_delegated = ($approval->user_id !== $currentUserId);

            if ($approval->approvable instanceof \App\Models\Payroll\PayrollRecord) {
                $record = $approval->approvable;

                // Get employee information
                $employeeName = 'N/A';
                $employeeCode = 'N/A';
                $jobTitle = 'N/A';
                $location = 'N/A';
                $subProgram = 'N/A';
                $incomeFrequency = 'N/A';
                $paymentReference = 'N/A';

                if ($record->employee) {
                    // Employee details
                    $employeeSurname = $record->employee->last_name ?? '';
                    $employeeFirstName = $record->employee->first_name ?? '';
                    $employeeSecondName = $record->employee->middle_name ?? '';

                    // Employee code
                    if (isset($record->employee->payroll_number)) {
                        $employeeCode = $record->employee->payroll_number;
                    } elseif (isset($record->employee->employee_code)) {
                        $employeeCode = $record->employee->employee_code;
                    }

                    // Job title
                    if ($record->employee->designation) {
                        $jobTitle = $record->employee->designation->designation_name ?? 'N/A';
                    }

                    // Location
                    if ($record->employee->branch) {
                        $location = $record->employee->branch->branch_name ?? 'N/A';
                    }

                    // Sub program (department)
                    if ($record->employee->department) {
                        $subProgram = $record->employee->department->department_name ?? 'N/A';
                    }
                }

                // Get income frequency and payment reference from employee payroll
                if ($record->employeePayroll) {
                    $incomeFrequency = $record->employeePayroll->income_frequency ?? 'N/A';
                    $paymentReference = $record->employeePayroll->payment_method ?? 'N/A';
                }

                // Calculate values from details
                $overtimeTotal = 0;
                $salaryAdvance = 0;
                $unpaidAmount = 0;

                foreach ($record->details as $detail) {
                    // Calculate overtime
                    if (($detail->type === 'allowance' || $detail->type === 'earning') &&
                        $this->isOvertimeDetail($detail)
                    ) {
                        $overtimeTotal += $detail->amount;
                    }

                    // Calculate salary advance
                    if (
                        $detail->type === 'deduction' &&
                        (stripos($detail->name, 'salary advance') !== false || $detail->code === 'salary_advance')
                    ) {
                        $salaryAdvance += $detail->amount;
                    }

                    // Calculate unpaid
                    if (
                        $detail->type === 'deduction' &&
                        (stripos($detail->name, 'unpaid') !== false ||
                            stripos($detail->name, 'absenteeism') !== false ||
                            $detail->code === 'unpaid_leave' ||
                            $detail->code === 'absenteeism')
                    ) {
                        $unpaidAmount += $detail->amount;
                    }
                }

                // Calculate effective earning
                $effectiveEarning = ($record->gross_salary ?? 0) - $unpaidAmount;

                // Calculate deductions vs earnings percentage
                $deductionsVsEarnings = 0;
                if (($record->gross_salary ?? 0) > 0) {
                    $deductionsVsEarnings = round(($record->total_deductions ?? 0) / ($record->gross_salary ?? 1) * 100, 2);
                }

                // Calculate pension totals
                $pensionEmployeeTotal = $record->pension_contribution ?? 0;
                $pensionEmployerTotal = $record->pension_employer_contribution ?? 0;

                // Calculate company contribution total
                $companyContributionTotal = ($record->nssf_tier1_company_contribution ?? 0) +
                    ($record->nssf_tier2_company_contribution ?? 0) +
                    ($record->housing_levy_company_contribution ?? 0) +
                    ($record->shif_company_contribution ?? 0) +
                    ($record->industrial_training_levy ?? 0) +
                    $pensionEmployerTotal;

                // Calculate total cost
                $totalCost = ($record->gross_salary ?? 0) + $companyContributionTotal;

                // GET PROJECT ALLOCATIONS - ADD THIS SECTION
                $projectAllocations = $record->employee->projectAllocations()
                    ->with(['project', 'project.parent'])
                    ->where('status', 'active')
                    ->orderBy('percentage_allocated', 'desc')
                    ->get();

                // Process project allocations using the same method as in export
                $processedAllocations = $this->processProjectAllocations($projectAllocations);

                // TEMPORARY: If no allocations found, try without status filter
                if (empty($processedAllocations) && $record->employee->projectAllocations()->count() > 0) {
                    $allAllocations = $record->employee->projectAllocations()
                        ->with(['project', 'project.parent'])
                        ->orderBy('percentage_allocated', 'desc')
                        ->get();
                    $processedAllocations = $this->processProjectAllocations($allAllocations);
                }

                // Store the record data
                $recordData = [
                    // Employee basic info
                    'employee_code' => $employeeCode,
                    'employee_surname' => $record->employee->last_name ?? '',
                    'employee_first_name' => $record->employee->first_name ?? '',
                    'employee_second_name' => $record->employee->middle_name ?? '',
                    'job_title' => $jobTitle,
                    'location' => $location,
                    'sub_program' => $subProgram,
                    'income_frequency' => $incomeFrequency,

                    // Earnings section
                    'basic_income' => (float)($record->basic_salary ?? 0),
                    'overtime_totals' => (float)$overtimeTotal,
                    'earning_total' => (float)($record->gross_salary ?? 0),
                    'unpaid' => (float)$unpaidAmount,
                    'effective_earning' => (float)$effectiveEarning,

                    // Company contributions section
                    'nita' => (float)($record->industrial_training_levy ?? 0),
                    'nssf_tier1_company' => (float)($record->nssf_tier1_company_contribution ?? 0),
                    'nssf_tier2_company' => (float)($record->nssf_tier2_company_contribution ?? 0),
                    'total_nssf_company' => (float)(($record->nssf_tier1_company_contribution ?? 0) + ($record->nssf_tier2_company_contribution ?? 0)),
                    'housing_levy_company' => (float)($record->housing_levy_company_contribution ?? 0),

                    'pension_employer_total' => (float)$pensionEmployerTotal,
                    'company_contribution_total' => (float)$companyContributionTotal,
                    'total_cost' => (float)$totalCost,

                    // Deductions section
                    'paye' => (float)($record->paye_tax ?? 0),
                    'nssf_tier1_deduction' => (float)($record->nssf_tier1_contribution ?? 0),
                    'nssf_tier2_deduction' => (float)($record->nssf_tier2_contribution ?? 0),
                    'total_nssf_deduction' => (float)($record->nssf_contribution ?? 0),
                    'shif_deduction' => (float)($record->shif_contribution ?? 0),
                    'housing_levy_deduction' => (float)($record->housing_levy ?? 0),
                    'pension_employee_total' => (float)$pensionEmployeeTotal,
                    'salary_advance' => (float)$salaryAdvance,
                    'total_deductions' => (float)($record->total_deductions ?? 0),

                    // Final values section
                    'netpay' => (float)($record->net_salary ?? 0),
                    'deductions_vs_earnings' => $deductionsVsEarnings,
                    'payment_reference' => $paymentReference,

                    // Project allocations section
                    'primary_grant' => $processedAllocations['primary_grant'] ?? '',
                    'primary_loe' => $processedAllocations['primary_loe'] ?? '',
                    'sec_grant' => $processedAllocations['sec_grant'] ?? '',
                    'sec_loe' => $processedAllocations['sec_loe'] ?? '',
                    'tertiary_project' => $processedAllocations['tertiary_project'] ?? '',
                    'tertiary_loe' => $processedAllocations['tertiary_loe'] ?? '',

                    // Status
                    'status' => $record->payroll_record_status ?? 'Pending'
                ];

                // Add to processed records
                $processedPayrollRecords[$approval->id] = $recordData;

                // Calculate totals
                foreach ($summableColumns as $column) {
                    $totals[$column] += $recordData[$column] ?? 0;
                }
                $totalDeductionsVsEarnings += $deductionsVsEarnings;
                $recordCount++;
            }
        }

        // Create totals row (as the second row)
        $totalsRow = [];
        foreach ($payrollColumns as $key => $label) {
            if (in_array($key, $summableColumns)) {
                // Format numeric totals with 2 decimal places
                $totalsRow[$key] = number_format($totals[$key], 2);
            } elseif ($key === 'deductions_vs_earnings') {
                // Calculate average for deductions vs earnings percentage
                $average = $recordCount > 0 ? $totalDeductionsVsEarnings / $recordCount : 0;
                $totalsRow[$key] = round($average, 2) . '%';
            } else {
                // For non-numeric columns, show appropriate labels
                switch ($key) {
                    case 'employee_code':
                        $totalsRow[$key] = 'TOTALS';
                        break;
                    case 'employee_surname':
                    case 'employee_first_name':
                    case 'employee_second_name':
                        $totalsRow[$key] = '';
                        break;
                    case 'job_title':
                        $totalsRow[$key] = '';
                        break;
                    case 'location':
                    case 'sub_program':
                        $totalsRow[$key] = '';
                        break;
                    case 'income_frequency':
                        $totalsRow[$key] = '';
                        break;
                    case 'payment_reference':
                        $totalsRow[$key] = '';
                        break;
                    case 'primary_grant':
                    case 'sec_grant':
                    case 'tertiary_project':
                        $totalsRow[$key] = 'N/A';
                        break;
                    case 'primary_loe':
                    case 'sec_loe':
                    case 'tertiary_loe':
                        $totalsRow[$key] = '';
                        break;
                    case 'status':
                        $totalsRow[$key] = 'TOTAL';
                        break;
                    default:
                        $totalsRow[$key] = '';
                        break;
                }
            }
        }

        // Get delegation data for the view
        $myDelegations = \App\Models\ApprovalDelegation::with('delegate')
            ->where('user_id', auth()->id())
            ->orderBy('is_active', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $delegatedToMe = \App\Models\ApprovalDelegation::with('delegator')
            ->where('delegate_to_user_id', auth()->id())
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get pending leave applications where current user is the supervisor
        $pendingLeaveApprovals = [];
        $loggedInEmployee = employeeInfo();
        if ($loggedInEmployee) {
            $supervisorId = $loggedInEmployee->employee_id;
            $pendingLeaveApprovals = LeaveApplication::with(['employee', 'leaveType'])
                ->whereHas('employee', function ($query) use ($supervisorId) {
                    $query->where('supervisor_id', $supervisorId);
                })
                ->where('final_status', LeaveStatus::PENDING)
                ->orderBy('leave_application_id', 'desc')
                ->get();
        }

        return view('admin.ess.approvals.index', compact(
            'approvals',
            'submissions',
            'payrollRecordApprovals',
            'payrollRecordSubmissions',
            'payrollColumns',
            'processedPayrollRecords',
            'totalsRow',
            'myDelegations',
            'delegatedToMe',
            'pendingLeaveApprovals'
        ));
    }

    /**
     * Process project allocations for an employee
     * Consolidates sub-projects under main projects and assigns to PRIMARY/SEC/Tertiary
     */
    private function processProjectAllocations($projectAllocations)
    {
        if ($projectAllocations->isEmpty()) {
            return [];
        }

        // Group allocations by main project (consolidate sub-projects)
        $consolidatedProjects = [];

        foreach ($projectAllocations as $allocation) {
            $project = $allocation->project;

            // Get main project name (if it's a sub-project, use parent name)
            $mainProjectName = $project->parent ? $project->parent->name : $project->name;

            // Consolidate percentages under main project
            if (!isset($consolidatedProjects[$mainProjectName])) {
                $consolidatedProjects[$mainProjectName] = 0;
            }
            $consolidatedProjects[$mainProjectName] += $allocation->percentage_allocated;
        }

        // Sort by percentage (highest first) and assign to PRIMARY/SEC/Tertiary
        arsort($consolidatedProjects);
        $sortedProjects = array_keys($consolidatedProjects);
        $sortedPercentages = array_values($consolidatedProjects);

        $result = [];

        // Assign to PRIMARY GRANT
        if (count($sortedProjects) >= 1) {
            $result['primary_grant'] = $sortedProjects[0];
            $result['primary_loe'] = number_format($sortedPercentages[0], 2) . '%';
        }

        // Assign to SEC. GRANT
        if (count($sortedProjects) >= 2) {
            $result['sec_grant'] = $sortedProjects[1];
            $result['sec_loe'] = number_format($sortedPercentages[1], 2) . '%';
        }

        // Assign to Tertiary project
        if (count($sortedProjects) >= 3) {
            $result['tertiary_project'] = $sortedProjects[2];
            $result['tertiary_loe'] = number_format($sortedPercentages[2], 2) . '%';
        }

        return $result;
    }

    /**
     * Check if detail is overtime related
     */
    private function isOvertimeDetail($detail)
    {
        return stripos($detail->name, 'overtime') !== false ||
            stripos($detail->code, 'ot') !== false ||
            stripos($detail->name, 'OT') !== false;
    }

    /**
     * Check if detail is overtime related
     */

    public function approvalShow($modelType, $modelId)
    {
        $className = str_replace('_', '\\', $modelType);
        $model = $className::findOrFail($modelId);

        $approvalLogs = $model->approvalLogs()
            ->with(['user.employeeDetails', 'step'])
            ->orderBy('created_at', 'desc')
            ->get();

        $currentStep = $model->currentApprovalStep();

        return view('admin.ess.approvals.show', [
            'model' => $model,
            'currentStep' => $currentStep,
            'approvalLogs' => $approvalLogs
        ]);
    }

    public function diciplinary()
    {
       
        $data = DisciplinaryCase::where('employee_id', $this->employee->employee_id)
            ->orWhere('reporter_id', $this->employee->employee_id)->with('category')
            ->get();
        $ess = 'set';

        return view('admin.ess.disciplinary.index', compact('data', 'ess'));
    }

    public function diciplinaryDetails(DisciplinaryCase $disciplinary)
    {
        $ess = 'set';

        $caseActions = DisciplinaryCaseAction::where('case_id', $disciplinary->id)->get();
        return view('admin.ess.disciplinary.details', ['case' => $disciplinary, 'caseActions' => $caseActions, 'ess' => $ess]);
    }

    public function shifts()
    {
        $employee = $this->employee;
        $workShift = $employee->work_shift_id
            ? WorkShift::find($employee->work_shift_id)
            : null;

        return view('admin.ess.shifts.index', [
            'employee' => $employee,
            'workShift' => $workShift,
        ]);
    }

    public function payroll()
    {
        $results = SalaryDetails::with(['employee' => function ($query) {
            $query->with('employeePayroll');
        }])->where('employee_id', $this->employee->employee_id)->orderBy('salary_details_id', 'DESC')->get();

        return view('admin.payroll.report.myPayroll', ['results' => $results]);
    }

    public function leave()
    {
        $signed_in_user_role = $this->role;
        $supervisor = $this->supervisor;
        $hr = $this->hr;
        $results = LeaveApplication::with(['employee', 'leaveType', 'approveBy', 'rejectBy'])
            ->where('employee_id', $this->employee->employee_id)
            ->orderBy('leave_application_id', 'desc')
            ->paginate(10);

        return view('admin.ess.leave.index', [
            'results' => $results,
            'signed_in_user_role' => $signed_in_user_role,
            'hr' => $hr,
            'supervisor' => $supervisor,
            'ess' => 'set'
        ]);
    }

    public function leaveApplyForm(Request $request)
    {
        $signed_in_user_role = $this->role;
        $leaveTypeList = $this->employee->applicableLeaveTypes()->pluck('leave_type_name', 'leave_type_id');
        $getEmployeeInfo = $this->commonRepository->getEmployeeDetails($this->user->id);
        //$employeeList = $this->commonRepository->employeeListForLeaves();

        if (!$getEmployeeInfo->supervisor_id) {
            return redirect()->back()->with('error', 'You do not have a supervisor assigned. Please contact P&C to assign a supervisor before applying for leave.');
        }

        // Get current financial year
        $financialYear = FinancialYear::active()->first();
        if (!$financialYear) {
            return redirect()->back()->with('error', 'No active financial year found. Please contact administrator.');
        }

        // Format dates for JavaScript
        $financialYearStart = \Carbon\Carbon::parse($financialYear->start_date)->format('d/m/Y');
        $financialYearEnd = \Carbon\Carbon::parse($financialYear->end_date)->format('d/m/Y');

        return view('admin.ess.leave.form', [
            'leaveTypeList' => $leaveTypeList,
            'getEmployeeInfo' => $getEmployeeInfo,
            'signed_in_user_role' => $signed_in_user_role,
            //'employeeList' => $employeeList,
            'ess' => 'set',
            'financialYearStart' => $financialYearStart,
            'financialYearEnd' => $financialYearEnd,
            'financialYear' => $financialYear
        ]);
    }

    public function leaveStore(ApplyForLeaveRequest $request)
    {
        $user = Auth::user();
        $currentFinancialYear = getCurrentFinancialYear();
        $employee = $user->employeeDetails;
        $supervisor_email = null;
        $hr_email = null;
        $supervisor = $employee->supervisor;
        $branch = $employee->branch;
        $hrApprover = $employee->hr();

        // dd($user,$employee,$supervisor,$branch,$hrApprover);
        $input = $request->all();


        $getEmployeeEmail = $employee->email;
        $getEmployeeFirstName = $employee->first_name;
        $getEmployeeSecondName = $employee->ast_name;
        if ($supervisor) {
            $supervisor_email = $supervisor->email;
        }
        if ($hrApprover) {
            $hr_email = $hrApprover->email;
        }


        $input['application_from_date'] = dateConvertFormtoDB($request->application_from_date);
        $input['application_to_date'] = dateConvertFormtoDB($request->application_to_date);
        $input['application_date'] = date('Y-m-d');
        $input['financial_year_id'] = $currentFinancialYear->id;

        // Recalculate number of days respecting working_days vs calendar_days setting
        $calculatedDays = $this->leaveRepository->calculateTotalNumberOfLeaveDays(
            $input['application_from_date'],
            $input['application_to_date'],
            $request->leave_type_id,
            $employee->employee_id
        );
        $input['number_of_day'] = $calculatedDays;
        $existingLeave = LeaveApplication::where('employee_id', $request->employee_id)
            ->whereIn('final_status', [1, 2])
            ->where(function ($query) use ($input) {
                $query->whereBetween('application_from_date', [$input['application_from_date'], $input['application_to_date']])
                    ->orWhereBetween('application_to_date', [$input['application_from_date'], $input['application_to_date']])
                    ->orWhere(function ($query) use ($input) {
                        $query->where('application_from_date', '<=', $input['application_from_date'])
                            ->where('application_to_date', '>=', $input['application_to_date']);
                    });
            })->first();
        // dd($input['application_from_date']);
        if ($existingLeave) {
            // Return an error response or handle the case where an overlapping leave exists
            return redirect()->back()->with('error', 'You already have a leave application within this period. Please selec different periods');
        }

        //approval for the mks system
        $input['ceo_approval_type'] = LeaveStatus::PENDING;
        //$input['ceo_approval_date'] = date('Y-m-d');
        $input['hr_approval'] = LeaveStatus::PENDING;
        $input['final_status'] = LeaveStatus::PENDING;
        $input['status'] = LeaveStatus::PENDING;
        // $input['approve_date'] = date('Y-m-d');

        //continue to save the details
        try {
            $leaveApplication = LeaveApplication::create($input);
            $justification_file = $request->file('justification_file');

            if ($justification_file) {
                foreach ($justification_file as $leaveFile) {
                    // Generate a unique file name
                    $fileName = Str::random(20) . '.' . $leaveFile->getClientOriginalExtension();

                    // Move the file to the desired directory
                    $leaveFile->move('uploads/leaveApplication/', $fileName);

                    // Prepare the input data
                    $input = [
                        'leave_application_id' => $leaveApplication['leave_application_id'],
                        'file_name' => $fileName,
                        'employee_id' => Auth::user()->id,
                    ];

                    // Save the justification file record
                    LeaveJustification::create($input);
                }
            }
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            Log::info($e->getMessage());
            // dd($e);
        }

        $leaveType = LeaveType::where('leave_type_id', $request->leave_type_id)->pluck('leave_type_name');
        $leaveLatest = LeaveApplication::latest()->pluck('leave_application_id')->first();
        $mailContent = ([
            'leave_from_date' => $request->application_from_date,
            'staff_first_name' => $getEmployeeFirstName,
            'staff_last_name' => $getEmployeeSecondName,
            'leave_to_date' => $request->application_to_date,
            'no_of_days' => $input['number_of_day'],
            'latest_leave' => $leaveLatest,
            'leaveType' => $leaveType,
        ]);

        try {
            Mail::to($getEmployeeEmail)->send(new StaffLeaveApplicationMail($mailContent));
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' Staff Leave application email failed');
        }

        //send mail to supervisor
        try {
            Mail::to($supervisor_email)->send(new SupervisorLeaveApplicationMail($mailContent));
        } catch (Exception $e) {
            Log::info($e->getMessage() . ' Supervisor Leave application email failed');
        }

        // send notification to all P&C
        try {
            $approvers = $employee->getLocationLeaveApprovers();

            foreach ($approvers as $approver) {
                event(new LeaveApplicationEvent($leaveApplication, $approver->employee_id));

                // Check if user exists and is notifiable
                if ($approver->user && method_exists($approver->user, 'notify')) {
                    $approver->user->notify(new LeaveApplicationSubmitted($leaveApplication));
                } else {
                    Log::warning("Approver {$approver->employee_id} has no notifiable user account");
                }
            }
        } catch (Exception $e) {
            Log::error('Notifications to Leave application failed: ' . $e->getMessage(), [
                'employee_id' => $employee->employee_id,
                'error' => $e->getTraceAsString()
            ]);
        }


        if ($bug == 0) {
            return redirect()->route('ess.leave.index')->with('success', 'Leave application successfully send.');
        } else {
            return redirect()->back()->with('error', 'Some error found !, Please try again.');
        }
    }

    public function leaveBalance(Request $request)
    {
        $leave_type_id = $request->leave_type_id;
        $employee_id = $request->employee_id;

        if ($leave_type_id != '' && $employee_id != '') {
            $balanceData = $this->leaveRepository->calculateEmployeeLeaveBalanceWithAdvanced($leave_type_id, $employee_id);
            return response()->json($balanceData);
        } else {
            return response()->json(['error' => 'Invalid parameters'], 400);
        }
    }

    public function editLeave($id)
    {
        $leaveApplication = LeaveApplication::where('leave_application_id', $id)->first();
        if (!$leaveApplication) {
            return redirect()->route('ess.leave.index')->with('error', 'Leave application not found.');
        }

        $leaveTypeList = LeaveType::where('status', 1)->get();
        $getEmployeeInfo = Employee::where('employee_id', session('logged_session_data.employee_id'))->first();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.ess.leave.leave_edit_form', [
            'leaveApplication' => $leaveApplication,
            'leaveTypeList' => $leaveTypeList,
            'getEmployeeInfo' => $getEmployeeInfo,
            'signed_in_user_role' => $signed_in_user_role,
            'ess' => 'set'
        ]);
    }

    public function updateLeave(Request $request)
    {
        $leaveApplication = LeaveApplication::where('leave_application_id', $request->leave_application_id)->first();
        if (!$leaveApplication) {
            return redirect()->route('ess.leave.index')->with('error', 'Leave application not found.');
        }

        $input = $request->all();
        $input['application_from_date'] = dateConvertFormtoDB($request->application_from_date);
        $input['application_to_date'] = dateConvertFormtoDB($request->application_to_date);

        // Recalculate number of days respecting working_days vs calendar_days setting
        $calculatedDays = $this->leaveRepository->calculateTotalNumberOfLeaveDays(
            $input['application_from_date'],
            $input['application_to_date'],
            $request->leave_type_id,
            $leaveApplication->employee_id
        );
        $input['number_of_day'] = $calculatedDays;

        try {
            $leaveApplication->update($input);
            return redirect()->route('ess.leave.index')->with('success', 'Leave application updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update leave application.');
        }
    }

    public function deleteLeaveJustification(Request $request)
    {
        $justification = LeaveJustification::where('id', $request->id)->first();
        if ($justification) {
            $justification->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }

    public function recall($id)
    {
        $leaveApplication = LeaveApplication::where('leave_application_id', $id)->first();
        if (!$leaveApplication) {
            return redirect()->route('ess.leave.index')->with('error', 'Leave application not found.');
        }

        try {
            $leaveApplication->delete();
            return redirect()->route('ess.leave.index')->with('success', 'Leave application recalled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to recall leave application.');
        }
    }

    public function viewLeaveDetails($id)
    {
        $leaveApplicationData = LeaveApplication::with(['employee' => function ($q) {
            $q->with(['designation']);
        }])->with('leaveType')->where('leave_application_id', $id)->first(); //Removed : ->where('status',1)
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        if (!$leaveApplicationData) {
            return response()->view('errors.404', [], 404);
        }
        $supervisor_id = $leaveApplicationData->employee->supervisor_id;

        $supervisor_details = Employee::where('employee_id', $supervisor_id)->first();
        $currentBalance = $this->leaveRepository->calCulateEmployeeLeaveBalance($leaveApplicationData->leave_type_id, $leaveApplicationData->employee_id);
        return view('admin.ess.leave.leaveDetails', ['leaveApplicationData' => $leaveApplicationData, 'currentBalance' => $currentBalance, 'signed_in_user_role' => $signed_in_user_role, 'supervisor_id' => $supervisor_id, 'supervisor_details' => $supervisor_details]);
    }

    public function approveOrRejectLeave(Request $request)
    {
        $data  = LeaveApplication::findOrFail($request->leave_application_id);
        $input = $request->all();

        if ($request->status == LeaveStatus::APPROVE) {
            $input['approve_date'] = date('Y-m-d');
            $input['final_status'] = LeaveStatus::APPROVE;
            $input['approve_by']   = session('logged_session_data.employee_id');
        } else {
            $input['reject_date']  = date('Y-m-d');
            $input['final_status'] = LeaveStatus::REJECT;
            $input['reject_by']    = session('logged_session_data.employee_id');
        }

        try {
            $data->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            if ($request->status == LeaveStatus::APPROVE) {
                return redirect()->route('ess.approval.index')->with('success', 'Leave application approved successfully.');
            } else {
                return redirect()->route('ess.approval.index')->with('success', 'Leave application rejected successfully.');
            }
        } else {
            return redirect()->route('ess.approval.index')->with('error', 'An error occurred, please try again.');
        }
    }

    public function awards()
    {
        $employee = Employee::where('employee_id', session('logged_session_data.employee_id'))
            ->first();
        $results = EmployeeAward::where('employee_id', $employee->employee_id)->orderBy('employee_award_id', 'DESC')->get();
        return view('admin.ess.awards.index', [
            'results' => $results
        ]);
    }

    public function survey()
    {
        $employee = Employee::where('employee_id', session('logged_session_data.employee_id'))
            ->first();
        if ($employee) {
            // Convert string gender to enum value
            $genderValue = Gender::getValue($employee->gender);
            // Get all active surveys that target this employee
            $surveyList = Survey::where(function ($query) use ($employee) {
                // Surveys that end today or in the future
                $query->where('end_date', '>=', now()->format('Y-m-d'))
                    ->orWhereNull('end_date');
            })
                ->get()
                ->filter(function ($survey) use ($employee) {
                    return $survey->targetsEmployee($employee);
                });
        }
        return view('admin.ess.survey.index', [
            'results' => $surveyList
        ]);
    }

    public function showSurvey($id)
    {
        $survey = Survey::findOrFail($id);

        // Make sure the form_url exists
        if (!$survey->form_url) {
            return redirect()->back()->with('error', 'Form URL not found.');
        }

        // Redirect user to the Google Form responder page
        return redirect()->away($survey->form_url);
    }

    public function subordinates()
    {
        // Get logged-in employee
        $employee = employeeInfo();


        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found');
        }


        // Get all subordinates (direct and indirect)
        $subordinateIds = $employee->getAllSubordinateIds();


        // Fetch all subordinate employees with their department and designation
        $subordinates = Employee::with(['department', 'designation', 'workLocation'])
            ->whereIn('employee_id', $subordinateIds)
            ->get();

        return view('admin.ess.subordinates.index', compact('subordinates'));
    }

    public function myPayroll()
    {
        $employeeId = session('logged_session_data.employee_id');

        $results = \App\Models\Payroll\PayrollRecord::with(['employee.employeePayroll', 'payrollPeriod'])
            ->where('payroll_record_status', PayrollStatus::PAID)
            ->where('employee_id', $employeeId)
            ->orderBy('payroll_period_id', 'DESC')
            ->get();

        return view('admin.ess.payroll.myPayroll', ['results' => $results]);
    }
    public function generatePayslip($id)
    {
        $paySlipId = $id;
        $payrollRecord = \App\Models\Payroll\PayrollRecord::findOrFail($paySlipId);
        $payrollRecord->load([
            'employeePayroll.employee',
            'payrollPeriod',
            'details'
        ]);

        return view('admin.payroll.payslip', compact('payrollRecord'));
    }

    public function noticeBoard()
    {
        // Get all published notices with relationships loaded for targeted audience filtering
        $allNotices = Notice::with(['departments', 'regions', 'branches'])
            ->where('status', 'Published')
            ->orderBy('notice_id', 'DESC')
            ->get();

        // Filter notices that target the current employee
        $results = $allNotices->filter(function ($notice) {
            return $notice->targetsEmployee($this->employee);
        });

        return view('admin.ess.notice_board.index', compact('results'));
    }

    public function show($id)
    {
        $editModeData = Notice::with('createdBy')->where('notice_id', $id)->first();
        return view('admin.ess.notice_board.details', compact('editModeData'));
    }

    /**
     * Display approval delegation management page
     */
    public function approvalDelegations()
    {
        $user = auth()->user();

        // Get delegations where user is delegator
        $myDelegations = ApprovalDelegation::with('delegate')
            ->where('user_id', $user->id)
            ->orderBy('is_active', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get delegations where user is delegate
        $delegatedToMe = ApprovalDelegation::with('delegator')
            ->where('delegate_to_user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get available users to delegate to (exclude self and existing delegates)
        $existingDelegateIds = $myDelegations->pluck('delegate_to_user_id')->toArray();
        $availableUsers = User::where('id', '!=', $user->id)
            ->whereNotIn('id', $existingDelegateIds)
            ->with('employeeDetails')
            ->whereHas('employeeDetails', function ($query) {
                $query->where('status', 1);
            })
            ->get();

        // Get available workflows
        $workflows = ApprovalWorkflow::with('steps')->get();

        // Get model types that have workflows
        $modelTypes = ApprovalWorkflow::select('model_type')
            ->distinct()
            ->pluck('model_type')
            ->map(function ($type) {
                return class_basename($type);
            });

        return view('admin.ess.approvals.delegations', compact(
            'myDelegations',
            'delegatedToMe',
            'availableUsers',
            'workflows',
            'modelTypes'
        ));
    }

    /**
     * Store a new approval delegation
     */
    public function storeApprovalDelegation(StoreApprovalDelegationRequest $request)
    {
        try {
            $delegator = auth()->user();
            $delegate = User::findOrFail($request->delegate_to_user_id);

            $delegation = ApprovalDelegation::create([
                'user_id' => auth()->id(),
                'delegate_to_user_id' => $request->delegate_to_user_id,
                'model_type' => $request->model_type,
                'delegation_type' => $request->delegation_type,
                'workflow_id' => $request->workflow_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_active' => $request->boolean('is_active'),
                'include_submissions' => $request->boolean('include_submissions'),
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            // Load relationships for the notification
            $delegation->load(['workflow', 'delegate']);

            // Send notification to the delegate
            try {
                $delegate->notify(new \App\Notifications\ApprovalDelegationAssigned($delegation, $delegator, $delegate));
            } catch (\Exception $e) {
                Log::warning('Failed to send delegation notification: ' . $e->getMessage());
                // Continue execution - delegation was created successfully
            }

            return redirect()->back()->with('success', 'Delegation created successfully. The delegate has been notified via email.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create delegation: ' . $e->getMessage());
        }
    }

    /**
     * Update an approval delegation
     */
    public function updateApprovalDelegation(UpdateApprovalDelegationRequest $request, $id)
    {
        try {
            $delegator = auth()->user();
            $delegation = ApprovalDelegation::where('user_id', auth()->id())
                ->findOrFail($id);

            $delegate = User::findOrFail($delegation->delegate_to_user_id);

            $oldData = $delegation->only(['delegation_type', 'workflow_id', 'start_date', 'end_date', 'is_active', 'include_submissions']);

            $delegation->update([
                'model_type' => $request->model_type,
                'delegation_type' => $request->delegation_type,
                'workflow_id' => $request->workflow_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_active' => $request->boolean('is_active'),
                'include_submissions' => $request->boolean('include_submissions'),
                'notes' => $request->notes,
            ]);

            // Load relationships for the notification
            $delegation->load(['workflow', 'delegate']);

            // Check if significant changes were made that warrant notification
            $newData = $delegation->only(['delegation_type', 'workflow_id', 'start_date', 'end_date', 'is_active', 'include_submissions']);
            $hasSignificantChanges = ($oldData != $newData) ||
                ($oldData['is_active'] != $newData['is_active']) ||
                ($oldData['start_date'] != $newData['start_date']) ||
                ($oldData['end_date'] != $newData['end_date']);

            // Send notification if there are significant changes
            if ($hasSignificantChanges) {
                try {
                    $delegate->notify(new \App\Notifications\ApprovalDelegationAssigned($delegation, $delegator, $delegate));
                } catch (\Exception $e) {
                    Log::warning('Failed to send delegation update notification: ' . $e->getMessage());
                    // Continue execution - delegation was updated successfully
                }
            }

            return redirect()->back()->with('success', 'Delegation updated successfully.' . ($hasSignificantChanges ? ' The delegate has been notified of changes.' : ''));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update delegation: ' . $e->getMessage());
        }
    }

    /**
     * Delete an approval delegation
     */
    public function deleteApprovalDelegation($id)
    {
        try {
            $delegation = ApprovalDelegation::where('user_id', auth()->id())
                ->findOrFail($id);

            $delegation->delete();

            return redirect()->back()->with('success', 'Delegation deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete delegation: ' . $e->getMessage());
        }
    }

    /**
     * Toggle delegation status
     */
    public function toggleDelegationStatus($id)
    {
        try {
            $delegator = auth()->user();
            $delegation = ApprovalDelegation::where('user_id', auth()->id())
                ->findOrFail($id);

            $delegate = User::findOrFail($delegation->delegate_to_user_id);
            $oldStatus = $delegation->is_active;

            $delegation->update([
                'is_active' => !$delegation->is_active
            ]);

            // Load relationships for the notification
            $delegation->load(['workflow', 'delegate']);

            // Send notification about status change
            try {
                $delegate->notify(new \App\Notifications\ApprovalDelegationAssigned($delegation, $delegator, $delegate));
            } catch (\Exception $e) {
                Log::warning('Failed to send delegation status change notification: ' . $e->getMessage());
                // Continue execution - status was updated successfully
            }

            $statusText = $delegation->is_active ? 'activated' : 'deactivated';
            return redirect()->back()->with('success', "Delegation {$statusText} successfully. The delegate has been notified.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update delegation status.');
        }
    }

    /**
     * Edit approval delegation
     */
    public function editApprovalDelegation($id)
    {
        try {
            $delegation = ApprovalDelegation::where('user_id', auth()->id())
                ->with(['delegate', 'workflow'])
                ->findOrFail($id);

            // Get available users to delegate to (exclude self and existing delegates)
            $existingDelegateIds = ApprovalDelegation::where('user_id', auth()->id())
                ->where('id', '!=', $id)
                ->pluck('delegate_to_user_id')
                ->toArray();

            $availableUsers = User::where('id', '!=', auth()->id())
                ->whereNotIn('id', $existingDelegateIds)
                ->with('employeeDetails')
                ->whereHas('employeeDetails', function ($query) {
                    $query->where('status', 1);
                })
                ->get();

            // Get available workflows
            $workflows = ApprovalWorkflow::with('steps')->get();

            // Get model types that have workflows
            $modelTypes = ApprovalWorkflow::select('model_type')
                ->distinct()
                ->pluck('model_type')
                ->map(function ($type) {
                    return class_basename($type);
                });

            return view('admin.ess.approvals.edit_delegation', compact(
                'delegation',
                'availableUsers',
                'workflows',
                'modelTypes'
            ));
        } catch (\Exception $e) {
            return redirect()->route('ess.approval.delegations.index')->with('error', 'Delegation not found.');
        }
    }

    /**
     * Deactivate delegation (sets is_active to false)
     */
    public function deactivateDelegation($id)
    {
        try {
            $delegator = auth()->user();
            $delegation = ApprovalDelegation::where('user_id', auth()->id())
                ->findOrFail($id);

            $delegate = User::findOrFail($delegation->delegate_to_user_id);

            $delegation->update([
                'is_active' => false
            ]);

            // Load relationships for the notification
            $delegation->load(['workflow', 'delegate']);

            // Send notification about deactivation
            try {
                $delegate->notify(new \App\Notifications\ApprovalDelegationAssigned($delegation, $delegator, $delegate));
            } catch (\Exception $e) {
                Log::warning('Failed to send delegation deactivation notification: ' . $e->getMessage());
                // Continue execution - delegation was deactivated successfully
            }

            return redirect()->back()->with('success', 'Delegation deactivated successfully. The delegate has been notified.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to deactivate delegation.');
        }
    }

    /**
     * Show my vehicle and assignment history for ESS user
     */
    public function myVehicle()
    {
        $employee = $this->employee;

        if (!$employee || !$employee->employee_id) {
            return view('admin.ess.vehicle.my_vehicle', [
                'employee' => null,
                'currentAssignment' => null,
                'assignments' => collect(),
            ])->with('warning', 'No employee record found for your account.');
        }

        // Get all assignments for this employee
        $assignments = VehicleAssignment::with(['vehicle', 'assignedBy', 'returnedBy'])
            ->where('employee_id', $employee->employee_id)
            ->orderBy('assigned_from', 'desc')
            ->get();

        // Get current assignment if any
        $currentAssignment = VehicleAssignment::with(['vehicle'])
            ->where('employee_id', $employee->employee_id)
            ->whereNull('assigned_to')
            ->first();

        return view('admin.ess.vehicle.my_vehicle', compact(
            'employee',
            'assignments',
            'currentAssignment'
        ));
    }

    /**
     * Show my performance appraisals for ESS user
     */
    public function myAppraisals()
    {
        $employee = $this->employee;

        if (!$employee || !$employee->employee_id) {
            return view('admin.ess.performance.my_appraisals', [
                'results' => collect(),
            ])->with('warning', 'No employee record found for your account.');
        }

        // Get appraisals where this user is the employee or supervisor
        $results = PerformanceAppraisal::with(['employee', 'supervisor'])
            ->where('employee_id', $employee->employee_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.ess.performance.my_appraisals', compact('results'));
    }

    /**
     * Redirect to the first available self-evaluation appraisal
     */
    public function goToSelfEvaluation()
    {
        $employee = $this->employee;

        if (!$employee || !$employee->employee_id) {
            return redirect()->route('ess.performance.myAppraisals')->with('error', 'No employee record found.');
        }

        // Find the first appraisal with status 'draft' or 'self_review'
        $appraisal = PerformanceAppraisal::where('employee_id', $employee->employee_id)
            ->whereIn('status', ['draft', 'self_review'])
            ->orderBy('created_at', 'desc')
            ->first();
           

        if (!$appraisal) {
            return redirect()->route('ess.performance.myAppraisals')
                ->with('error', 'No pending self-evaluation available. Please check your appraisal list for current status.');
        }

        return redirect()->route('ess.performance.selfReview', $appraisal->appraisal_id);
    }

    /**
     * Show self review form for ESS user
     */
    public function selfReview($id)
    {
        $employee = $this->employee;

        if (!$employee || !$employee->employee_id) {
            return redirect()->route('ess.performance.myAppraisals')->with('error', 'No employee record found.');
        }

        $appraisal = PerformanceAppraisal::with([
            'scores.goal.focusArea',
            'behavioralScores.behavioralItem',
            'employee'
        ])
            ->where('employee_id', $employee->employee_id)
            ->findOrFail($id);

        // ENFORCE WORKFLOW: Self review only available in 'draft' or 'self_review' status
        // Once supervisor has started reviewing (status = 'supervisor_review' or beyond), self review is locked
        if (!in_array($appraisal->status, ['draft', 'self_review'])) {
            return redirect()->route('ess.performance.myAppraisals')
                ->with('error', 'Self evaluation is no longer available. The appraisal has moved to ' . str_replace('_', ' ', $appraisal->status) . ' phase.');
        }

        // Group by focus area
        $focusAreaScores = [];
        foreach ($appraisal->scores as $score) {
            $faId = $score->goal ? $score->goal->focus_area_id : 0;
            if (!isset($focusAreaScores[$faId])) {
                $focusAreaScores[$faId] = [
                    'focusArea' => $score->goal ? $score->goal->focusArea : null,
                    'scores' => [],
                ];
            }
            $focusAreaScores[$faId]['scores'][] = $score;
        }

        return view('admin.ess.performance.self_review', compact('appraisal', 'focusAreaScores'));
    }

    /**
     * Save self review for ESS user
     */
    public function saveSelfReview(Request $request, $id)
    {
        $employee = $this->employee;

        if (!$employee || !$employee->employee_id) {
            return redirect()->route('ess.performance.myAppraisals')->with('error', 'No employee record found.');
        }

        $appraisal = PerformanceAppraisal::where('employee_id', $employee->employee_id)->findOrFail($id);

        // ENFORCE WORKFLOW: Self review only available in 'draft' or 'self_review' status
        if (!in_array($appraisal->status, ['draft', 'self_review'])) {
            return redirect()->route('ess.performance.myAppraisals')
                ->with('error', 'Cannot save self evaluation. The appraisal has moved to ' . str_replace('_', ' ', $appraisal->status) . ' phase.');
        }

        $scores = $request->input('scores', []);
        $comments = $request->input('comments', []);

        foreach ($scores as $scoreId => $selfWeighting) {
            $score = PerformanceAppraisalScore::find($scoreId);
            if ($score) {
                $score->self_weighting = $selfWeighting;
                $score->self_comments = $comments[$scoreId] ?? null;
                $score->save();
            }
        }

        // Behavioral scores
        $behavioralScores = $request->input('behavioral_scores', []);
        $behavioralComments = $request->input('behavioral_comments', []);

        foreach ($behavioralScores as $scoreId => $selfWeighting) {
            $score = PerformanceAppraisalBehavioralScore::find($scoreId);
            if ($score) {
                $score->self_weighting = $selfWeighting;
                $score->self_comments = $behavioralComments[$scoreId] ?? null;
                $score->save();
            }
        }

        $appraisal->status = 'self_review';
        $appraisal->save();

        return redirect()->route('ess.performance.myAppraisals')->with('success', 'Self review saved successfully.');
    }

    /**
     * Submit self review for ESS user - Final submission to supervisor
     */
    public function submitSelfReview(Request $request, $id)
    {
        $employee = $this->employee;

        if (!$employee || !$employee->employee_id) {
            return redirect()->route('ess.performance.myAppraisals')->with('error', 'No employee record found.');
        }

        $appraisal = PerformanceAppraisal::where('employee_id', $employee->employee_id)->findOrFail($id);

        // ENFORCE WORKFLOW: Can only submit self review in 'draft' or 'self_review' status
        if (!in_array($appraisal->status, ['draft', 'self_review'])) {
            return redirect()->route('ess.performance.myAppraisals')
                ->with('error', 'Cannot submit self evaluation. The appraisal has already moved to ' . str_replace('_', ' ', $appraisal->status) . ' phase.');
        }

        // Mark as submitted for supervisor review
        $appraisal->status = 'self_review';
        $appraisal->employee_submitted_at = now();
        $appraisal->save();

        return redirect()->route('ess.performance.myAppraisals')->with('success', 'Self evaluation submitted successfully. Your supervisor will now review it.');
    }

    /**
     * Show appraisal details for ESS user
     */
    public function showAppraisal($id)
    {
        $employee = $this->employee;

        if (!$employee || !$employee->employee_id) {
            return redirect()->route('ess.performance.myAppraisals')->with('error', 'No employee record found.');
        }

        $appraisal = PerformanceAppraisal::with([
            'employee',
            'supervisor',
            'scores.goal.focusArea',
            'behavioralScores.behavioralItem',
            'developmentPlans',
            'learningPlans',
            'pipPlans'
        ])
            ->where(function ($query) use ($employee) {
                $query->where('employee_id', $employee->employee_id)
                    ->orWhere('supervisor_id', $employee->employee_id);
            })
            ->findOrFail($id);

        // Group scores by focus area for display
        $focusAreaScores = [];
        foreach ($appraisal->scores as $score) {
            $faId = $score->goal ? $score->goal->focus_area_id : 0;
            if (!isset($focusAreaScores[$faId])) {
                $focusAreaScores[$faId] = [
                    'focusArea' => $score->goal ? $score->goal->focusArea : null,
                    'scores' => [],
                    'self_total' => 0,
                    'review_total' => 0,
                ];
            }
            $focusAreaScores[$faId]['scores'][] = $score;
            $focusAreaScores[$faId]['self_total'] += $score->self_weighting;
            $focusAreaScores[$faId]['review_total'] += $score->review_weighting;
        }

        return view('admin.ess.performance.show', compact('appraisal', 'focusAreaScores'));
    }

    /**
     * Show my PIP plans for ESS user
     */
    public function myPipPlans()
    {
        $employee = $this->employee;

        if (!$employee || !$employee->employee_id) {
            return view('admin.ess.pip.my_plans', [
                'results' => collect(),
            ])->with('warning', 'No employee record found for your account.');
        }

        // Get PIP plans where this user is the employee
        $results = PipPlan::with(['employee', 'supervisor', 'hrManager'])
            ->where('employee_id', $employee->employee_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.ess.pip.my_plans', compact('results'));
    }

    /**
     * Show PIP plan details for ESS user
     */
    public function showPipPlan($id)
    {
        $employee = $this->employee;

        if (!$employee || !$employee->employee_id) {
            return redirect()->route('ess.pip.myPlans')->with('error', 'No employee record found.');
        }

        $plan = PipPlan::with([
            'employee',
            'supervisor',
            'hrManager',
            'appraisal',
            'concerns.goal',
            'concerns.behavioralItem',
            'concerns.appraisalScore',
            'goals',
            'supportResources',
            'reviewSchedules.conductor',
        ])
            ->where('employee_id', $employee->employee_id)
            ->findOrFail($id);

        return view('admin.ess.pip.show', compact('plan'));
    }

    /**
     * Display scheduled leaves for the logged-in employee (ESS view).
     */
    public function employeeScheduledLeaves()
    {
      
        $employee = Employee::where('employee_id', session('logged_session_data.employee_id'))->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found.');
        }

        $upcomingSchedules = LeaveSchedule::with('leaveType')
            ->forEmployee($employee->employee_id)
            ->where('status', 'scheduled')
            ->where('scheduled_from_date', '>=', now())
            ->orderBy('scheduled_from_date', 'asc')
            ->get();

        $pastSchedules = LeaveSchedule::with('leaveType')
            ->forEmployee($employee->employee_id)
            ->where(function($query) {
                $query->where('status', 'completed')
                      ->orWhere('scheduled_from_date', '<', now());
            })
            ->orderBy('scheduled_from_date', 'desc')
            ->limit(10)
            ->get();

        return view('admin.ess.leave.scheduledLeaves', [
            'upcomingSchedules' => $upcomingSchedules,
            'pastSchedules' => $pastSchedules,
            'employee' => $employee
        ]);
    }

    /**
     * Display available job posts for ESS.
     */
    public function jobPosts()
    {
        $results = Job::where('status', 1)
            ->where('application_end_date', '>=', date('Y-m-d'))
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.ess.recruitments.job_posts', [
            'results' => $results
        ]);
    }

    /**
     * Display job post details for ESS.
     */
    public function jobPostDetails($id)
    {
        $job = Job::find($id);
        if (!$job || $job->status != 1 || $job->application_end_date < now()) {
            return redirect()->back()->with('error', 'The job you are trying to view is not available.');
        }

        $employee = Employee::where('employee_id', session('logged_session_data.employee_id'))->first();

        return view('admin.ess.recruitments.job_post_details', [
            'job' => $job,
            'employee' => $employee
        ]);
    }

    /**
     * Display HR documents for ESS users.
     */
    public function documents(Request $request)
    {
        $employeeId = $this->employee->employee_id ?? null;

        $documents = HrDocument::with('category')
            ->whereNotNull('approved_by')
            ->orderBy('created_at', 'desc')
            ->get();

        $documentConsents = [];
        if ($employeeId) {
            foreach ($documents as $doc) {
                $documentConsents[$doc->id] = DocumentConsent::hasConsented($doc->id, $employeeId);
            }
        }

        if ($request->has('doc_id')) {
            $document = HrDocument::with('category')
                ->whereNotNull('approved_by')
                ->findOrFail($request->doc_id);

            $hasConsented = $employeeId ? DocumentConsent::hasConsented($document->id, $employeeId) : false;
            $consent = $employeeId ? DocumentConsent::getConsent($document->id, $employeeId) : null;

            return view('admin.ess.docs.view', compact('document', 'hasConsented', 'consent'));
        }

        return view('admin.ess.docs.index', compact('documents', 'documentConsents'));
    }

    /**
     * Acknowledge a document for the logged-in employee.
     */
    public function acknowledgeDocument(Request $request, $id)
    {
        $employee = $this->employee;

        if (!$employee || !$employee->employee_id) {
            return response()->json(['status' => 'error', 'message' => 'Employee record not found.'], 400);
        }

        $document = HrDocument::whereNotNull('approved_by')->findOrFail($id);

        if (DocumentConsent::hasConsented($document->id, $employee->employee_id)) {
            return response()->json(['status' => 'error', 'message' => 'You have already acknowledged this document.'], 400);
        }

        try {
            DocumentConsent::create([
                'document_id' => $document->id,
                'employee_id' => $employee->employee_id,
                'user_id' => auth()->id(),
                'consented_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'acknowledgment_text' => $request->input('acknowledgment_text', 'I have read and understood this document and agree to abide by the terms stated therein.'),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Document acknowledged successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to acknowledge document.'], 500);
        }
    }

    /**
     * Serve an approved document file from storage.
     */
    public function serveDocument($id)
    {
        $document = HrDocument::whereNotNull('approved_by')->findOrFail($id);

        if (!$document->file_path || !Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'Document file not found.');
        }

        $file = Storage::disk('local')->get($document->file_path);
        $mimeType = Storage::disk('local')->mimeType($document->file_path);
        $filename = basename($document->file_path);

        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    /**
     * Handle internal job application from ESS.
     */
    public function jobApply(Request $request)
    {
        try {
            $job = Job::findOrFail($request->job_id);
            if (!$job || $job->status != 1 || $job->application_end_date < now()) {
                return redirect()->back()->with('error', 'The job you are applying for is not available.');
            }

            // Handle file upload
            $resumePath = $request->file('resume')->store('resumes', 'public');

            // Create application with all fields
            $application = JobApplicant::create([
                'job_id' => $job->job_id,
                'applicant_name' => $request->name,
                'applicant_email' => $request->email,
                'phone' => $request->phone,
                'cover_letter' => $request->input('cover_letter', ''),
                'attached_resume' => $resumePath,
                'years_of_experience' => $request->years_of_experience,
                'highest_qualification' => $request->highest_qualification,
                'location_id' => $job->location_id,
                'application_source' => 'internal',
                'application_date' => now(),
            ]);

            // Send confirmation email to applicant
            try {
                Mail::to($request->email)->send(new JobApplicationConfirmation(
                    $application,
                    $job->load(['branch', 'createdBy']),
                    $application
                ));
            } catch (Exception $e) {
                Log::info('Job application confirmation email failed: ' . $e->getMessage());
            }

            // Send notification to HR admins
            $hrAdmins = Employee::whereHas('user.roles', function ($q) {
                $q->where('name', 'HR Administrator');
            })->where('status', 1)->with('user')->get()
                ->pluck('email')
                ->filter()
                ->unique();

            if ($hrAdmins->isNotEmpty()) {
                try {
                    Mail::to($hrAdmins)->send(new NewApplicationHrNotification(
                        $application,
                        $job
                    ));
                } catch (Exception $e) {
                    Log::info('HR notification email failed: ' . $e->getMessage());
                }
            }

            return redirect()->route('ess.recruitment.job.posts')->with('success', 'Your application has been submitted successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
       public function trainings(Request $request)
    {

        $login_employee = employeeInfo();
        if ($login_employee) {
            if ($request->training_id) {
                $trainingTypeList = TrainingType::all();
                $facilitatorList = TrainingFacilitator::all();
                $training = Training::whereId($request->training_id)->first();

                // Get invitation status for this training
                $invitationStatus = null;
                if ($login_employee->trainingInvites()->where('training_id', $request->training_id)->exists()) {
                    $invitationStatus = $this->employee->trainingInvites()
                        ->where('training_id', $request->training_id)
                        ->first()
                        ->status;
                }

                return view('admin.training.employeeTraining.form')->with([
                    'trainingTypeList' => $trainingTypeList,
                    'facilitatorList' => $facilitatorList,
                    'ess' => 'ess',
                    'editModeData' => $training,
                    'showOnly' => 1,
                    'invitationStatus' => $invitationStatus
                ]);
            }

            // Get all training invites with their status
            $invitations = $login_employee->trainingInvites()
                ->with(['training', 'training.trainingType'])
                ->orderBy('id', 'DESC')
                ->get();

            // Get pending invitations
            $send_invitations = $login_employee->trainingInvites()
                ->where('status', TrainingInvitationStatus::SENT)
                ->orderBy('id', 'DESC')
                ->get();

            // Get all attended trainings
            $attendances = $login_employee->trainingAttendances()
                ->with(['training', 'training.trainingType'])
                ->orderBy('id', 'DESC')
                ->get();

            // Create a collection with all trainings (invited + attended)
            $all_trainings = $invitations->merge($attendances)
                ->unique('training_id')
                ->sortByDesc('id');


            return view('admin.ess.trainings.index')->with([
                'all_trainings' => $all_trainings,
                'invitations' => $invitations,
                'invitations_sent' => $send_invitations,
                'attendances' => $attendances,
                'employee' => $login_employee,
            ]);
        }
    }
     public function showTraining($id)
    {
        $training = Training::with(['trainingType', 'facilitator'])
            ->findOrFail($id);

        // Check if the current employee is invited to this training
        $invitationStatus = null;
        $login_employee = employeeInfo();
        $employee = [];
        if ($login_employee) {
            $invitationStatus = TrainingInvitee::where('training_id', $id)
                ->where('employee_id',  $login_employee->employee_id)
                ->first();
            $employee =  $login_employee;
        }
        return view('admin.ess.trainings.view', [
            'employeeList' => $this->commonRepository->employeeList(),
            'trainingTypeList' => $this->commonRepository->trainingTypeList(),
            'facilitatorList' => $this->commonRepository->trainingFacilitorList(),
            'showOnly' => true, // Force showOnly mode
            'editModeData' => $training,
            'start_date' => $training->start_date ? Carbon::parse($training->start_date)->format('Y-m-d') : null,
            'end_date' => $training->end_date ? Carbon::parse($training->end_date)->format('Y-m-d') : null,
            'invitationStatus' => $invitationStatus, // Pass invitation status to view
            'ess' => 'ess',
            'employee' => $employee
        ]);
    }
 public function handleInvitationResponse(Request $request, Training $training, Employee $employee, string $status)
    {
        // Validate the status (optional but recommended)
        if (!in_array($status, ['accepted', 'declined'])) {
            return redirect()->route('ess.trainings.index')->with('error', 'Invalid invitation response status.');
        }

        // Parse with the same timezone (e.g., 'Africa/Nairobi' or your local TZ)
        $startDate = Carbon::parse($training->start_date)->timezone(config('app.timezone'))->startOfDay();

        $today = Carbon::now(config('app.timezone'))->startOfDay();

        // Check if training has started
        if ($today->gt($startDate)) {
            return redirect()->route('ess.trainings.index')->with('error', 'This invitation is no longer active');
        }

        // Check if already responded
        // $invite = TrainingInvitee::where([
        //     'training_id' => $training->id,
        //     'employee_id' => $employee->employee_id
        // ])->firstOrFail();

        // if ($invite && $invite->responded_at) {
        //     return redirect()->route('ess.trainings.index')->with('error', 'You have already responded to this invitation');
        // }

        // Process response
        TrainingInvitee::updateOrCreate(
            [
                'training_id' => $training->id,
                'employee_id' => $employee->employee_id
            ],
            [
                'status' => $status === 'accepted'
                    ? TrainingInvitationStatus::ACCEPTED
                    : TrainingInvitationStatus::DECLINED,
                'responded_at' => now(),
                'responded_from' => $request->ip()
            ]
        );

        // Redirect to attendance confirmation if accepted
        if ($status === 'accepted') {
            return redirect()->route('ess.trainings.attendance.confirm', [
                'training' => $training->id,
                'employee' => $employee->employee_id
            ]);
        }

        return redirect()
            ->route('ess.trainings.index')
            ->with([
                'error' => $training->subject . ' invitation has been declined'
            ]);
    }

    public function showTrainingAttendanceConfirmation(Training $training, Employee $employee)
    {
        return view('admin.ess.trainings.attendance_confirmation', [
            'training' => $training,
            'employee' => $employee
        ]);
    }

    public function handleAttendanceResponse(Request $request, Training $training, Employee $employee)
    {
        $validated = $request->validate([
            'status' => 'required|in:confirmed,declined'
        ]);

        $status = TrainingAttendanceStatus::getValue($validated['status']);


        $attendance = TrainingAttendant::updateOrCreate(
            [
                'training_id' => $training->id,
                'employee_id' => $employee->employee_id
            ],
            [
                'status' => $status,
                'responded_at' => now()
            ]
        );

        if ($status === TrainingAttendanceStatus::CONFIRMED) {
            try {
                Mail::to($employee->email)
                    ->send(new TrainingConfirmationMail($training, $employee));

                return redirect()->route('ess.trainings.index')
                    ->with(['success' => $training->subject . ' attendance has been confirmed']);
            } catch (\Exception $e) {
                // Log the error for debugging
                Log::error('Failed to send training confirmation email: ' . $e->getMessage());

                // Still return success response but notify admin about email failure
                return redirect()->route('ess.trainings.index')
                    ->with([
                        'success' => $training->subject . ' attendance confirmed, but the confirmation email failed to send',
                        'warning' => 'Please notify the administrator about this issue'
                    ]);
            }
        }

        return redirect()->route('ess.trainings.index')->with(['error' => $training->subject . ' attendance has been declined']);
    }
}