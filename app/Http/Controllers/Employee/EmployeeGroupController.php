<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeGroupRequest;
use App\Http\Requests\UpdateEmployeeGroupRequest;
use App\Models\Location;
use App\Models\Employee;
use App\Models\EmployeeGroup;
use Illuminate\Http\Request;
use App\Models\User;

class EmployeeGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $results = EmployeeGroup::with('branch')->get();
        return view('admin.employee.employeeGroup.index', ['results' => $results, 'signed_in_user_role' => $signed_in_user_role]);
    }


    public function create()
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $branchList = Location::get();
        return view('admin.employee.employeeGroup.form', ['signed_in_user_role' => $signed_in_user_role, 'branchList' => $branchList]);
    }


    public function store(Request $request)
    {
        $input = $request->except('_token');
        try {
            EmployeeGroup::create($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('employeeGroup.index')->with('success', 'EmployeeGroup successfully saved.');
        } else {
            return redirect()->route('employeeGroup.index')->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }


    public function edit($id)
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $editModeData = EmployeeGroup::with('branch')->findOrFail($id);
        $branchList = Location::get();
        return view('admin.employee.employeeGroup.editForm', ['editModeData' => $editModeData, 'signed_in_user_role' => $signed_in_user_role, 'branchList' => $branchList]);
    }


    public function update(Request $request, $id)
    {
        $department = EmployeeGroup::findOrFail($id);
        $input = $request->all();
        try {
            $department->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->back()->with('success', 'EmployeeGroup successfully updated ');
        } else {
            return redirect()->back()->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }


    public function destroy($id)
    {

        $count = Employee::where('employee_section_id', '=', $id)->count();

        if ($count > 0) {

            return  'hasForeignKey';
        }
        try {
            $department = EmployeeGroup::FindOrFail($id);
            $department->delete();
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            echo "success";
        } elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }
}
