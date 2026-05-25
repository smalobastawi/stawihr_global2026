<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\FinancialYear;
use App\Models\Payroll\PayrollRecord;
use App\Lib\Enumerations\PayrollStatus;
use Carbon\Carbon;

class PayrollController extends Controller
{
    protected $currentMonth;

    public function __construct()
    {
        $this->currentMonth = now()->format('Y-m');
    }

    /**
     * Get salary details for the authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthUserSalaryDetails(Request $request)
    {
        try {
            $user = $request->user();
            $employee = Employee::where('user_id', $user->id)->first();

            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee not found'
                ], 404);
            }

            // Get the latest payroll record for this employee
            $payrollRecord = PayrollRecord::with(['employeePayroll', 'payrollPeriod'])
                ->where('employee_id', $employee->employee_id)
                ->where('payroll_record_status', PayrollStatus::PAID)
                ->orderBy('created_at', 'DESC')
                ->first();

            if (!$payrollRecord) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Salary details not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $payrollRecord->id,
                    'employee_id' => $payrollRecord->employee_id,
                    'basic_salary' => $payrollRecord->basic_salary,
                    'total_allowances' => $payrollRecord->total_allowances,
                    'gross_salary' => $payrollRecord->gross_salary,
                    'total_deductions' => $payrollRecord->total_deductions,
                    'paye_tax' => $payrollRecord->paye_tax,
                    'nssf_contribution' => $payrollRecord->nssf_contribution,
                    'shif_contribution' => $payrollRecord->shif_contribution,
                    'housing_levy' => $payrollRecord->housing_levy,
                    'net_salary' => $payrollRecord->net_salary,
                    'payment_date' => $payrollRecord->payment_date,
                    'payment_reference' => $payrollRecord->payment_reference,
                    'period_name' => $payrollRecord->payrollPeriod->period_name ?? null,
                    'period_month' => $payrollRecord->payrollPeriod ?
                        Carbon::parse($payrollRecord->payrollPeriod->start_date)->format('F Y') : null
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate yearly salary for the authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateAuthUserYearlySalary(Request $request)
    {
        try {
            $user = $request->user();
            $employee = Employee::where('user_id', $user->id)->first();

            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee not found'
                ], 404);
            }

            $year = $request->query('year', now()->year);

            // Fetch payroll records for the authenticated employee and specified year
            $payrollRecords = PayrollRecord::where('employee_id', $employee->employee_id)
                ->where('payroll_record_status', PayrollStatus::PAID)
                ->whereHas('payrollPeriod', function ($query) use ($year) {
                    $query->whereYear('start_date', $year);
                })
                ->get();

            if ($payrollRecords->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No salary details found for this year.',
                    'year' => $year
                ], 404);
            }

            $totals = [
                'total_basic_salary' => $payrollRecords->sum('basic_salary'),
                'total_allowances' => $payrollRecords->sum('total_allowances'),
                'total_deductions' => $payrollRecords->sum('total_deductions'),
                'total_paye' => $payrollRecords->sum('paye_tax'),
                'total_nssf' => $payrollRecords->sum('nssf_contribution'),
                'total_shif' => $payrollRecords->sum('shif_contribution'),
                'total_housing_levy' => $payrollRecords->sum('housing_levy'),
                'total_net_pay' => $payrollRecords->sum('net_salary'),
                'total_gross_pay' => $payrollRecords->sum('gross_salary'),
            ];

            $responseData = array_merge(
                [
                    'employee_id' => $employee->employee_id,
                    'year' => $year,
                    'number_of_months' => $payrollRecords->count(),
                ],
                $totals
            );

            return response()->json([
                'status' => 'success',
                'data' => $responseData,
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error calculating yearly salary:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get paid payslips for the authenticated user in the current financial year.
     * Mirrors ESS /ess/payroll (myPayroll).
     */
    public function getRecentPayslips(Request $request)
    {
        try {
            $employee = $this->resolveAuthenticatedEmployee($request);
            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee not found'
                ], 404);
            }

            $fiscalYear = $this->getCurrentFinancialYear();
            $payrollRecords = $this->paidPayslipsQuery($employee->employee_id, $fiscalYear)
                ->with(['payrollPeriod'])
                ->orderBy('payroll_period_id', 'DESC')
                ->get();

            $payslips = $payrollRecords
                ->map(fn ($record) => $this->formatPayslipListItem($record))
                ->values()
                ->toArray();

            $summary = [
                'net_earnings' => round((float) $payrollRecords->sum('net_salary'), 2),
                'gross_earnings' => round((float) $payrollRecords->sum('gross_salary'), 2),
                'total_deductions' => round((float) $payrollRecords->sum('total_deductions'), 2),
                'payslip_count' => $payrollRecords->count(),
            ];

            return response()->json([
                'status' => 'success',
                'message' => count($payslips) > 0
                    ? 'Payslips retrieved successfully'
                    : 'No payslips found for the current financial year',
                'data' => $payslips,
                'summary' => $summary,
                'financial_year' => [
                    'start_date' => $fiscalYear->start_date ?? null,
                    'end_date' => $fiscalYear->end_date ?? null,
                    'name' => $fiscalYear->year_name ?? $fiscalYear->name ?? null,
                ],
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error getting recent payslips:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get full payslip detail for a payroll record owned by the authenticated employee.
     */
    public function getPayslipDetail(Request $request, $id)
    {
        try {
            $employee = $this->resolveAuthenticatedEmployee($request);
            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee not found'
                ], 404);
            }

            $payrollRecord = PayrollRecord::where('id', $id)
                ->where('employee_id', $employee->employee_id)
                ->where('payroll_record_status', PayrollStatus::PAID)
                ->with(['payrollPeriod', 'details', 'employeePayroll'])
                ->first();

            if (!$payrollRecord) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payslip not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $this->formatPayslipDetail($payrollRecord),
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error getting payslip detail:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payslip view URL for a specific payroll record
     */
    public function getPayslipUrl(Request $request, $id)
    {
        try {
            $employee = $this->resolveAuthenticatedEmployee($request);
            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee not found'
                ], 404);
            }

            $payrollRecord = PayrollRecord::where('id', $id)
                ->where('employee_id', $employee->employee_id)
                ->where('payroll_record_status', PayrollStatus::PAID)
                ->with(['payrollPeriod'])
                ->first();

            if (!$payrollRecord) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payslip not found'
                ], 404);
            }

            $payslipUrl = url('/api/payroll/payslip/' . $id . '/view');

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $payrollRecord->id,
                    'url' => $payslipUrl,
                    'web_url' => route('ess.payroll.payslip.generate', $id),
                    'period_name' => $payrollRecord->payrollPeriod->name
                        ?? $payrollRecord->payrollPeriod->period_name
                        ?? null,
                    'period_month' => $payrollRecord->payrollPeriod
                        ? Carbon::parse($payrollRecord->payrollPeriod->start_date)->format('F Y')
                        : null,
                ]
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error getting payslip URL:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Render payslip HTML for mobile/WebView download (Sanctum authenticated).
     */
    public function viewPayslip(Request $request, $id)
    {
        $employee = $this->resolveAuthenticatedEmployee($request);
        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found'
            ], 404);
        }

        $payrollRecord = PayrollRecord::where('id', $id)
            ->where('employee_id', $employee->employee_id)
            ->where('payroll_record_status', PayrollStatus::PAID)
            ->firstOrFail();

        $payrollRecord->load([
            'employeePayroll.employee',
            'payrollPeriod',
            'details',
        ]);

        return view('admin.payroll.payslip', compact('payrollRecord'));
    }

    protected function resolveAuthenticatedEmployee(Request $request): ?Employee
    {
        $user = $request->user();
        if (!$user) {
            return null;
        }

        return Employee::where('user_id', $user->id)->first();
    }

    protected function getCurrentFinancialYear()
    {
        $currentDate = now();
        $fiscalYear = FinancialYear::where('start_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate)
            ->first();

        if (!$fiscalYear) {
            $fiscalYear = FinancialYear::orderByDesc('start_date')->first();
        }

        return $fiscalYear;
    }

    protected function paidPayslipsQuery(int $employeeId, ?FinancialYear $fiscalYear)
    {
        $query = PayrollRecord::where('employee_id', $employeeId)
            ->where('payroll_record_status', PayrollStatus::PAID);

        if ($fiscalYear && $fiscalYear->start_date && $fiscalYear->end_date) {
            $query->whereHas('payrollPeriod', function ($periodQuery) use ($fiscalYear) {
                $periodQuery->whereBetween('start_date', [
                    $fiscalYear->start_date,
                    $fiscalYear->end_date,
                ]);
            });
        }

        return $query;
    }

    protected function formatPayslipListItem(PayrollRecord $record): array
    {
        $period = $record->payrollPeriod;
        $periodDate = $period ? Carbon::parse($period->start_date) : null;
        $payDate = $record->payment_date
            ?? ($period?->pay_date)
            ?? ($period?->end_date);

        return [
            'id' => $record->id,
            'month' => $periodDate ? (int) $periodDate->format('m') : null,
            'year' => $periodDate ? (int) $periodDate->format('Y') : null,
            'month_name' => $periodDate ? $periodDate->format('F') : ($period->name ?? null),
            'period_name' => $period->name ?? $period->period_name ?? null,
            'pay_date' => $payDate ? Carbon::parse($payDate)->format('Y-m-d') : null,
            'basic_salary' => (float) ($record->basic_salary ?? 0),
            'total_allowance' => (float) ($record->total_allowances ?? 0),
            'total_allowances' => (float) ($record->total_allowances ?? 0),
            'total_deduction' => (float) ($record->total_deductions ?? 0),
            'total_deductions' => (float) ($record->total_deductions ?? 0),
            'tax' => (float) ($record->paye_tax ?? 0),
            'paye_tax' => (float) ($record->paye_tax ?? 0),
            'nssf_amount' => (float) ($record->nssf_contribution ?? 0),
            'nssf_contribution' => (float) ($record->nssf_contribution ?? 0),
            'shif_contribution' => (float) ($record->shif_contribution ?? 0),
            'housing_levy' => (float) ($record->housing_levy ?? 0),
            'pension_contribution' => (float) ($record->pension_contribution ?? 0),
            'net_salary' => (float) ($record->net_salary ?? 0),
            'gross_salary' => (float) ($record->gross_salary ?? 0),
            'payment_reference' => $record->payment_reference,
            'payment_method' => $record->payment_method,
            'status' => PayrollStatus::getName(PayrollStatus::PAID),
        ];
    }

    protected function formatPayslipDetail(PayrollRecord $record): array
    {
        $listItem = $this->formatPayslipListItem($record);
        $period = $record->payrollPeriod;

        $earningTypes = ['allowance', 'earning'];
        $deductionTypes = ['deduction', 'statutory_deduction'];

        $earnings = $record->details
            ->whereIn('type', $earningTypes)
            ->map(fn ($detail) => [
                'name' => $detail->name,
                'amount' => (float) $detail->amount,
                'description' => $detail->description,
            ])
            ->values()
            ->toArray();

        if ((float) ($record->basic_salary ?? 0) > 0) {
            array_unshift($earnings, [
                'name' => 'Basic Salary',
                'amount' => (float) $record->basic_salary,
                'description' => null,
            ]);
        }

        $deductions = $record->details
            ->whereIn('type', $deductionTypes)
            ->map(fn ($detail) => [
                'name' => $detail->name,
                'amount' => (float) $detail->amount,
                'description' => $detail->description,
            ])
            ->values()
            ->toArray();

        if (empty($deductions)) {
            $deductions = array_values(array_filter([
                ((float) ($record->paye_tax ?? 0) > 0) ? [
                    'name' => 'PAYE Tax',
                    'amount' => (float) $record->paye_tax,
                    'description' => null,
                ] : null,
                ((float) ($record->nssf_contribution ?? 0) > 0) ? [
                    'name' => 'NSSF Contribution',
                    'amount' => (float) $record->nssf_contribution,
                    'description' => null,
                ] : null,
                ((float) ($record->shif_contribution ?? 0) > 0) ? [
                    'name' => 'SHIF Contribution',
                    'amount' => (float) $record->shif_contribution,
                    'description' => null,
                ] : null,
                ((float) ($record->housing_levy ?? 0) > 0) ? [
                    'name' => 'Housing Levy',
                    'amount' => (float) $record->housing_levy,
                    'description' => null,
                ] : null,
                ((float) ($record->pension_contribution ?? 0) > 0) ? [
                    'name' => 'Pension Contribution',
                    'amount' => (float) $record->pension_contribution,
                    'description' => null,
                ] : null,
            ]));
        }

        return array_merge($listItem, [
            'period_start' => $period?->start_date?->format('Y-m-d'),
            'period_end' => $period?->end_date?->format('Y-m-d'),
            'earnings' => $earnings,
            'deductions' => $deductions,
            'view_url' => url('/api/payroll/payslip/' . $record->id . '/view'),
        ]);
    }
}