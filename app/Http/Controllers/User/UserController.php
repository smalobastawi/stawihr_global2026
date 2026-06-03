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
use App\Models\AnonymizedRecordBackup;
use App\Services\AnonymizedDeletionService;


class UserController extends Controller
{

    protected $commonRepository;
    protected $anonymizedDeletionService;

    public function __construct(
        CommonRepository $commonRepository,
        AnonymizedDeletionService $anonymizedDeletionService
    ) {
        $this->commonRepository = $commonRepository;
        $this->anonymizedDeletionService = $anonymizedDeletionService;
    }

    public function index()
    {
        $allUsers = User::with('roles', 'employeeDetails')
            ->orderBy('id', 'desc')
            ->get();


        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.user.user.index', [
            'data' => $allUsers,
            'signed_in_user_role' => $signed_in_user_role,
            'restorableUserIds' => [],
        ]);
    }

    public function indexInactive()
    {
        $restorableUserIds = AnonymizedRecordBackup::restorable()->pluck('user_id');

        $allUsers = User::withTrashed()
            ->where(function ($query) use ($restorableUserIds) {
                $query->where('status', '!=', GeneralStatus::ACTIVE)
                    ->orWhereIn('id', $restorableUserIds);
            })
            ->with('roles', 'employeeDetails')
            ->orderBy('id', 'desc')
            ->get();


        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.user.user.index', [
            'data' => $allUsers,
            'signed_in_user_role' => $signed_in_user_role,
            'restorableUserIds' => $restorableUserIds->all(),
        ]);
    }
    public function indexActive()
    {
        $allUsers = User::where('status',  GeneralStatus::ACTIVE)->with('roles', 'employeeDetails')
            ->orderBy('id', 'desc')
            ->get();


        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.user.user.index', [
            'data' => $allUsers,
            'signed_in_user_role' => $signed_in_user_role,
            'restorableUserIds' => [],
        ]);
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
        $user = User::withTrashed()->findOrFail($id);

        try {
            $this->anonymizedDeletionService->anonymizeUser($user);
            return response('anonymized', 200)->header('Content-Type', 'text/plain');
        } catch (\RuntimeException $e) {
            return response($e->getMessage(), 422)->header('Content-Type', 'text/plain');
        } catch (\Exception $e) {
            \Log::error('User anonymized deletion failed', [
                'user_id' => $id,
                'message' => $e->getMessage(),
            ]);

            if (strpos($e->getMessage(), '1451') !== false) {
                return response('hasForeignKey', 409)->header('Content-Type', 'text/plain');
            }

            return response('error', 500)->header('Content-Type', 'text/plain');
        }
    }

    public function restore($id)
    {
        try {
            $this->anonymizedDeletionService->restoreUser((int) $id);
            return response('restored', 200)->header('Content-Type', 'text/plain');
        } catch (\RuntimeException $e) {
            return response($e->getMessage(), 422)->header('Content-Type', 'text/plain');
        } catch (\Exception $e) {
            \Log::error('User restore failed', [
                'user_id' => $id,
                'message' => $e->getMessage(),
            ]);

            return response('error', 500)->header('Content-Type', 'text/plain');
        }
    }

    public function delete($id)
    {
        return $this->destroy($id);
    }
}
