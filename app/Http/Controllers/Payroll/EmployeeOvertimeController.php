<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeOvertime;
use App\Models\Payroll\PayrollPeriod;
use App\Exports\EmployeeOvertimeTemplateExport;
use App\Imports\EmployeeOvertimeImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeOvertimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EmployeeOvertime::with(['employee', 'creator']);
        
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        
        if ($request->filled('month_year')) {
            $query->where('month_year', $request->month_year);
        }
        
        $overtimes = $query->orderBy('created_at', 'desc')->paginate(50);
        $employees = Employee::where('status', 1)->get(['employee_id', 'first_name', 'last_name', 'payroll_number']);
        
        return view('admin.payroll.overtime.index', compact('overtimes', 'employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::whereHas('employeePayroll')->where('status', 1)->get();
        $payrollPeriods = PayrollPeriod::where('status', 1)->get();
        return view('admin.payroll.overtime.form', compact('employees', 'payrollPeriods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         
        $request->validate([
            'employee_id' => 'required|exists:employee,employee_id',
            'month_year' => 'required|date_format:Y-m',
            'weekend_hours_totals' => 'required|numeric|min:0',
            'weekend_days_totals' => 'required|numeric|min:0',
            'public_holiday_hours_totals' => 'required|numeric|min:0',
            'public_holiday_days_totals' => 'required|numeric|min:0',
            'weekday_hours_total' => 'required|numeric|min:0',
            'weekday_days_total' => 'required|numeric|min:0',
            'payroll_period_id' => 'required',
        ]);

      
        try {
            DB::beginTransaction();

            // Check if overtime record already exists for this employee and month
            $existingOvertime = EmployeeOvertime::where('employee_id', $request->employee_id)
                ->where('month_year', $request->month_year)
                ->first();

            if ($existingOvertime) {
                return redirect()->back()->with('error', 'Overtime record already exists for this employee and month.');
            }

            // Validate that the overtime rate matches the employee's profile setting
            $employee = Employee::with('payGrade')->findOrFail($request->employee_id);
            $profileOvertimeRate = $employee->payGrade ? $employee->payGrade->overtime_rate : 0;
            
            // Only validate if profile rate exists and doesn't match input (allowing for manual override)
            if ($profileOvertimeRate > 0 && $request->overtime_rate != $profileOvertimeRate) {
                // Add warning but don't block (business decision to allow override)
                // This could be implemented as a flash message in a real application
            }

            // Calculate total amount
            $totalAmount = $request->hours_worked * $request->overtime_rate;

            $overtime = new EmployeeOvertime();
            $overtime->employee_id = $request->employee_id;
            $overtime->month_year = $request->month_year;
            $overtime->hours_worked = $request->hours_worked;
            $overtime->overtime_rate = $request->overtime_rate;
            $overtime->total_amount = $totalAmount;
            $overtime->weekend_hours_totals = $request->weekend_hours_totals;
            $overtime->weekend_days_totals = $request->weekend_days_totals;
            $overtime->public_holiday_hours_totals = $request->public_holiday_hours_totals;
            $overtime->public_holiday_days_totals = $request->public_holiday_days_totals;
            $overtime->weekday_hours_total = $request->weekday_hours_total;
            $overtime->weekday_days_total = $request->weekday_days_total;
            $overtime->payroll_period_id = $request->payroll_period_id;
            $overtime->created_by = Auth::user()->id;
            $overtime->updated_by = Auth::user()->id;
            $overtime->save();

            DB::commit();

            return redirect()->route('payroll.overtime.index')->with('success', 'Overtime record created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'An error occurred while creating the overtime record: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $overtime = EmployeeOvertime::with(['employee', 'creator', 'updater'])->findOrFail($id);
        return view('admin.payroll.overtime.show', compact('overtime'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $overtime = EmployeeOvertime::findOrFail($id);
        $employees = Employee::where('status', 1)->get();
         $payrollPeriods = PayrollPeriod::where('status', 1)->get();

        return view('admin.payroll.overtime.form', compact( 'employees', 'payrollPeriods') ,[ 'overtime' => $overtime ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:employee,employee_id',
            'month_year' => 'required|date_format:Y-m',

        ]);

        try {
            DB::beginTransaction();

            $overtime = EmployeeOvertime::findOrFail($id);

            // Check if another overtime record already exists for this employee and month
            $existingOvertime = EmployeeOvertime::where('employee_id', $request->employee_id)
                ->where('month_year', $request->month_year)
                ->where('id', '!=', $id)
                ->first();

            if ($existingOvertime) {
                return redirect()->back()->with('error', 'Overtime record already exists for this employee and month.');
            }

            // Validate that the overtime rate matches the employee's profile setting
            $employee = Employee::with('payGrade')->findOrFail($request->employee_id);
            $profileOvertimeRate = $employee->payGrade ? $employee->payGrade->overtime_rate : 0;
            
            // Only validate if profile rate exists and doesn't match input (allowing for manual override)
            if ($profileOvertimeRate > 0 && $request->overtime_rate != $profileOvertimeRate) {
                // Add warning but don't block (business decision to allow override)
                // This could be implemented as a flash message in a real application
            }

              $overtime->employee_id = $request->employee_id;
            $overtime->month_year = $request->month_year;
            $overtime->hours_worked = $request->hours_worked;
            $overtime->overtime_rate = $request->overtime_rate;
           
            $overtime->weekend_hours_totals = $request->weekend_hours_totals;
            $overtime->weekend_days_totals = $request->weekend_days_totals;
            $overtime->public_holiday_hours_totals = $request->public_holiday_hours_totals;
            $overtime->public_holiday_days_totals = $request->public_holiday_days_totals;
            $overtime->weekday_hours_total = $request->weekday_hours_total;
            $overtime->weekday_days_total = $request->weekday_days_total;
            $overtime->payroll_period_id = $request->payroll_period_id;
            $overtime->updated_by = Auth::user()->id;
            $overtime->save();

            DB::commit();

            return redirect()->route('payroll.overtime.index')->with('success', 'Overtime record updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'An error occurred while updating the overtime record: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
       
        try {
            $overtime = EmployeeOvertime::findOrFail($id);
            $overtime->delete();

            return redirect()->route('payroll.overtime.index')->with('success', 'Overtime record deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while deleting the overtime record: ' . $e->getMessage());
        }
    }

    /**
     * Get employee overtime rate from payroll profile
     */
    public function getEmployeeOvertimeRate(Request $request)
    {
        $employee = Employee::with('payGrade')->findOrFail($request->employee_id);
        
        $overtimeRate = 0;
        if ($employee->payGrade) {
            $overtimeRate = $employee->payGrade->overtime_rate ?? 0;
        }
        
        return response()->json(['overtime_rate' => $overtimeRate]);
    }

    /**
     * Download overtime template CSV with employee data
     */
    public function downloadTemplate(Request $request)
    {
        $month_year = $request->get('month_year', date('Y-m'));
        
        $fileName = 'overtime_template_' . $month_year . '.xlsx';
        
        return Excel::download(new EmployeeOvertimeTemplateExport($month_year), $fileName);
    }

    /**
     * Import overtime data from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'overtime_file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

      
        try {
            DB::beginTransaction();

            Excel::import(new EmployeeOvertimeImport, $request->file('overtime_file'));

            DB::commit();

            return redirect()->route('payroll.overtime.index')
                ->with('success', 'Overtime records imported successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollback();
            
            $failures = $e->failures();
            $errorMessages = [];
            
            foreach ($failures as $failure) {
                $errorMessages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
                \Log::info("Row {$failure->row()} failed validation: " . implode(', ', $failure->errors()));
            }
            
            return redirect()->back()
                ->with('error', 'Import failed: ' . implode('; ', $errorMessages));
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Show import form
     */
    public function showImportForm()
    {
       
        return view('admin.payroll.overtime.import');
    }
 
}