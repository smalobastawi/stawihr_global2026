<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaveGroupRequest;
use App\Http\Requests\StoreLeaveGroupSettingRequest;
use App\Http\Requests\UpdateLeaveGroupRequest;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\LeaveGroup;
use App\Models\LeaveType;
use App\Models\LeaveGroupSetting;
use Illuminate\Http\Request;

class LeaveGroupController extends Controller
{
    /**
     * List all leave groups.
     */
    public function index()
    {
        // Fetch leave groups with their settings and the related leave type (if relationships are defined)
        $leaveGroups = LeaveGroup::with('settings.leaveType')->get();
        return view('admin.leave.leaveGroup.index', compact('leaveGroups'));
    }

    /**
     * Show the create form.
     */
    public function create()
    {
        // Retrieve all leave types for the settings table
        $leaveTypes = LeaveType::all();
        return view('admin.leave.leaveGroup.form', compact('leaveTypes'));
    }

    /**
     * Store a new leave group and its settings.
     */
    public function store(StoreLeaveGroupSettingRequest $request)
    {

        // Retrieve validated data from the form request
        $data = $request->validated();

        // Create the leave group
        $leaveGroup = LeaveGroup::create([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active'   => $data['is_active'] ?? true,
        ]);



        // Loop through the settings array (keyed by leave type id) and create the corresponding settings
        // Only save settings where 'active' is checked (true)
        foreach ($data['settings'] as $leaveTypeId => $settingData) {
            // Skip if not active (status checkbox not checked)
            if (empty($settingData['active'])) {
                continue;
            }

            $settingData['leave_type_id']  = $leaveTypeId;
            $settingData['leave_group_id'] = $leaveGroup->id;

            LeaveGroupSetting::create($settingData);
        }

        return redirect()->route('leaveGroup.index')->with('success', 'Leave group created successfully.');
    }

    /**
     * Show the edit form with existing group and its settings.
     */

    public function edit(Request $request, LeaveGroup $leaveGroup)
    {
        if ($request->action == 'filter_employees') {
            $currentEmployeeIds = $leaveGroup->employees->pluck('employee_id');
            $employeesNotInGroup = Employee::whereNotIn('employee_id', $currentEmployeeIds);
            $viewFile = 'admin.leave.leaveGroup.filteremployees';
            if ($request->filled('ftype')) {
                $employeesNotInGroup = $leaveGroup->employees();
                $viewFile = 'admin.leave.leaveGroup.filteremployeesx';
            }
            if ($request->department) {
                $employeesNotInGroup->where('department_id', $request->department);
            }
            if ($request->designation) {
                $employeesNotInGroup->where('designation_id', $request->designation);
            }
            if ($request->name) {
                $employeesNotInGroup->whereRaw("CONCAT(first_name, middle_name, last_name) LIKE ?", ["%{$request->name}%"]);
            }
            $employees = $employeesNotInGroup->orderBy('first_name', 'asc')->get();
            return view($viewFile)->with(['employees' => $employees]);
        }
        $currentEmployeeIds = $leaveGroup->employees->pluck('employee_id');

        $leaveTypes = LeaveType::all();
        $settings = $leaveGroup->settings->keyBy('leave_type_id')->toArray();

        // Fetch current employees in the group

        // Fetch employees not in the group
        $employeesNotInGroup = Employee::whereNotIn('employee_id', $currentEmployeeIds)->orderBy('first_name', 'asc')->get();
        $departments = Department::all();
        $designations = Designation::all();

        return view('admin.leave.leaveGroup.form', compact('designations', 'departments', 'leaveGroup', 'leaveTypes', 'settings', 'employeesNotInGroup'));
    }

    /**
     * Update the specified leave group and its settings.
     */
    public function update(UpdateLeaveGroupRequest $request, LeaveGroup $leaveGroup)
    {
        // Retrieve validated data from the form request
        $data = $request->validated();

        $leaveGroup->update([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active'   => $data['is_active'] ?? true,
        ]);


        // Get all active leave type IDs from the submitted data
        $activeLeaveTypeIds = [];

        // Update or create each leave group setting based on the leave type id
        // Only save settings where 'active' is checked (true)
        foreach ($data['settings'] as $leaveTypeId => $settingData) {
            // Skip if not active (status checkbox not checked)
            if (empty($settingData['active'])) {
                continue;
            }

            $activeLeaveTypeIds[] = $leaveTypeId;

            $settingData['leave_type_id']  = $leaveTypeId;
            $settingData['leave_group_id'] = $leaveGroup->id;

            LeaveGroupSetting::updateOrCreate(
                ['leave_type_id' => $leaveTypeId, 'leave_group_id' => $leaveGroup->id],
                $settingData
            );
        }

        // Delete settings for leave types that are no longer active
        LeaveGroupSetting::where('leave_group_id', $leaveGroup->id)
            ->whereNotIn('leave_type_id', $activeLeaveTypeIds)
            ->delete();

        return redirect()->route('leaveGroup.index')->with('success', 'Leave group updated successfully.');
    }


    /**
     * Delete the specified leave group.
     */
    public function destroy(LeaveGroup $leaveGroup)
    {
        try {
            // Check for linked records before attempting deletion
            $linkedRecords = [];

            // Check for employees linked to this leave group
            $employeeCount = $leaveGroup->employees()->count();
            if ($employeeCount > 0) {
                $linkedRecords[] = $employeeCount . ' employee(s)';
            }

            // Check for public holidays linked to this leave group
            $publicHolidayCount = $leaveGroup->publicHolidays()->count();
            if ($publicHolidayCount > 0) {
                $linkedRecords[] = $publicHolidayCount . ' public holiday(s)';
            }

            // Check for weekly holidays linked to this leave group
            $weeklyHolidayCount = $leaveGroup->weeklyHolidays()->count();
            if ($weeklyHolidayCount > 0) {
                $linkedRecords[] = $weeklyHolidayCount . ' weekly holiday(s)';
            }

            // If there are linked records, prevent deletion and return error
            if (!empty($linkedRecords)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete this leave group because it is linked to: ' . implode(', ', $linkedRecords) . '. Please remove these associations first.'
                ], 422);
            }

            // Delete related settings first
            $leaveGroup->settings()->delete();

            // Delete the leave group
            $leaveGroup->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Leave group deleted successfully.'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database query exceptions (including foreign key constraints)
            $errorMessage = $e->getMessage();

            // Check for foreign key constraint violation
            if (strpos($errorMessage, '1451') !== false ||
                strpos($errorMessage, '23000') !== false ||
                strpos($errorMessage, 'foreign key constraint') !== false ||
                strpos($errorMessage, 'Integrity constraint violation') !== false) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete this leave group because it is linked to other records. Please remove all associations first.'
                ], 422);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Database error occurred while deleting the leave group: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            \Log::error("Error deleting leave group ID {$leaveGroup->id}: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while deleting the leave group: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, LeaveGroup $leaveGroup)
    {
        if ($request->action == 'filter_employees') {
            $currentEmployeeIds = $leaveGroup->employees->pluck('employee_id');
            $employees = $leaveGroup->employees();
            if ($request->department) {
                $employees = $employees->where('department_id', $request->department);
            }

            if ($request->designation) {
                $employees =  $employees->where('designation_id', $request->designation);
            }
            if ($request->name) {
                $searchName = strtolower($request->name);
                $employees = $employees->where(function ($query) use ($searchName) {
                    $query->where('first_name', 'LIKE', "%{$searchName}%")
                        ->orWhere('middle_name', 'LIKE', "%{$searchName}%")
                        ->orWhere('last_name', 'LIKE', "%{$searchName}%");
                });
            }
            $employees = $employees->orderBy('first_name', 'asc')->get();
            return view('admin.leave.leaveGroup.employees_show')->with(['employees' => $employees]);
        }
        $settings = $leaveGroup->settings->keyBy('leave_type_id')->toArray();

        $leaveTypes = LeaveType::all();
        // $employeesNotInGroup = Employee::whereNotIn('employee_id', $currentEmployeeIds)->orderBy('first_name','asc')->take(100)->get();
        $departments = Department::all();
        $designations = Designation::all();

        return view('admin.leave.leaveGroup.show', compact('designations', 'departments', 'leaveGroup', 'leaveTypes', 'settings'));
    }


    public function addEmployee(Request $request, LeaveGroup $leaveGroup, Employee $employee)
    {
        // Attach employee to leave group, but avoid duplicates
        $leaveGroup->employees()->syncWithoutDetaching([$employee->employee_id]);

        return response()->json(['message' => 'Employee added successfully']);
    }

    // Remove Employee from Leave Group
    public function deleteEmployee(Request $request, LeaveGroup $leaveGroup, Employee $employee)
    {
        // Check if the employee exists in the group
        if (!$leaveGroup->employees()->where('employee_leavegroups.employee_id', $employee->employee_id)->exists()) {
            return response()->json(['message' => 'Employee not found in the leave group'], 404);
        }

        // Detach employee from the leave group
        $leaveGroup->employees()->detach($employee->employee_id);

        return response()->json(['message' => 'Employee removed successfully']);
    }


    public function addEmployees(Request $request, LeaveGroup $leaveGroup)
    {
        // Validate the request
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employee,employee_id'
        ]);

        // Attach employees to leave group, avoiding duplicates
        $leaveGroup->employees()->syncWithoutDetaching($request->employee_ids);

        return response()->json(['message' => 'Employees added successfully']);
    }


    // Remove Employee from Leave Group
    public function deleteEmployees(Request $request, LeaveGroup $leaveGroup)
    {
        // Validate the request
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employee,employee_id'
        ]);

        // Detach employees from the leave group
        $leaveGroup->employees()->detach($request->employee_ids);

        return response()->json(['message' => 'Employees removed successfully']);
    }


    // List Employees in a Leave Group
    public function listEmployees(Request $request, LeaveGroup $leaveGroup)
    {
        $employees = $leaveGroup->employees()->get();
        return response()->json($employees);
    }
}
