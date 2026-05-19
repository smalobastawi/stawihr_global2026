<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Project;
use App\Models\ProjectEmployeePayrollAllocation;
use App\Exports\ProjectAllocationReportExport;
use App\Models\Department; // Added
use Excel;

class ProjectAllocationReportController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::all(); // Fetch all departments
        $allProjects = Project::all(); // Fetch all projects for the filter dropdown

        $reportData = [];
        $projectNames = [];

        // Only proceed if there are projects in the system
        if ($allProjects->isNotEmpty()) {
            // Prepare project names for table headers
            foreach ($allProjects as $project) {
                $projectNames[] = $project->name;
            }

            $employeesQuery = Employee::with(['projectAllocations.project', 'department', 'currentPayrollRecord']);

            // Apply department filter
            if ($request->filled('department_id')) {
                $employeesQuery->where('department_id', $request->department_id);
            }

            // Apply project filter
            if ($request->filled('project_id')) {
                $employeesQuery->whereHas('projectAllocations', function ($query) use ($request) {
                    $query->where('project_id', $request->project_id);
                });
            }

            $employees = $employeesQuery->get();

            foreach ($employees as $employee) {
                $hasAllocation = false; // Flag to check if employee has any allocation for selected projects
                $rowData = [
                    'payroll_number' => $employee->payroll_number ?? '',
                    'employee_name' => $employee->full_name ?? '',
                    'department' => $employee->department->department_name ?? '',
                ];

                $grossSalary = $employee->currentPayrollRecord ? $employee->currentPayrollRecord->gross_salary : 0;
                $totalAllocation = 0;
                $totalAllocatedAmount = 0;

                foreach ($allProjects as $project) { // Iterate through all projects to ensure all columns are present
                    $allocation = $employee->projectAllocations->where('project_id', $project->id)->first();
                    $percentage = $allocation ? $allocation->percentage_allocated : 0;
                    $allocatedAmount = ($grossSalary * $percentage) / 100;

                    $rowData[$project->name . '_percentage'] = $percentage . '%';
                    $rowData[$project->name . '_amount'] = number_format($allocatedAmount, 2);

                    if ($allocation) { // If there's an allocation, set the flag
                        $hasAllocation = true;
                    }

                    $totalAllocation += $percentage;
                    $totalAllocatedAmount += $allocatedAmount;
                }
                $rowData['total_allocation'] = $totalAllocation . '%';
                $rowData['total_allocated_amount'] = number_format($totalAllocatedAmount, 2);

                if ($hasAllocation) { // Only push employee data if they have at least one allocation
                    $reportData[] = $rowData;
                }
            }
        }

        return view('admin.project.reports.project_allocation_report', compact('reportData', 'projectNames', 'departments', 'allProjects'));
    }

    public function export(Request $request)
    {
        $departments = Department::all(); // Fetch all departments
        $allProjects = Project::all(); // Fetch all projects for the filter dropdown

        $reportData = [];
        $projectNames = [];

        // Only proceed if there are projects in the system
        if ($allProjects->isNotEmpty()) {
            // Prepare project names for table headers
            foreach ($allProjects as $project) {
                $projectNames[] = $project->name;
            }

            $employeesQuery = Employee::with(['projectAllocations.project', 'department', 'currentPayrollRecord']);

            // Apply department filter
            if ($request->filled('department_id')) {
                $employeesQuery->where('department_id', $request->department_id);
            }

            // Apply project filter
            if ($request->filled('project_id')) {
                $employeesQuery->whereHas('projectAllocations', function ($query) use ($request) {
                    $query->where('project_id', $request->project_id);
                });
            }

            $employees = $employeesQuery->get();

            foreach ($employees as $employee) {
                $hasAllocation = false; // Flag to check if employee has any allocation for selected projects
                $rowData = [
                    'payroll_number' => $employee->payroll_number ?? '',
                    'employee_name' => $employee->full_name ?? '',
                    'department' => $employee->department->department_name ?? '',
                ];

                $grossSalary = $employee->currentPayrollRecord ? $employee->currentPayrollRecord->gross_salary : 0;
                $totalAllocation = 0;
                $totalAllocatedAmount = 0;

                foreach ($allProjects as $project) { // Iterate through all projects to ensure all columns are present
                    $allocation = $employee->projectAllocations->where('project_id', $project->id)->first();
                    $percentage = $allocation ? $allocation->percentage_allocated : 0;
                    $allocatedAmount = ($grossSalary * $percentage) / 100;

                    $rowData[$project->name . '_percentage'] = $percentage . '%';
                    $rowData[$project->name . '_amount'] = number_format($allocatedAmount, 2);

                    if ($allocation) { // If there's an allocation, set the flag
                        $hasAllocation = true;
                    }

                    $totalAllocation += $percentage;
                    $totalAllocatedAmount += $allocatedAmount;
                }
                $rowData['total_allocation'] = $totalAllocation . '%';
                $rowData['total_allocated_amount'] = number_format($totalAllocatedAmount, 2);

                if ($hasAllocation) { // Only push employee data if they have at least one allocation
                    $reportData[] = $rowData;
                }
            }
        }

        $fileName = 'Project_Allocation_Report_' . date('Y-m-d') . '.xlsx';
        return Excel::download(new ProjectAllocationReportExport($reportData, $projectNames), $fileName);
    }
}
