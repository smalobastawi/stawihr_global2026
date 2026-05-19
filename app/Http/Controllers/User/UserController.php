<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Http\Controllers\RolesController;
use App\Repositories\CommonRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserRequest;
use App\Lib\Enumerations\GeneralStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\User;
use App\Models\EmployeeEducationQualification;
use App\Models\EmployeeExperience;
use App\Models\Attendance;
use App\Models\EmployeeAward;
use App\Models\EmployeeBonus;
use App\Models\SalaryDetails;
use App\Models\Promotion;
use App\Models\Employee;
use App\Models\TrainingInfo;
use App\Models\LeaveApplication;
use App\Models\Termination;
use App\Models\Warning;
use App\Models\Company;


class UserController extends Controller
{

    protected $commonRepository;

    public function __construct(CommonRepository $commonRepository)
    {
        $this->commonRepository = $commonRepository;
    }

    public function index()
    {
        $allUsers = User::with('roles', 'employeeDetails')
            ->orderBy('id', 'desc')
            ->get();


        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.user.user.index', ['data' => $allUsers, 'signed_in_user_role' => $signed_in_user_role]);
    }

    public function indexInactive()
    {
        $allUsers = User::where('status', '!=', GeneralStatus::ACTIVE)->with('roles', 'employeeDetails')
            ->orderBy('id', 'desc')
            ->get();


        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.user.user.index', ['data' => $allUsers, 'signed_in_user_role' => $signed_in_user_role]);
    }
    public function indexActive()
    {
        $allUsers = User::where('status',  GeneralStatus::ACTIVE)->with('roles', 'employeeDetails')
            ->orderBy('id', 'desc')
            ->get();


        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.user.user.index', ['data' => $allUsers, 'signed_in_user_role' => $signed_in_user_role]);
    }

    public function create()
    {
        $roleList = $this->commonRepository->roleList();
        $roles = Role::all();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.user.user.add_user', ['data' => $roleList, 'signed_in_user_role' => $signed_in_user_role, 'roles' => $roles]);
    }

    public function store(UserRequest $request)
    {

        unset($request['password_confirmation']);
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['created_by'] = Auth::user()->id;
        $input['msisdn'] = $request['msisdn'];
        $input['updated_by'] = Auth::user()->id;
        $roles = Role::whereIn('id', $request->roles)->pluck('name');

        try {
            $user = User::create($input);
            $user->syncRoles($roles);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect('user')->with('success', 'User successfully saved.');
        } else {
            return redirect('user')->with('error', 'Some error found !, Please try again.');
        }
    }

    public function show($id)
    {
        $user = User::where('id', $id)->with('roles', 'employeeDetails')->firstOrFail();
        return view('admin.user.user.show', ['user' => $user]);
    }

    public function edit($id)
    {
        $roleList = $this->commonRepository->roleList();
        $roles = Role::all();
        $editModeData = User::where('id', $id)->with('roles')->first();
        $userRoles = $editModeData->roles()->pluck('id')->toArray();
        $companies = Company::all();

        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

       
        return view('admin.user.user.edit_user', ['roleList' => $roles, 'editModeData' => $editModeData, 'userRoles' => $userRoles, 'companies' => $companies]);
    }

    public function update(Request $request, $id)
    {

        //dd($request->all());
        $user = User::findOrFail($id);

        if (!empty($request['password'])) {
            $input['password'] = Hash::make($request['password']);
            $input['password_changed_at'] = null;
        }
        $input['user_name'] = $request->user_name;
        $input['email'] = $request->email;
        $input['status'] = $request['status'];
        $input['msisdn'] = $request['msisdn'];
        $input['company_id'] = $request->company_id;
        $input['updated_by'] = Auth::user()->id;
        $roles = Role::whereIn('id', $request->roles)->pluck('name');
        try {
            $user->update($input);
            $user->syncRoles($roles);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            \Log::info($bug);
        }

        if ($bug == 0) {
            return redirect()->back()->with('success', 'User successfully updated.');
        } else {
            return redirect()->back()->with('error', 'Some error found !, Please try again.' . $bug);
        }
    }

    public function destroy($id)
    {
        $user = User::withTrashed()->FindOrFail($id);

        try {
            // Check if user has an employee profile
            $employee = Employee::where('user_id', $user->id)->first();

            if ($employee) {
                // User has employee profile - deactivate instead of delete
                $user->status = 0;
                $user->updated_by = Auth::user()->id;
                $user->save();

                echo "deactivated";
            } else {
                // User has no employee profile - safe to delete
                $user->forceDelete();
                echo "success";
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            if (strpos($bug, '1451') !== false) {
                echo 'hasForeignKey';
            } else {
                echo 'error';
            }
        }
    }
    public function delete($id)
    {
        try {
            $user = User::withTrashed()->FindOrFail($id);
            $user->delete();
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
