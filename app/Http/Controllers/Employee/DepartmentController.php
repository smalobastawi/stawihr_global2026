<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Employee;

use App\Http\Requests\DepartmentRequest;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;

use App\Models\Department;

use App\Models\Employee;


class DepartmentController extends Controller
{

    public function index(){
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        $results = Department::withCount('employees')->with('departmentHead')->get();

        return view('admin.employee.department.index',['results'=>$results, 'signed_in_user_role'=>$signed_in_user_role]);
    }


    public function create(){
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $employees = Employee::select('employee_id', 'first_name', 'middle_name', 'last_name')
            ->where('status', 1)
            ->get();
        return view('admin.employee.department.form', ['signed_in_user_role'=>$signed_in_user_role, 'employees'=>$employees]);
    }


    public function store(DepartmentRequest $request) {
        $input = $request->all();
        try{
            Department::create($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug==0){
            return redirect()->route('department.index')->with('success', 'Department successfully saved.');
        }else {
            return redirect()->route('department.index')->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }


    public function edit($id){
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $editModeData = Department::findOrFail($id);
        $employees = Employee::select('employee_id', 'first_name', 'middle_name', 'last_name')
            ->where('status', 1)
            ->get();
        return view('admin.employee.department.form',['editModeData' => $editModeData, 'signed_in_user_role'=>$signed_in_user_role, 'employees'=>$employees]);
    }


    public function update(DepartmentRequest $request,$id) {
        $department = Department::findOrFail($id);
        $input = $request->all();
        try{
            $department->update($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug==0){
            return redirect()->route('department.index')->with('success', 'Department successfully updated ');
        }else {
            return redirect()->route('department.index')->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }


    public function destroy($id){

      $count = Employee::where('department_id','=',$id)->count();

         if($count>0){

            return  'hasForeignKey';
         }


        try{
            $department = Department::FindOrFail($id);
            $department->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug==0){
            echo "success";
        }elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }

}
