<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Lib\Enumerations\GeneralStatus;
use App\Lib\Enumerations\PayrollStatus;
use App\Models\Employee;
use App\Models\Company;
use App\Models\Payroll\PayrollPeriod;
use App\Models\Payroll\PayrollRecord;
use App\Models\Payroll\EmployeePayroll;
use App\Services\Payroll\KenyanPayrollCalculationService;
use App\Services\Payroll\PayrollCalculationServiceResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;
use App\Mail\Payroll\SendPayslipEmail;
use App\Helpers\ProgressHelper;
use App\Jobs\ProcessBulkPayrollSubmission;
use App\Jobs\ProcessPayrollJob;
use App\Models\Department;
use App\Models\LeaveAdjustment;
use App\Models\FinancialYear;
use App\Models\LeaveType;
use App\Support\CompanyContext;

class PayrollController extends Controller
{
    protected PayrollCalculationServiceResolver $payrollCalculationResolver;

    public function __construct(PayrollCalculationServiceResolver $payrollCalculationResolver)
    {
        $this->payrollCalculationResolver = $payrollCalculationResolver;
    }

    /**
     * Display payroll dashboard
     */
    public function dashboard()
    {
        $currentPeriod = PayrollPeriod::where('is_current', 1)->orderBy('start_date', 'desc')->first();

        // Get summary data for charts - pass the period ID, not the month string
        $periodId = $currentPeriod ? $currentPeriod->id : null;
        $chartData = $this->getReportsChartData($periodId);

        // Get available payroll periods for dropdown
        $periods = PayrollPeriod::orderBy('start_date', 'desc')->take(12)->get();

        // Get recent payroll activities
        $recentActivities = PayrollRecord::with(['employeePayroll.employee', 'payrollPeriod'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Monthly trends data for charts
        $monthlyTrends = $this->getMonthlyTrends();

        // Get stats for statutory deductions (used in view)
        $stats = [
            'total_gross_salary' => $chartData['report_summary']['total_gross'] ?? 0,
            'total_deductions' => $chartData['report_summary']['total_deductions'] ?? 0,
            'total_net_salary' => $chartData['report_summary']['total_net'] ?? 0,
            'total_paye' => $chartData['report_summary']['total_paye'] ?? 0,
            'total_nssf' => $chartData['report_summary']['total_nssf'] ?? 0,
            'total_shif' => $chartData['report_summary']['total_shif'] ?? 0,
            'total_housing_levy' => $chartData['report_summary']['total_housing_levy'] ?? 0,
        ];

        return view('admin.payroll.dashboard', compact('chartData', 'periods', 'currentPeriod', 'recentActivities', 'monthlyTrends', 'stats'));
    }

    /**
     * Display payroll index
     */
    public function index(Request $request)
    {
        $query = PayrollRecord::with(['employeePayroll.employee', 'payrollPeriod']);
        $currentPeriod = PayrollPeriod::where('is_current', true)->first();
        // Apply filters
        if ($request->filled('period_id')) {
            $query->where('payroll_period_id', $request->period_id);
        } else {
            $query->where('payroll_period_id', $currentPeriod->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $monthYear = Carbon::createFromFormat('Y-m', $request->month);
            $query->whereHas('payrollPeriod', function ($q) use ($monthYear) {
                $q->whereYear('start_date', $monthYear->year)
                    ->whereMonth('start_date', $monthYear->month);
            });
        }

        $payrollRecords = $query->orderBy('created_at', 'desc')->get();
        $periods = PayrollPeriod::orderBy('start_date', 'desc')->get();


        return view('admin.payroll.index', compact('payrollRecords', 'periods'));
    }

    /**
     * Show payroll processing form
     */
    public function showProcessForm()
    {
        $currentPeriod = PayrollPeriod::getCurrentPeriod();
        $periods = PayrollPeriod::where('status', PayrollPeriod::STATUS_OPEN)
            ->orderBy('start_date', 'desc')
            ->get();

        $companies = $this->getPayrollCompanies();
        $employees = $this->employeesReadyForPayrollQuery()
            ->with(['employeePayroll', 'currentPayrollRecord', 'company'])
            ->get();

        $totalEmployees = $employees->count();

        // Calculate period statistics
        $periodStats = null;
        if ($currentPeriod) {
            $periodStats = $this->calculatePeriodStatistics($currentPeriod);
        }

        // Calculate payroll status statistics for current period
        $payrollStats = null;
        if ($currentPeriod) {
            $payrollStats = $this->calculatePayrollStatusStatistics($currentPeriod);
        }

        return view('admin.payroll.process', compact(
            'currentPeriod',
            'periods',
            'employees',
            'totalEmployees',
            'periodStats',
            'payrollStats',
            'companies'
        ));
    }

    /**
     * Process payroll for current period
     */
    public function processPayroll(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:payroll_periods,id',
            'process_all_companies' => 'nullable|boolean',
            'company_ids' => 'nullable|array',
            'company_ids.*' => 'integer|exists:companies,id',
        ]);

        $period = PayrollPeriod::findOrFail($request->period_id);

        if (!$period->canBeProcessed()) {
            return redirect()->back()->with('error', 'This payroll period cannot be processed.');
        }

        $companyIds = $this->resolvePayrollCompanyIds($request);
        if ($companyIds === false) {
            return response()->json([
                'success' => false,
                'message' => 'Please select at least one company to process payroll for.',
            ], 422);
        }

        try {
            // Generate batch ID for progress tracking
            $batchId = ProgressHelper::generateBatchId();

            // Dispatch background job
            ProcessPayrollJob::dispatch(
                $period->id,
                $batchId,
                auth()->id(),
                auth()->user()->email,
                $request->has('recalculate_existing'),
                $companyIds
            );

            $companyMessage = $companyIds === null
                ? 'all companies'
                : count($companyIds) . ' selected ' . (count($companyIds) === 1 ? 'company' : 'companies');

            return response()->json([
                'success' => true,
                'message' => 'Payroll processing started in background for ' . $companyMessage . '. You will receive an email notification when complete.',
                'batch_id' => $batchId
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error starting payroll processing: ' . $e->getMessage()
            ], 500);
        }
    }
    public function processSinglePayroll($period, $employeeID)
    {

        $period = PayrollPeriod::findOrFail($period);

        if (!$period->canBeProcessed()) {
            return redirect()->back()->with('error', 'This payroll period cannot be processed.');
        }
        //check of there's a record already and if it is approved or paid. Then return with error. 
        $existingRecord = PayrollRecord::where('payroll_period_id', $period->id)
            ->where('employee_id', $employeeID)
            ->first();
        if ($existingRecord && in_array($existingRecord->payroll_record_status, [PayrollStatus::APPROVED, PayrollStatus::PAID])) {
            return redirect()->back()->with('error', 'Payroll for this employee has already been approved or paid.');
        }



        try {
            DB::beginTransaction();

            $employee = Employee::findOrFail($employeeID);
            $payrollService = $this->payrollCalculationResolver->resolveForEmployee($employee);
            $results = $payrollService->calculatePeriodPayrollForOneEmployee($period, $employeeID);

            $successCount = collect($results)->where('status', 'success')->count();
            $errorCount = collect($results)->where('status', 'error')->count();

            // Update period status
            // $period->update(['status' => PayrollPeriod::STATUS_PROCESSING]);

            DB::commit();

            $message = "Payroll processed successfully. {$successCount} employees processed";
            if ($errorCount > 0) {
                $message .= ", {$errorCount} errors occurred";
            }


            //  return redirect()->back()->with('success', $message);
            return redirect()->route('payroll.show', $results['payroll_record_id'])->with('success', $message);
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Error processing payroll: ' . $e->getMessage());
        }
    }

    /**
     * Approve payroll records
     */
    public function approvePayroll(Request $request)
    {
        $request->validate([
            'record_ids' => 'required|array',
            'record_ids.*' => 'exists:payroll_records,id'
        ]);

        try {
            DB::beginTransaction();

            $records = PayrollRecord::whereIn('id', $request->record_ids)
                ->where('status', PayrollRecord::STATUS_CALCULATED)
                ->get();

            foreach ($records as $record) {
                $record->update([
                    'status' => PayrollRecord::STATUS_APPROVED,
                    'approved_by' => auth()->id()
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', count($records) . ' payroll records approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error approving payroll: ' . $e->getMessage());
        }
    }

    /**
     * Mark payroll as paid
     */
    public function markAsPaid(Request $request)
    {

        $request->validate([
            'record_ids' => 'required|array',
            'record_ids.*' => 'exists:payroll_records,id',
            'payment_reference' => 'nullable|string|max:255',
            'payment_date' => 'nullable|date'
        ]);


        try {
            DB::beginTransaction();

            // Use payroll_record_status (integer) instead of status (string)
            // to match what the UI checks (PayrollStatus::APPROVED = 2)
            $records = PayrollRecord::whereIn('id', $request->record_ids)
                ->where('payroll_record_status', PayrollStatus::APPROVED)
                ->get();

            if ($records->isEmpty()) {
                // Check if records exist but have wrong status
                $existingRecords = PayrollRecord::whereIn('id', $request->record_ids)->get();
                $statuses = $existingRecords->pluck('payroll_record_status')->unique()->toArray();

                return redirect()->back()->with('error',
                    'No records marked as paid. Records must be approved before payment. ' .
                    'Current status(es): ' . implode(', ', array_map(fn($s) => PayrollStatus::getName($s), $statuses))
                );
            }

            foreach ($records as $record) {

                $record->markAsPaid(
                    $request->payment_reference,
                    $request->payment_date ? Carbon::parse($request->payment_date) : now()
                );

                // Check if this is a terminated employee and mark arrears as paid
                $this->markTerminationArrearsAsPaid($record);

                // Process leave encashment - deduct leave days for paid encashments
                $this->processLeaveEncashment($record);
            }

            DB::commit();

            return redirect()->back()->with('success', count($records) . ' payroll records marked as paid.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error marking payroll as paid: ' . $e->getMessage());
        }
    }

    /**
     * Mark termination arrears as paid for terminated employees
     */
    private function markTerminationArrearsAsPaid(PayrollRecord $record)
    {
        try {
            // Check if the employee was terminated and has a termination record
            $termination = \App\Models\Termination::where('terminate_to', $record->employee_id)
                ->where('status', 2) // Approved termination
                ->where('arrears_paid', 0) // Not yet marked as paid
                ->first();

            if ($termination) {
                // Check if the payroll metadata indicates this was a terminated employee calculation
                $metadata = json_decode($record->metadata, true);
                if (isset($metadata['calculation_type']) && $metadata['calculation_type'] === 'terminated_prorated') {
                    $termination->update([
                        'arrears_paid' => 1,
                        'updated_at' => now()
                    ]);

                    Log::info('Termination arrears marked as paid', [
                        'termination_id' => $termination->termination_id,
                        'employee_id' => $record->employee_id,
                        'payroll_record_id' => $record->id,
                        'payment_date' => $record->payment_date
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Log the error but don't throw it to avoid breaking the payroll payment process
            Log::warning('Failed to mark termination arrears as paid for employee ' . $record->employee_id . ': ' . $e->getMessage());
        }
    }

    /**
     * Process leave encashment - deduct leave days when encashment is paid
     */
    private function processLeaveEncashment(PayrollRecord $record)
    {
        try {
            // Load payroll record details to find leave encashment earnings
            $record->load('details');

            // Find leave encashment earnings in the payroll details
            $leaveEncashmentDetails = $record->details()
                ->where(function ($query) {
                    $query->where('name', 'like', '%Leave Encashment%')
                        ->orWhere('code', 'like', '%LEAVE_ENCASH%')
                        ->orWhere('description', 'like', '%leave encashment%');
                })
                ->whereIn('type', ['earning', 'allowance'])
                ->get();

            if ($leaveEncashmentDetails->isEmpty()) {
                return;
            }

            // Get the current financial year
            $financialYear = getActiveFinancialYear();
            if (!$financialYear) {
                Log::warning('No active financial year found for leave encashment processing', [
                    'payroll_record_id' => $record->id,
                    'employee_id' => $record->employee_id
                ]);
                return;
            }

            // Get the annual leave type (default for encashment)
            $leaveType = LeaveType::where('leave_type_name', 'like', '%Annual%')
                ->orWhere('leave_type_name', 'like', '%Vacation%')
                ->first();

            if (!$leaveType) {
                // If no annual leave found, get the first active leave type
                $leaveType = LeaveType::first();
            }

            if (!$leaveType) {
                Log::warning('No leave type found for leave encashment processing', [
                    'payroll_record_id' => $record->id,
                    'employee_id' => $record->employee_id
                ]);
                return;
            }

            foreach ($leaveEncashmentDetails as $detail) {
                // Get the number of days from metadata or units field
                $metadata = $detail->metadata ?? [];
                $days = $metadata['days'] ?? $metadata['leave_days'] ?? $detail->units ?? 0;

                if ($days <= 0) {
                    continue;
                }

                // Create leave adjustment to deduct the encashed days
                $adjustment = LeaveAdjustment::create([
                    'employee_id' => $record->employee_id,
                    'leave_type_id' => $leaveType->leave_type_id,
                    'financial_year_id' => $financialYear->id,
                    'adjustment_type' => 'deduct',
                    'adjustment_days' => $days,
                    'reason' => "Leave encashment for {$days} days - Payroll Period: " . ($record->payrollPeriod->name ?? 'Unknown'),
                    'created_by' => auth()->id(),
                    'adjusted_by' => auth()->id(),
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'adjustment_date' => now(),
                ]);

                Log::info('Leave encashment processed - leave days deducted', [
                    'payroll_record_id' => $record->id,
                    'employee_id' => $record->employee_id,
                    'leave_adjustment_id' => $adjustment->id,
                    'days_deducted' => $days,
                    'leave_type' => $leaveType->leave_type_name,
                    'financial_year' => $financialYear->name
                ]);
            }
        } catch (\Exception $e) {
            // Log the error but don't throw it to avoid breaking the payroll payment process
            Log::error('Failed to process leave encashment for employee ' . $record->employee_id . ': ' . $e->getMessage(), [
                'payroll_record_id' => $record->id,
                'error' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Show payroll record details
     */
    public function show(PayrollRecord $payrollRecord)
    {
        $payrollRecord->load([
            'employeePayroll.employee',
            'payrollPeriod',
            'details'
        ]);

        // Decode metadata for the view


        return view('admin.payroll.show', compact('payrollRecord'));
    }

    /**
     * Generate payslip
     */
    public function generatePayslip(PayrollRecord $payrollRecord)
    {
        $payrollRecord->load([
            'employeePayroll.employee',
            'payrollPeriod',
            'details'
        ]);

        return view('admin.payroll.payslip', compact('payrollRecord'));
    }

    /**
     * Export payroll data
     */
    public function export(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:payroll_periods,id',
            'format' => 'required|in:excel,pdf,csv'
        ]);

        $period = PayrollPeriod::findOrFail($request->period_id);
        $records = PayrollRecord::with(['employeePayroll.employee'])
            ->where('payroll_period_id', $period->id)
            ->get();

        // Implementation for different export formats would go here
        // For now, return a simple response
        return response()->json([
            'message' => 'Export functionality will be implemented',
            'period' => $period->name,
            'format' => $request->format,
            'records_count' => $records->count()
        ]);
    }

    /**
     * Calculate period statistics including working days, holidays, and weekends
     */
    private function calculatePeriodStatistics($period)
    {
        $startDate = Carbon::parse($period->start_date);
        $endDate = Carbon::parse($period->end_date);

        $totalDays = $startDate->diffInDays($endDate) + 1;
        $workingDays = 0;
        $weekends = 0;
        $holidays = 0;

        // Use the same calculation logic as in KenyanPayrollCalculationService
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            if ($current->isWeekend()) {
                $weekends++;
            } else {
                $workingDays++;
            }
            $current->addDay();
        }

        // Note: Holiday calculation would require a holidays table or configuration
        // For now, we'll set it to 0 and can be enhanced later
        $holidays = 0;

        return [
            'total_days' => $totalDays,
            'working_days' => $workingDays,
            'weekends' => $weekends,
            'holidays' => $holidays,
            'period_month' => $startDate->format('F Y')
        ];
    }

    /**
     * Calculate payroll status statistics for the current period
     */
    private function calculatePayrollStatusStatistics($period)
    {
        $stats = PayrollRecord::where('payroll_period_id', $period->id)
            ->selectRaw('
                COUNT(*) as total_records,
                SUM(CASE WHEN payroll_record_status = ? THEN 1 ELSE 0 END) as calculated,
                SUM(CASE WHEN payroll_record_status = ? THEN 1 ELSE 0 END) as pending_approval,
                SUM(CASE WHEN payroll_record_status = ? THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN payroll_record_status = ? THEN 1 ELSE 0 END) as paid
            ', [PayrollStatus::CALCULATED, PayrollStatus::DRAFT, PayrollStatus::APPROVED, PayrollStatus::PAID])
            ->first();

        return [
            'total_records' => $stats->total_records ?? 0,
            'calculated' => $stats->calculated ?? 0,
            'pending_approval' => $stats->pending_approval ?? 0,
            'approved' => $stats->approved ?? 0,
            'paid' => $stats->paid ?? 0
        ];
    }

    /**
     * Get monthly trends for dashboard charts
     */
    private function getMonthlyTrends()
    {
        $trends = [];
        $months = [];
        $grossSalaries = [];
        $netSalaries = [];
        $deductions = [];

        // Get last 12 months data
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M Y');
            $months[] = $monthName;

            $monthlyData = PayrollRecord::whereHas('payrollPeriod', function ($q) use ($date) {
                $q->whereYear('start_date', $date->year)
                    ->whereMonth('start_date', $date->month);
            })->selectRaw('
                SUM(gross_salary) as total_gross,
                SUM(net_salary) as total_net,
                SUM(total_deductions) as total_deductions
            ')->first();

            $grossSalaries[] = $monthlyData->total_gross ?? 0;
            $netSalaries[] = $monthlyData->total_net ?? 0;
            $deductions[] = $monthlyData->total_deductions ?? 0;
        }

        return [
            'months' => $months,
            'gross_salaries' => $grossSalaries,
            'net_salaries' => $netSalaries,
            'deductions' => $deductions
        ];
    }

    /**
     * Get chart data for reports dashboard
     */
    private function getReportsChartData($periodId)
    {
        // Get statutory contributions data
        $period = null;

        if ($periodId) {
            $period = PayrollPeriod::where('id', $periodId)->first();
        }

        if (!$period) {
            $period = PayrollPeriod::where('is_current', 1)
                ->first();
        }

        $data = [
            'statutory_breakdown' => [],
            'monthly_trends' => [],
            'department_breakdown' => [],
            'report_summary' => []
        ];

        if ($period) {
            // Get payroll records for the period
            $payrollRecords = PayrollRecord::with(['employee', 'payrollPeriod'])
                ->where('payroll_period_id', $period->id)
                ->get();

            // Statutory breakdown (pie chart data)
            $data['statutory_breakdown'] = [
                'labels' => ['PAYE Tax', 'NSSF', 'SHIF', 'Housing Levy', 'Pension'],
                'data' => [
                    $payrollRecords->sum('paye_tax'),
                    $payrollRecords->sum('nssf_contribution'),
                    $payrollRecords->sum('shif_contribution'),
                    $payrollRecords->sum('housing_levy'),
                    $payrollRecords->sum('pension_contribution')
                ],
                'colors' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
            ];

            // Monthly trends (last 6 months)
            $monthlyTrends = $this->getMonthlyTrends();
            $data['monthly_trends'] = $monthlyTrends;

            // Department breakdown (bubble chart data)
            $departmentBreakdown = $this->getDepartmentBreakdown($payrollRecords);
            $data['department_breakdown'] = $departmentBreakdown;

            // Report summary statistics
            $data['report_summary'] = [
                'total_employees' => $payrollRecords->count(),
                'total_gross' => $payrollRecords->sum('gross_salary'),
                'total_net' => $payrollRecords->sum('net_salary'),
                'total_deductions' => $payrollRecords->sum('total_deductions'),
                'total_paye' => $payrollRecords->sum('paye_tax'),
                'total_nssf' => $payrollRecords->sum('nssf_contribution'),
                'total_shif' => $payrollRecords->sum('shif_contribution'),
                'total_housing_levy' => $payrollRecords->sum('housing_levy'),
                'period_name' => $period->name
            ];
        }

        return $data;
    }

    /**
     * Get department breakdown for bubble chart
     */
    private function getDepartmentBreakdown($payrollRecords)
    {
        $departmentData = [];

        // Group by department
        $grouped = $payrollRecords->groupBy(function ($record) {
            return $record->employee->department->department_name ?? 'Unknown';
        });

        $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#FF6384', '#36A2EB'];
        $colorIndex = 0;

        foreach ($grouped as $department => $records) {
            $totalGross = $records->sum('gross_salary');
            $totalNet = $records->sum('net_salary');
            $employeeCount = $records->count();

            $departmentData[] = [
                'label' => $department,
                'data' => [[
                    'x' => $totalGross / 1000, // Convert to thousands for better display
                    'y' => $totalNet / 1000,   // Convert to thousands for better display
                    'r' => max(5, min(25, $employeeCount * 2)) // Bubble size based on employee count
                ]],
                'backgroundColor' => $colors[$colorIndex % count($colors)],
                'borderColor' => $colors[$colorIndex % count($colors)],
                'employee_count' => $employeeCount,
                'total_gross' => $totalGross,
                'total_net' => $totalNet
            ];

            $colorIndex++;
        }

        return $departmentData;
    }

    /**
     * Get reports data via AJAX
     */
    public function getChartsData(Request $request)
    {
        $periodID = $request->get('month');

        $chartData = $this->getReportsChartData($periodID);

        return response()->json($chartData);
    }

    /**
     * API endpoint for payroll statistics
     */
    public function apiStats()
    {
        $currentPeriod = PayrollPeriod::getCurrentPeriod();
        $stats = [];

        if ($currentPeriod) {
            $stats = $currentPeriod->getSummary();
        }

        return response()->json($stats);
    }

    /**
     * API endpoint for employee payroll calculation
     */
    public function apiCalculateEmployee(Request $request)
    {
        $request->validate([
            'employee_payroll_id' => 'required|exists:employee_payrolls,id',
            'period_id' => 'required|exists:payroll_periods,id'
        ]);

        try {
            $employeePayroll = EmployeePayroll::findOrFail($request->employee_payroll_id);
            $period = PayrollPeriod::findOrFail($request->period_id);

            $payrollService = $this->payrollCalculationResolver->resolveForEmployeePayroll($employeePayroll);
            $payrollRecord = $payrollService->calculateEmployeePayroll($employeePayroll, $period);

            return response()->json([
                'success' => true,
                'payroll_record' => $payrollRecord->load('details')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send payslip via email to a single employee
     */
    public function emailPayslip(Request $request, PayrollRecord $payrollRecord)
    {
        $request->validate([
            'custom_message' => 'nullable|string|max:1000'
        ]);

        try {
            $employee = $payrollRecord->employeePayroll->employee;
            $recipientEmail = $employee->email;

            if (!$recipientEmail) {
                return redirect()->back()->with('error', 'Employee does not have a primary email address.');
            }

            // Load necessary relationships
            $payrollRecord->load([
                'employeePayroll.employee',
                'payrollPeriod',
                'details'
            ]);

            // Prepare email recipients
            $mail = Mail::to($recipientEmail);
            if ($employee->personal_email && $employee->personal_email !== $recipientEmail) {
                $mail->cc($employee->personal_email);
            }

            // Send email
            $mail->send(new SendPayslipEmail($payrollRecord, $request->custom_message));

            $recipientList = $recipientEmail;
            if ($employee->personal_email && $employee->personal_email !== $recipientEmail) {
                $recipientList .= " and {$employee->personal_email}";
            }

            return redirect()->back()->with('success', "Payslip sent successfully to {$employee->fullName()} at {$recipientList}");
        } catch (\Exception $e) {
            Log::error('Error sending payslip email: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error sending payslip email: ' . $e->getMessage());
        }
    }

    /**
     * Send payslips via email to multiple employees (mass email)
     */
    public function emailPayslipsMass(Request $request)
    {
        $request->validate([
            'record_ids' => 'required|array',
            'record_ids.*' => 'exists:payroll_records,id',
            'custom_message' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $records = PayrollRecord::whereIn('id', $request->record_ids)
                ->whereIn('status', [PayrollRecord::STATUS_APPROVED, PayrollRecord::STATUS_PAID])
                ->with([
                    'employeePayroll.employee',
                    'payrollPeriod',
                    'details'
                ])
                ->get();

            $successCount = 0;
            $failureCount = 0;
            $failedEmployees = [];

            foreach ($records as $record) {
                try {
                    $employee = $record->employeePayroll->employee;
                    $recipientEmail = $employee->email;

                    if (!$recipientEmail) {
                        $failureCount++;
                        $failedEmployees[] = "{$employee->fullName()} (no primary email)";
                        continue;
                    }

                    // Prepare email recipients
                    $mail = Mail::to($recipientEmail);
                    if ($employee->personal_email && $employee->personal_email !== $recipientEmail) {
                        $mail->cc($employee->personal_email);
                    }

                    // Send email
                    $mail->send(new SendPayslipEmail($record, $request->custom_message));
                    $successCount++;
                } catch (\Exception $e) {
                    $failureCount++;
                    $failedEmployees[] = "{$employee->fullName()} (error: {$e->getMessage()})";
                    Log::error('Error sending payslip to ' . $employee->fullName() . ': ' . $e->getMessage());
                }
            }

            DB::commit();

            $message = "Mass email completed. {$successCount} payslips sent successfully";
            if ($failureCount > 0) {
                $message .= ", {$failureCount} failed";
                if (count($failedEmployees) <= 5) {
                    $message .= ": " . implode(', ', $failedEmployees);
                } else {
                    $message .= ". Check logs for details.";
                }
            }

            return redirect()->back()->with($failureCount > 0 ? 'warning' : 'success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in mass email: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error sending mass emails: ' . $e->getMessage());
        }
    }



    public function bulkSubmitWithProgress(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            $batchId = $request->input('batch_id');

            // Initialize progress
            ProgressHelper::initializeProgress($batchId, count($ids));

            // Process submissions in background (you can use queues here)
            dispatch(new ProcessBulkPayrollSubmission($ids, $batchId));

            return response()->json([
                'success' => true,
                'message' => 'Submission started',
                'batch_id' => $batchId
            ]);
        } catch (\Exception $e) {
            \Log::error('Bulk submission error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to start submission'
            ], 500);
        }
    }

    public function checkProgress(Request $request)
    {
        $batchId = $request->input('batch_id');
        $progress = ProgressHelper::getProgress($batchId);

        if ($progress) {
            return response()->json([
                'success' => true,
                'progress' => $progress
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Progress not found'
        ], 404);
    }

    /**
     * Get payroll processing progress for AJAX
     */
    public function getPayrollProgress(Request $request)
    {
        $batchId = $request->input('batch_id');
        $progress = ProgressHelper::getProgress($batchId);

        if ($progress) {
            return response()->json([
                'success' => true,
                'progress' => $progress,
                'is_completed' => $progress['status'] === 'completed',
                'is_failed' => $progress['status'] === 'failed'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Progress not found'
        ], 404);
    }

    private function getPayrollCompanies()
    {
        $companies = CompanyContext::switchableCompanies();

        if ($companies->isNotEmpty()) {
            return $companies;
        }

        $permittedIds = CompanyContext::permittedCompanyIds();
        if (!empty($permittedIds)) {
            return Company::whereIn('id', $permittedIds)->where('status', 'active')->orderBy('name')->get();
        }

        return collect();
    }

    /**
     * @return array|null|false  null = all allowed companies, array = specific IDs, false = invalid selection
     */
    private function resolvePayrollCompanyIds(Request $request)
    {
        if ($request->boolean('process_all_companies')) {
            return CompanyContext::isSuperAdmin() ? null : CompanyContext::permittedCompanyIds();
        }

        $allowedCompanyIds = $this->getPayrollCompanies()->pluck('id')->all();
        $selectedCompanyIds = array_values(array_unique(array_map('intval', (array) $request->input('company_ids', []))));
        $companyIds = array_values(array_intersect($selectedCompanyIds, $allowedCompanyIds));

        if (empty($companyIds)) {
            return false;
        }

        return $companyIds;
    }

    private function employeesReadyForPayrollQuery(?array $companyIds = null)
    {
        $query = Employee::where('status', GeneralStatus::ACTIVE)
            ->whereHas('employeePayroll', function ($query) {
                $query->where('status', GeneralStatus::ACTIVE);
            });

        if ($companyIds !== null) {
            $query->whereIn('company_id', $companyIds);
        }

        return $query;
    }
}
