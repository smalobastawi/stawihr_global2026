<?php

namespace App\Http\Controllers\Payroll;


use App\Http\Controllers\Controller;
use App\Models\EmployeeDeductions;
use App\Models\Payroll\EmployeePayroll;
use App\Models\Payroll\EmployeeDeduction;
use App\Models\Payroll\DeductionType;
use Illuminate\Http\Request;

class DeductionController extends Controller
{
    /**
     * Display deductions for an employee
     */
    public function index()
    {
        $results = DeductionType::all();
        return view('admin.payroll.deduction.index', ['results' => $results]);
    }

    /**
     * Show the form for creating a new deduction
     */
    public function create(EmployeePayroll $employeePayroll)
    {
        $deductionTypes = DeductionType::active()->orderBy('name')->get();

        return view('admin.payroll.deduction.create', compact('employeePayroll', 'deductionTypes'));
    }

    /**
     * Store a newly created deduction
     */
    public function store(Request $request, EmployeePayroll $employeePayroll)
    {
        $request->validate([
            'deduction_type_id' => 'required|exists:deduction_types,id',
            'calculation_type' => 'required|in:fixed,percentage,formula',
            'amount' => 'required_if:calculation_type,fixed|nullable|numeric|min:0',
            'percentage' => 'required_if:calculation_type,percentage|nullable|numeric|min:0|max:100',
            'effective_date' => 'required|date',
            'end_date' => 'nullable|date|after:effective_date',
        ]);

        $deductionType = DeductionType::find($request->deduction_type_id);

        $employeePayroll->deductions()->create([
            'deduction_type_id' => $request->deduction_type_id,
            'name' => $deductionType->name,
            'calculation_type' => $request->calculation_type,
            'amount' => $request->calculation_type === 'fixed' ? $request->amount : 0,
            'percentage' => $request->calculation_type === 'percentage' ? $request->percentage : 0,
            'is_statutory' => $deductionType->is_statutory,
            'is_active' => true,
            'effective_date' => $request->effective_date,
            'end_date' => $request->end_date,
            'created_by' => auth()->id()
        ]);

        return redirect()->route('payroll.employees.deductions.index', $employeePayroll)
            ->with('success', 'Deduction added successfully.');
    }

    /**
     * Show the form for editing a deduction
     */
    public function edit(EmployeePayroll $employeePayroll, EmployeeDeductions $deduction)
    {
        $deductionTypes = DeductionType::active()->orderBy('name')->get();

        return view('admin.payroll.employees.deductions.edit', compact('employeePayroll', 'deduction', 'deductionTypes'));
    }

    /**
     * Update the specified deduction
     */
    public function update(Request $request, EmployeePayroll $employeePayroll, EmployeeDeductions $deduction)
    {
        $request->validate([
            'calculation_type' => 'required|in:fixed,percentage,formula',
            'amount' => 'required_if:calculation_type,fixed|nullable|numeric|min:0',
            'percentage' => 'required_if:calculation_type,percentage|nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'end_date' => 'nullable|date|after:effective_date',
        ]);

        $deduction->update([
            'calculation_type' => $request->calculation_type,
            'amount' => $request->calculation_type === 'fixed' ? $request->amount : 0,
            'percentage' => $request->calculation_type === 'percentage' ? $request->percentage : 0,
            'is_active' => $request->boolean('is_active', true),
            'end_date' => $request->end_date,
            'updated_by' => auth()->id()
        ]);

        return redirect()->route('payroll.employees.deductions.index', $employeePayroll)
            ->with('success', 'Deduction updated successfully.');
    }

    /**
     * Remove the specified deduction
     */
    public function destroy(EmployeePayroll $employeePayroll, EmployeeDeductions $deduction)
    {
        $deduction->delete();

        return redirect()->route('payroll.employees.deductions.index', $employeePayroll)
            ->with('success', 'Deduction deleted successfully.');
    }

    /**
     * API endpoint to get employee deductions
     */
    public function apiGetDeductions(EmployeePayroll $employeePayroll)
    {
        $deductions = $employeePayroll->deductions()
            ->with('deductionType')
            ->active()
            ->get();

        return response()->json($deductions);
    }
}