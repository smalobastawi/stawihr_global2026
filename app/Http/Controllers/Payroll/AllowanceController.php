<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Payroll\EmployeePayroll;
use App\Models\Payroll\EmployeeAllowance;
use App\Models\Payroll\AllowanceType;
use Illuminate\Http\Request;

class AllowanceController extends Controller
{
    /**
     * Display allowances for an employee
     */
    public function index(EmployeePayroll $employeePayroll)
    {
        $allowances = $employeePayroll->allowances()->with('allowanceType')->get();
        
        return view('admin.payroll.employees.allowances.index', compact('employeePayroll', 'allowances'));
    }

    /**
     * Show the form for creating a new allowance
     */
    public function create(EmployeePayroll $employeePayroll)
    {
        $allowanceTypes = AllowanceType::active()->orderBy('name')->get();
        
        return view('admin.payroll.employees.allowances.create', compact('employeePayroll', 'allowanceTypes'));
    }

    /**
     * Store a newly created allowance
     */
    public function store(Request $request, EmployeePayroll $employeePayroll)
    {
        $request->validate([
            'allowance_type_id' => 'required|exists:allowance_types,id',
            'calculation_type' => 'required|in:fixed,percentage,formula',
            'amount' => 'required_if:calculation_type,fixed|nullable|numeric|min:0',
            'percentage' => 'required_if:calculation_type,percentage|nullable|numeric|min:0|max:100',
            'is_taxable' => 'boolean',
            'is_pensionable' => 'boolean',
            'effective_date' => 'required|date',
            'end_date' => 'nullable|date|after:effective_date',
        ]);

        $allowanceType = AllowanceType::find($request->allowance_type_id);

        $employeePayroll->allowances()->create([
            'allowance_type_id' => $request->allowance_type_id,
            'name' => $allowanceType->name,
            'calculation_type' => $request->calculation_type,
            'amount' => $request->calculation_type === 'fixed' ? $request->amount : 0,
            'percentage' => $request->calculation_type === 'percentage' ? $request->percentage : 0,
            'is_taxable' => $request->boolean('is_taxable', $allowanceType->is_taxable),
            'is_pensionable' => $request->boolean('is_pensionable', $allowanceType->is_pensionable),
            'is_active' => true,
            'effective_date' => $request->effective_date,
            'end_date' => $request->end_date,
            'created_by' => auth()->id()
        ]);

        return redirect()->route('payroll.employees.allowances.index', $employeePayroll)
                        ->with('success', 'Allowance added successfully.');
    }

    /**
     * Show the form for editing an allowance
     */
    public function edit(EmployeePayroll $employeePayroll, EmployeeAllowance $allowance)
    {
        $allowanceTypes = AllowanceType::active()->orderBy('name')->get();
        
        return view('admin.payroll.employees.allowances.edit', compact('employeePayroll', 'allowance', 'allowanceTypes'));
    }

    /**
     * Update the specified allowance
     */
    public function update(Request $request, EmployeePayroll $employeePayroll, EmployeeAllowance $allowance)
    {
        $request->validate([
            'calculation_type' => 'required|in:fixed,percentage,formula',
            'amount' => 'required_if:calculation_type,fixed|nullable|numeric|min:0',
            'percentage' => 'required_if:calculation_type,percentage|nullable|numeric|min:0|max:100',
            'is_taxable' => 'boolean',
            'is_pensionable' => 'boolean',
            'is_active' => 'boolean',
            'end_date' => 'nullable|date|after:effective_date',
        ]);

        $allowance->update([
            'calculation_type' => $request->calculation_type,
            'amount' => $request->calculation_type === 'fixed' ? $request->amount : 0,
            'percentage' => $request->calculation_type === 'percentage' ? $request->percentage : 0,
            'is_taxable' => $request->boolean('is_taxable'),
            'is_pensionable' => $request->boolean('is_pensionable'),
            'is_active' => $request->boolean('is_active', true),
            'end_date' => $request->end_date,
            'updated_by' => auth()->id()
        ]);

        return redirect()->route('payroll.employees.allowances.index', $employeePayroll)
                        ->with('success', 'Allowance updated successfully.');
    }

    /**
     * Remove the specified allowance
     */
    public function destroy(EmployeePayroll $employeePayroll, EmployeeAllowance $allowance)
    {
        $allowance->delete();

        return redirect()->route('payroll.employees.allowances.index', $employeePayroll)
                        ->with('success', 'Allowance deleted successfully.');
    }

    /**
     * API endpoint to get employee allowances
     */
    public function apiGetAllowances(EmployeePayroll $employeePayroll)
    {
        $allowances = $employeePayroll->allowances()
            ->with('allowanceType')
            ->active()
            ->get();

        return response()->json($allowances);
    }
}
