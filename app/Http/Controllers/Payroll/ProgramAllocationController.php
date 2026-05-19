<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProgramEmployeePayrollAllocation;
use App\Models\Employee;
use App\Models\Program;
use App\Models\Project;
use App\Lib\Enumerations\GeneralStatus;

class ProgramAllocationController extends Controller
{
    public function create(Employee $employee)
    {
        $programs = Program::all();
        return view('admin.employee.program_allocation.create', compact('employee', 'programs'));
    }

    public function store(Request $request, Employee $employee)
    {
        $request->validate([
            'program_id' => 'required|exists:projects,id|unique:programs_to_employee_payroll_allocation,program_id,NULL,id,employee_id,' . $employee->employee_id,
            'percentage_allocated' => 'required|numeric|min:0|max:100',
            'allocation_start_date' => 'required|date',
            'allocation_end_date' => 'required|date|after_or_equal:allocation_start_date',
            'status' => 'required',
        ]);

        // Calculate current total allocation for the employee
        $currentTotalAllocation = ProgramEmployeePayrollAllocation::where('employee_id', $employee->employee_id)->sum('percentage_allocated');

        // Add the new allocation to the total
        $newTotalAllocation = $currentTotalAllocation + $request->percentage_allocated;

        // Validate if the new total exceeds 100%
        if ($newTotalAllocation > 100) {
            return response()->json(['status' => 'error', 'message' => 'Total program allocation cannot exceed 100%.'], 422);
        }

        $programAllocation = ProgramEmployeePayrollAllocation::create([
            'employee_id' => $employee->employee_id,
            'program_id' => $request->program_id,
            'percentage_allocated' => $request->percentage_allocated,
            'allocation_start_date' => $request->allocation_start_date,
            'allocation_end_date' => $request->allocation_end_date,
            'status' => $request->status,
            'created_by' => auth()->id(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Program allocation added successfully!']);
    }

    public function edit($id)
    {
        $programAllocation = ProgramEmployeePayrollAllocation::findOrFail($id);
        $employee = Employee::findOrFail($programAllocation->employee_id);
        $programs = Project::all(); // Assuming you have a Program model

        return view('admin.employee.program_allocation.edit', compact('programAllocation', 'employee', 'programs'));
    }

    public function update(Request $request, $id)
    {
        $programAllocation = ProgramEmployeePayrollAllocation::findOrFail($id);

        $request->validate([
            'program_id' => 'required|exists:projects,id',
            'percentage_allocated' => 'required|numeric|min:0|max:100',
            'allocation_start_date' => 'required|date',
            'allocation_end_date' => 'required|date|after_or_equal:allocation_start_date',
            'status' => 'required',
        ]);

        $programAllocation->update($request->all());

        return redirect()->route('employee.show', $programAllocation->employee_id)->with('success', 'Program allocation updated successfully!');
    }

    public function destroy($id)
    {
        try {
            $programAllocation = ProgramEmployeePayrollAllocation::findOrFail($id);
            $programAllocation->forceDelete();
            return response()->json(['status' => 'success', 'message' => 'Program allocation deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Error deleting program allocation: ' . $e->getMessage()], 500);
        }
    }
}
