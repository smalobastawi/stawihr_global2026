<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
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
     * Get recent payslips for the authenticated user (last 6 months)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecentPayslips(Request $request)
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

            // Get payroll records for the last 6 months
            $sixMonthsAgo = now()->subMonths(6);
            $payrollRecords = PayrollRecord::where('employee_id', $employee->employee_id)
                ->where('payroll_record_status', PayrollStatus::PAID)
                ->whereHas('payrollPeriod', function ($query) use ($sixMonthsAgo) {
                    $query->where('start_date', '>=', $sixMonthsAgo->startOfMonth());
                })
                ->with(['payrollPeriod'])
                ->orderBy('created_at', 'DESC')
                ->get();

            if ($payrollRecords->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'data' => [],
                    'message' => 'No payslips found for the last 6 months'
                ], 200);
            }

            // Format the data for the mobile app
            $payslips = $payrollRecords->map(function ($record) {
                $period = $record->payrollPeriod;
                $periodDate = $period ? Carbon::parse($period->start_date) : null;

                return [
                    'id' => $record->id,
                    'month' => $periodDate ? (int)$periodDate->format('m') : null,
                    'year' => $periodDate ? (int)$periodDate->format('Y') : null,
                    'month_name' => $periodDate ? $periodDate->format('F') : null,
                    'basic_salary' => $record->basic_salary ?? 0,
                    'total_allowance' => $record->total_allowances ?? 0,
                    'total_deduction' => $record->total_deductions ?? 0,
                    'tax' => $record->paye_tax ?? 0,
                    'nssf_amount' => $record->nssf_contribution ?? 0,
                    'net_salary' => $record->net_salary ?? 0,
                    'gross_salary' => $record->gross_salary ?? 0,
                    'shif_contribution' => $record->shif_contribution ?? 0,
                    'housing_levy' => $record->housing_levy ?? 0,
                ];
            })->toArray();

            return response()->json([
                'status' => 'success',
                'data' => $payslips
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
     * Get payslip URL for a specific payroll record
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPayslipUrl(Request $request, $id)
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

            // Get the specific payroll record
            $payrollRecord = PayrollRecord::where('id', $id)
                ->where('employee_id', $employee->employee_id)
                ->with(['payrollPeriod'])
                ->first();

            if (!$payrollRecord) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payslip not found'
                ], 404);
            }

            // Generate the payslip URL (using the web app route)
            $payslipUrl = route('ess.payroll.payslip.generate', $id);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $payrollRecord->id,
                    'url' => $payslipUrl,
                    'period_name' => $payrollRecord->payrollPeriod->period_name ?? null,
                    'period_month' => $payrollRecord->payrollPeriod ?
                        Carbon::parse($payrollRecord->payrollPeriod->start_date)->format('F Y') : null
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
}