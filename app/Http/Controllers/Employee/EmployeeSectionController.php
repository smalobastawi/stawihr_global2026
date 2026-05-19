<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\SectionRequest;
use App\Models\Location;
use App\Models\Employee;
use App\Models\EmployeeSection;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EmployeeSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $results = EmployeeSection::withCount('employees')->with(['branch', 'sectionHead'])->get();
        return view('admin.employee.employeeSection.index', ['results' => $results, 'signed_in_user_role' => $signed_in_user_role]);
    }

    public function create()
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $branchList = Location::get();
        $employees = Employee::select('employee_id', 'first_name', 'middle_name', 'last_name')
            ->where('status', 1)
            ->get();
        return view('admin.employee.employeeSection.form', ['signed_in_user_role' => $signed_in_user_role, 'branchList' => $branchList, 'employees' => $employees]);
    }

    public function store(SectionRequest $request)
    {
        $input = $request->except('_token');
        $input['created_by'] = Auth::id();

        try {
            EmployeeSection::create($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }
        if ($bug == 0) {
            return redirect()->route('employeeSection.index')->with('success', 'Section successfully saved.');
        } else {
            return redirect()->route('employeeSection.index')->with('error', 'An error occurred, please try again. If the problem persists, contact Support for assistance.');
        }
    }

    public function edit($id)
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $editModeData = EmployeeSection::with('branch')->findOrFail($id);
        $branchList = Location::get();
        $employees = Employee::select('employee_id', 'first_name', 'middle_name', 'last_name')
            ->where('status', 1)
            ->get();
        return view('admin.employee.employeeSection.editForm', ['editModeData' => $editModeData, 'signed_in_user_role' => $signed_in_user_role, 'branchList' => $branchList, 'employees' => $employees]);
    }


    public function update(SectionRequest $request, $id)
    {
        $section = EmployeeSection::findOrFail($id);
        $input = $request->all();

        try {
            $section->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('employeeSection.index')->with('success', 'Section successfully updated.');
        } else {
            return redirect()->route('employeeSection.index')->with('error', 'An error occurred, please try again. If the problem persists, contact Support for assistance.');
        }
    }


    public function destroy($id)
    {
        $count = Employee::where('employee_section_id', '=', $id)->count();

        if ($count > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'This section cannot be deleted because it has employees assigned to it. Please reassign or remove the employees first.'
            ]);
        }

        try {
            $section = EmployeeSection::findOrFail($id);
            $section->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Section deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
}
