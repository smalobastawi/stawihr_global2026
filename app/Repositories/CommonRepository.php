<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Repositories;
use App\Models\TrainingFacilitator;
use Illuminate\Support\Facades\DB;
use App\Models\TrainingType;
use App\Models\WorkShift;
use App\Models\Employee;
use App\Models\Role;
use App\Models\SalaryBonusTypes;


class CommonRepository
{

    public function roleList(){
        $results = Role::get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->id] = $value->name;
        }
        return $options ;
    }


    public function userList(){
        $results = DB::table('user')->where('status',1)->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->id] = $value->user_name;
        }
        return $options ;
    }


    public function departmentList(){
        $results = DB::table('department')->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->department_id] = $value->department_name;
        }
        return $options ;
    }


    public function designationList(){
        $results = DB::table('designation')->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->designation_id] = $value->designation_name;
        }
        return $options ;
    }


    public function branchList(){
        $results = DB::table('branch')->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->location_id] = $value->branch_name;
        }
        return $options ;
    }


    public function supervisorList(){
        $results = DB::table('employee')->where('status',1)->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->employee_id] = $value->first_name.' '.$value->last_name;
        }
        return $options ;
    }


    public function holidayList(){
        $results = DB::table('holiday')->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->holiday_id] = $value->holiday_name;
        }
        return $options ;
    }


    public function weekList(){
        $results = ['Friday','Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday'];
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value] = $value;
        }
        return $options ;
    }


    public function leaveTypeList(){
        $results = DB::table('leave_type')->where('status', '=', 1)->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->leave_type_id] = $value->leave_type_name;
        }
        return $options ;
    }


    public function workShiftList(){
        $results = WorkShift::get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->work_shift_id] = $value->work_shift_name;
        }
        return $options ;
    }

    public function employeeList(){
        $results = Employee::where('status',1)->where('department_id', '!=',15)->orderBy('first_name', 'ASC')->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->employee_id] = $value->first_name.' '. $value->middle_name.' '.$value->last_name;
        }
        return $options ;
    }

    public function employeeListTermination(){
        $results = Employee::where('department_id', '!=',15)->orderBy('first_name', 'ASC')->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->employee_id] = $value->first_name.' '. $value->middle_name.' '.$value->last_name;
        }
        return $options ;
    }

    public function employeeListAll(){

        $results = Employee::where('status',1)->orderBy('first_name', 'ASC')->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->employee_id] = $value->first_name.' '.$value->middle_name.' '.$value->last_name;
        }
        return $options ;
    }
        public function employeeListOnlyWithPayrolls(){

        $results = Employee::whereHas('employeePayroll')->where('status',1)->orderBy('first_name', 'ASC')->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->employee_id] = $value->first_name.' '.$value->middle_name.' '.$value->last_name;
        }
        return $options ;
    }

    public function employeeListAllP9(){

        $results = Employee::orderBy('first_name', 'ASC')->get();
        foreach ($results as $key => $value) {
            $options [$value->employee_id] = $value->first_name.' '.$value->middle_name.' '.$value->last_name;
        }
        return $options ;
    }


    public function employeeListForLeaves(){
        $results = Employee::where('status',1)->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->employee_id] = $value->first_name.' '.$value->middle_name.' '.$value->last_name;
        }
        return $options ;
    }
    public function employeeListManagement(){
        $results = Employee::where('status',1)->where('department_id', '=', 15)->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->employee_id] = $value->first_name.' '.$value->last_name;
        }
        return $options ;
    }
    public function employeeListManagementP9(){
        $results = Employee::where('department_id', '=', 15)->get();

        foreach ($results as $key => $value) {
            $options [$value->employee_id] = $value->first_name.' '.$value->last_name;
        }
        return $options ;
    }

    public function salaryBonusTypes(){
        $results = SalaryBonusTypes::where('status',1)->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->bonus_type_name] = $value->bonus_type_name;
        }
        return $options ;
    }


    public function getEmployeeInfo($id)
    {
        return  Employee::where('employee_id',$id)->first();
    }
    public function getEmployeeDetails($id){

        return  Employee::where('user_id',$id)->first();
    }

    public function trainingTypeList(){
        $results = TrainingType::where('status',1)->get();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $key => $value) {
            $options [$value->training_type_id] = $value->training_type_name;
        }
        return $options ;
    }

    public function trainingFacilitorList(){
        $results=TrainingFacilitator::all();
        $options = [''=>'---- Please select ----'];
        foreach ($results as $value) {
            $options [$value->id] = '('.$value->type.')'.$value->name;
        }
        return $options ;
    }

}