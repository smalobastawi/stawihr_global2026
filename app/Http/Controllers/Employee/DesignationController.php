<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Employee;

use App\Http\Requests\DesignationRequest;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Designation;
use App\Models\User;
use App\Models\Employee;



class DesignationController extends Controller
{

    public function index(){
        $results = Designation::withCount('employees')->get();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        return view('admin.employee.designation.index',['results'=>$results,'signed_in_user_role'=>$signed_in_user_role]);
    }


    public function create(){
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        return view('admin.employee.designation.form', ['signed_in_user_role'=>$signed_in_user_role]);
    }


    public function store(DesignationRequest $request) {
        $input = $request->all();
        try{
            Designation::create($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
            \Log::info($e->getMessage());
        }

        if($bug==0){
            return redirect()->route('designation.index')->with('success', 'Designation Successfully saved.');
        }else {
            return redirect()->route('designation.index')->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }


    public function edit($id){
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $editModeData = Designation::findOrFail($id);
        return view('admin.employee.designation.form',['editModeData' => $editModeData, 'signed_in_user_role'=>$signed_in_user_role]);
    }


    public function update(DesignationRequest $request,$id) {
        $data = Designation::findOrFail($id);
        $input = $request->all();
        try{
            $data->update($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
            \Log::info($e->getMessage());
        }

        if($bug==0){
            return redirect()->back()->with('success', 'Designation Successfully updated.');
        }else {
            return redirect()->back()->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }


    public function destroy($id){

       $count = Employee::where('designation_id','=',$id)->count();

         if($count>0){

            return  'hasForeignKey';
         }

        try{
            $department = Designation::FindOrFail($id);
            $department->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
            \Log::info($e->getMessage());
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
