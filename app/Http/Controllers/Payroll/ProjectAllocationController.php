<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProjectEmployeePayrollAllocation;
use App\Models\Employee;
use App\Models\Project;
use App\Lib\Enumerations\GeneralStatus;

class ProjectAllocationController extends Controller
{
    public function create(Employee $employee)
    {
        $projects = Project::all();
        return view('admin.employee.project_allocation.create', compact('employee', 'projects'));
    }

    public function store(Request $request, Employee $employee)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id|unique:projects_to_employee_payroll_allocation,project_id,NULL,id,employee_id,' . $employee->employee_id,
            'percentage_allocated' => 'required|numeric|min:0|max:100',
            'allocation_start_date' => 'required|date',
            'allocation_end_date' => 'required|date|after_or_equal:allocation_start_date',
            'status' => 'required',
        ],
        [
            'project_id.unique' => 'This Project has already been allocated to this Employee.',
        ]);

        // Custom validation for allocation dates against project dates
        $project = Project::findOrFail($request->project_id);
        $allocationStartDate = new \DateTime($request->allocation_start_date);
        $allocationEndDate = new \DateTime($request->allocation_end_date);
        $projectStartDate = new \DateTime($project->start_date);
        $projectEndDate = new \DateTime($project->end_date);

        if ($allocationStartDate < $projectStartDate) {
            return response()->json(['status' => 'error', 'message' => 'Allocation start date cannot be earlier than the project start date (' . $projectStartDate->format('d M, Y') . ').'], 422);
        }

        if ($allocationEndDate > $projectEndDate) {
            return response()->json(['status' => 'error', 'message' => 'Allocation end date cannot be later than the project end date (' . $projectEndDate->format('d M, Y') . ').'], 422);
        }

        // Calculate current total allocation for the employee
        $currentTotalAllocation = ProjectEmployeePayrollAllocation::where('employee_id', $employee->employee_id)->sum('percentage_allocated');

        // Add the new allocation to the total
        $newTotalAllocation = $currentTotalAllocation + $request->percentage_allocated;

        // Validate if the new total exceeds 100%
        if ($newTotalAllocation > 100) {
            return response()->json(['status' => 'error', 'message' => 'Total project allocation cannot exceed 100%.'], 422);
        }

        $projectAllocation = ProjectEmployeePayrollAllocation::create([
            'employee_id' => $employee->employee_id,
            'project_id' => $request->project_id,
            'percentage_allocated' => $request->percentage_allocated,
            'allocation_start_date' => $request->allocation_start_date,
            'allocation_end_date' => $request->allocation_end_date,
            'status' => $request->status,
            'created_by' => auth()->id(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Project allocation added successfully!']);
    }

    public function edit($id)
    {
        $projectAllocation = ProjectEmployeePayrollAllocation::findOrFail($id);
        $employee = Employee::findOrFail($projectAllocation->employee_id);
        $projects = Project::all(); // Assuming you have a Project model

        return view('admin.employee.project_allocation.edit', compact('projectAllocation', 'employee', 'projects'));
    }

    public function update(Request $request, $id)
    {
        $projectAllocation = ProjectEmployeePayrollAllocation::findOrFail($id);

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'percentage_allocated' => 'required|numeric|min:0|max:100',
            'allocation_start_date' => 'required|date',
            'allocation_end_date' => 'required|date|after_or_equal:allocation_start_date',
            'status' => 'required',
        ]);

        // Custom validation for allocation dates against project dates
        $project = Project::findOrFail($request->project_id);
        $allocationStartDate = new \DateTime($request->allocation_start_date);
        $allocationEndDate = new \DateTime($request->allocation_end_date);
        $projectStartDate = new \DateTime($project->start_date);
        $projectEndDate = new \DateTime($project->end_date);

        if ($allocationStartDate < $projectStartDate) {
            return redirect()->back()->with('error', 'Allocation start date cannot be earlier than the project start date (' . $projectStartDate->format('d M, Y') . ').')->withInput();
        }

        if ($allocationEndDate > $projectEndDate) {
            return redirect()->back()->with('error', 'Allocation end date cannot be later than the project end date (' . $projectEndDate->format('d M, Y') . ').')->withInput();
        }

        $projectAllocation->update($request->all());

        return redirect()->route('employee.show', $projectAllocation->employee_id)->with('success', 'Project allocation updated successfully!');
    }

    public function destroy($id)
    {
        try {
            $projectAllocation = ProjectEmployeePayrollAllocation::findOrFail($id);
            $projectAllocation->forceDelete();
            return response()->json(['status' => 'success', 'message' => 'Project allocation deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Error deleting project allocation: ' . $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        $departments = \App\Models\Department::all();
        $allProjects = Project::all();

        $reportData = [];
        $projectNames = [];
        $employees = collect(); // Initialize $employees as an empty collection
        $projects = collect(); // Initialize $projects as an empty collection

        // Only proceed if there are projects in the system
        if ($allProjects->isNotEmpty()) {
            foreach ($allProjects as $project) {
                $projectNames[] = $project->name;
            }

            $employeesQuery = Employee::with(['projectAllocations.project', 'department']);

            if ($request->filled('department_id')) {
                $employeesQuery->where('department_id', $request->department_id);
            }

            if ($request->filled('project_id')) {
                $employeesQuery->whereHas('projectAllocations', function ($query) use ($request) {
                    $query->where('project_id', $request->project_id);
                });
            }

            $employees = $employeesQuery->get();
            $projects = Project::all();

            foreach ($employees as $employee) {
                $hasAllocation = false; // Flag to check if employee has any allocation for selected projects
                $rowData = [
                    'employee_id' => $employee->employee_id,
                    'payroll_number' => $employee->payroll_number ?? '',
                    'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                    'department' => $employee->department->department_name ?? '',
                ];

                $totalAllocation = 0;
                foreach ($allProjects as $project) { // Iterate through all projects to ensure all columns are present
                    $allocation = $employee->projectAllocations->where('project_id', $project->id)->first();
                    $percentage = $allocation ? $allocation->percentage_allocated : 0;
                    $rowData[$project->name] = $percentage . '%';

                    if ($allocation) { // If there's an allocation, set the flag
                        $hasAllocation = true;
                    }
                    $totalAllocation += $percentage;
                }
                $rowData['total_allocation'] = $totalAllocation . '%';

                if ($hasAllocation) { // Only push employee data if they have at least one allocation
                    $reportData[] = $rowData;
                }
            }
        }

        return view('admin.project.allocations.index', compact('reportData', 'projectNames', 'departments', 'allProjects', 'employees', 'projects'));
    }

    public function storeAllocation(Request $request)
    {
        $request->validate(['employee_id' => 'required|exists:employee,employee_id']);

        $employee = Employee::findOrFail($request->employee_id);

        // Calculate current total allocation for the employee
        $currentTotalAllocation = ProjectEmployeePayrollAllocation::where('employee_id', $employee->employee_id)->sum('percentage_allocated');

        // Add the new allocation to the total
        $newTotalAllocation = $currentTotalAllocation + $request->percentage_allocated;

        // Validate if the new total exceeds 100%
        if ($newTotalAllocation > 100) {
            return back()->with('error', 'Total project allocation cannot exceed 100%.');
        }

        $request->validate([
            'project_id' => 'required|exists:projects,id|unique:projects_to_employee_payroll_allocation,project_id,NULL,id,employee_id,' . $request->employee_id,
            'percentage_allocated' => 'required|numeric|min:0|max:100',
            'allocation_start_date' => 'required|date',
            'allocation_end_date' => 'required|date|after_or_equal:allocation_start_date',
            'status' => 'required',
        ],
        [
            'project_id.unique' => 'This project has already been allocated to this employee.',
        ]);

        // Custom validation for allocation dates against project dates
        $project = Project::findOrFail($request->project_id);
        $allocationStartDate = new \DateTime($request->allocation_start_date);
        $allocationEndDate = new \DateTime($request->allocation_end_date);
        $projectStartDate = new \DateTime($project->start_date);
        $projectEndDate = new \DateTime($project->end_date);

        if ($allocationStartDate < $projectStartDate) {
            return redirect()->back()->with('error', 'Allocation start date cannot be earlier than the project start date (' . $projectStartDate->format('d M, Y') . ').')->withInput();
        }

        if ($allocationEndDate > $projectEndDate) {
            return redirect()->back()->with('error', 'Allocation end date cannot be later than the project end date (' . $projectEndDate->format('d M, Y') . ').')->withInput();
        }

        ProjectEmployeePayrollAllocation::create([
            'employee_id' => $employee->employee_id,
            'project_id' => $request->project_id,
            'percentage_allocated' => $request->percentage_allocated,
            'allocation_start_date' => $request->allocation_start_date,
            'allocation_end_date' => $request->allocation_end_date,
            'status' => $request->status,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('project.project-allocations.index')->with('success', 'Project allocation added successfully!');
    }
}