<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\EmployeeType;


class EmployeeTypeController extends Controller
{

    public function index(){
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        $results = EmployeeType::get();
        return view('admin.employee.employeeType.index',['results'=>$results, 'signed_in_user_role'=>$signed_in_user_role]);
    }


    public function create(){
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        return view('admin.employee.employeeType.form', [ 'signed_in_user_role'=>$signed_in_user_role]);
    }


    public function store(Request $request) {
        $input = $request->all();
        try{
            EmployeeType::create($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug==0){
            return redirect('department')->with('success', 'EmployeeType successfully saved.');
        }else {
            return redirect('department')->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }


    public function edit($id){
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $editModeData = EmployeeType::findOrFail($id);
        return view('admin.employee.employeeType.form',['editModeData' => $editModeData, 'signed_in_user_role'=>$signed_in_user_role]);
    }


    public function update(Request $request,$id) {
        $department = EmployeeType::findOrFail($id);
        $input = $request->all();
        try{
            $department->update($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug==0){
            return redirect()->back()->with('success', 'EmployeeType successfully updated ');
        }else {
            return redirect()->back()->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }


    public function destroy($id){

        $count = Employee::where('department_id','=',$id)->count();

        if($count>0){

            return  'hasForeignKey';
        }


        try{
            $department = EmployeeType::FindOrFail($id);
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
