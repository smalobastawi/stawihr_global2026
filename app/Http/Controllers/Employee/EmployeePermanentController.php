<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Employee;
use App\Http\Controllers\Controller;
use App\Lib\Enumerations\UserStatus;
use Illuminate\Http\Request;
use App\Models\Designation;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;

class EmployeePermanentController extends Controller
{


    public function index(Request $request){
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $departmentList     = Department::get();
        $designationList    = Designation::get();
        $roleList           = Role::get();

        $results = Employee::with(['userName'=>function($q){
            $q->with('roles');
        },'department','designation','branch','payGrade','supervisor','hourlySalaries'])
            ->orderBy('date_of_joining','ASC')->where('status',UserStatus::$ACTIVE)->where('permanent_status',UserStatus::$PROBATION_PERIOD)->paginate(10);

        if (request()->ajax())
        {
            if($request->role_id !='') {
                $results = Employee::whereHas('userName', function($q) use($request){
                    $q->with('roles')->where('role_id', $request->role_id);
                })->with('department', 'designation', 'branch', 'payGrade', 'supervisor', 'hourlySalaries')->where('status',UserStatus::$ACTIVE)->where('permanent_status',UserStatus::$PROBATION_PERIOD)->orderBy('date_of_joining','ASC');
            }else{
                $results = Employee::with(['userName' => function ($q) {
                    $q->with('roles');
                }, 'department', 'designation', 'branch', 'payGrade', 'supervisor', 'hourlySalaries'])->where('status',UserStatus::$ACTIVE)->where('permanent_status',UserStatus::$PROBATION_PERIOD)->orderBy('date_of_joining','ASC');
            }

            if($request->department_id !=''){
                $results->where('department_id',$request->department_id);
            }

            if($request->designation_id !=''){
                $results->where('designation_id',$request->designation_id);
            }

            if($request->employee_name !=''){
                $results->where(function($query) use ($request) {
                    $query->where('first_name', 'like','%' . $request->employee_name . '%')
                        ->orWhere('last_name', 'like','%' . $request->employee_name . '%');
                });
            }

            $results = $results->paginate(10);
            return   View('admin.employee.permanent.pagination',['signed_in_user_role'=>$signed_in_user_role,], compact('results'))->render();
        }

        return view('admin.employee.permanent.index',['signed_in_user_role'=>$signed_in_user_role,'results' =>$results,'departmentList' => $departmentList,'designationList'=>$designationList,'roleList'=>$roleList]);
    }


    public function updatePermanent(Request $request){
        $result  = Employee::where('employee_id',$request->employee_id)->update(['permanent_status' => $request->permanent_status]);
        if(!!$result){
            return "success";
        }
    }

}
