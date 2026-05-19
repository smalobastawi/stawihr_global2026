<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Payroll\EmployeeSalaryHistory;
use App\Models\Payroll\EmployeePayroll;
use App\Services\Payroll\PayrollChangeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalaryHistoryExport;

class SalaryHistoryController extends Controller
{
    protected $payrollChangeService;

    public function __construct(PayrollChangeService $payrollChangeService)
    {
        $this->payrollChangeService = $payrollChangeService;
    }

    /**
     * Display salary history index page
     */
    public function index(Request $request)
    {
        $query = EmployeeSalaryHistory::with(['employee.department', 'employee.designation', 'changedBy'])
            ->orderBy('effective_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('change_type')) {
            $query->where('change_type', $request->change_type);
        }

        if ($request->filled('date_from')) {
            $query->where('effective_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('effective_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        $salaryHistory = $query->paginate(100);
        $employees = Employee::active()->get();
        $changeTypes = $this->getChangeTypes();

        // Statistics
        $stats = [
            'total_changes' => EmployeeSalaryHistory::count(),
            'changes_this_year' => EmployeeSalaryHistory::whereYear('effective_date', date('Y'))->count(),
            'changes_this_month' => EmployeeSalaryHistory::whereYear('effective_date', date('Y'))
                ->whereMonth('effective_date', date('m'))->count(),
            'average_increase' => EmployeeSalaryHistory::where('salary_change_amount', '>', 0)
                ->avg('salary_change_amount'),
        ];


        return view('admin.payroll.salary-history.index', compact(
            'salaryHistory',
            'employees',
            'changeTypes',
            'stats'
        ));
    }

    /**
     * Show salary history for a specific employee
     */
    public function showEmployee($employeeId, Request $request)
    {
        $employee = Employee::with(['department', 'designation'])->findOrFail($employeeId);

        $salaryHistory = EmployeeSalaryHistory::where('employee_id', $employeeId)
            ->with('changedBy')
            ->orderBy('effective_date', 'desc')
            ->paginate(20);

        $currentSalary = EmployeePayroll::where('employee_id', $employeeId)
            ->value('basic_salary') ?? 0;

        // Calculate salary progression
        $progression = $this->calculateSalaryProgression($employeeId);

        return view('admin.payroll.salary-history.employee', compact(
            'employee',
            'salaryHistory',
            'currentSalary',
            'progression'
        ));
    }

    /**
     * Export salary history
     */
    public function export(Request $request)
    {
        $fileName = 'salary-history-' . date('Y-m-d') . '.xlsx';

        return Excel::download(new SalaryHistoryExport($request), $fileName);
    }

    /**
     * Get available change types
     */
    private function getChangeTypes()
    {
        return [
            'promotion' => 'Promotion',
            'increment' => 'Annual Increment',
            'adjustment' => 'Salary Adjustment',
            'demotion' => 'Demotion',
            'correction' => 'Correction',
            'other' => 'Other'
        ];
    }

    /**
     * Calculate salary progression for an employee
     */
    private function calculateSalaryProgression($employeeId)
    {
        $history = EmployeeSalaryHistory::where('employee_id', $employeeId)
            ->orderBy('effective_date', 'asc')
            ->get();

        if ($history->isEmpty()) {
            return null;
        }

        $firstSalary = $history->first()->previous_salary;
        $currentSalary = EmployeePayroll::where('employee_id', $employeeId)
            ->value('basic_salary') ?? $history->last()->new_salary;

        $totalIncrease = $currentSalary - $firstSalary;
        $percentageIncrease = $firstSalary > 0 ? ($totalIncrease / $firstSalary) * 100 : 0;

        return [
            'first_salary' => $firstSalary,
            'current_salary' => $currentSalary,
            'total_increase' => $totalIncrease,
            'percentage_increase' => $percentageIncrease,
            'total_changes' => $history->count(),
            'average_change' => $history->avg('salary_change_amount'),
            'positive_changes' => $history->where('salary_change_amount', '>', 0)->count(),
            'negative_changes' => $history->where('salary_change_amount', '<', 0)->count(),
        ];
    }
}
