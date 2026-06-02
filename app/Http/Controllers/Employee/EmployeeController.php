<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Employee;

use Exception;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\LeaveRollover;
use App\Models\Location;
use App\Models\Warning;
use Carbon\Traits\Date;
use App\Models\DailyPay;
use App\Models\Employee;
use App\Models\JobGroup;
use App\Models\LeaveType;
use App\Models\Promotion;
use App\Models\WorkShift;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\LeaveGroup;
use App\Models\Designation;
use App\Models\Termination;
use Illuminate\Support\Str;
use App\Models\HourlySalary;
use App\Models\TrainingInfo;
use Illuminate\Http\Request;
use App\Models\EmployeeAward;
use App\Models\EmployeeBonus;
use App\Models\EmployeeGroup;
use App\Models\FinancialYear;
use App\Models\PayoutChannel;
use App\Models\SalaryDetails;
use App\Models\StaffContract;
use App\Models\WeeklyHoliday;
use App\Charts\Attendancechart;
use App\Models\EmployeeSection;
use App\Models\LeaveApplication;
use App\Models\EmployeeDocuments;
use App\Models\LeaversAndJoiners;
use App\Models\EmployeeExperience;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Lib\Enumerations\LeaveStatus;
use App\Models\EmployeePayoutChannel;
use App\Repositories\LeaveRepository;
use App\Http\Requests\EmployeeRequest;
use App\Lib\Enumerations\GeneralStatus;
use App\Mail\Employee\NewEmployeeEmail;
use Illuminate\Database\QueryException;
use App\Repositories\EmployeeRepository;
use App\Notifications\EmployeeProfileUpdated;
use App\Models\EmployeeEducationQualification;
use App\Notifications\EmployeeDocumentUploaded;
use App\Notifications\EmployeeExperienceUpdated;
use App\Lib\Enumerations\EmployeeBiometricStatus;
use App\Lib\Enumerations\StaffContractTypes;
use App\Http\Requests\StoreEmployeeExperienceRequest;
use App\Http\Requests\StoreEmployeePayoutChannelRequest;
use App\Models\Payroll\DeductionType;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{


    protected $employeeRepositories;
    protected $leaveRepository;
    private $baseApiUrl, $token;

    public function __construct(EmployeeRepository $employeeRepositories,  LeaveRepository $leaveRepository)
    {
        $this->employeeRepositories = $employeeRepositories;
        $this->leaveRepository = $leaveRepository;
       // $this->baseApiUrl = config('app.BIOTIME_API_URL', 'https://102.37.21.7:8003'); // Default if not set in .env
       // $this->token = config('app.BIOTIME_API_TOKEN'); // Extract token
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $leaveTypes = LeaveType::where('status', 1)->get();
        $departmentList = Department::where('status', 1)->get();
        $designationList = Designation::where('status', 1)->get();
        $roleList = Role::get();
        $today = Carbon::today();
        $threeMonthsToCome = Carbon::today()->addMonth(3);

        $activeContracts =  StaffContract::whereDate('end_date', '>=', $today)->count();
        $expiringContracts =  StaffContract::whereDate('end_date', '<=', $threeMonthsToCome)->count(); // contracts expiring within the next 3 months.

        $staffByGender = Employee::selectRaw('gender, COUNT(*) as count')
            ->groupBy('gender')
            ->pluck('count', 'gender');

        $results = Employee::with([
            'userName' => function ($q) {
                $q->with('roles');
            },
            'department',
            'designation',
            'workLocation',
            'supervisor',
            'hourlySalaries',
            'company',
        ])->where('status', 1);


        if (request()->ajax()) {
            if ($request->role_id != '') {
                $results = Employee::whereHas('userName', function ($q) use ($request) {
                    $q->with('roles')->where('role_id', $request->role_id);
                })->where('status', 1)->with('department', 'designation', 'workLocation', 'supervisor', 'hourlySalaries', 'company')->orderBy('employee_id', 'DESC');
            } else {
                $results = Employee::with(['userName' => function ($q) use ($request) {
                    $q->with('roles')->where('role_id', $request->role_id);
                }, 'department', 'designation', 'workLocation', 'supervisor', 'hourlySalaries', 'company'])->where('status', 1)->orderBy('employee_id', 'DESC');
            }

            if ($request->department_id != '') {
                $results->where('department_id', $request->department_id);
            }

            if ($request->designation_id != '') {
                $results->where('designation_id', $request->designation_id);
            }

            if ($request->employee_name != '') {
                $results->where(function ($query) use ($request) {
                    $query->where('first_name', 'like', '%' . $request->employee_name . '%')
                        ->orWhere('middle_name', 'like', '%' . $request->employee_name . '%')
                        ->orWhere('last_name', 'like', '%' . $request->employee_name . '%');
                });
            }


            $results = $results->paginate(10000);
            return view('admin.employee.employee.pagination')->with(['signed_in_user_role' => $signed_in_user_role, 'results' => $results, 'activeContracts' => $activeContracts, 'expiredContracts' => $expiringContracts]);
        }


        $results = $results->orderBy('employee_id', 'DESC')->paginate(10000);

        return view('admin.employee.employee.index', [
            'signed_in_user_role' => $signed_in_user_role,
            'results' => $results,
            'departmentList' => $departmentList,
            'designationList' => $designationList,
            'roleList' => $roleList,
            'leaveTypes' => $leaveTypes,
            'activeContracts' => $activeContracts,
            'expiringContracts' => $expiringContracts,
            'genderCounts' => $staffByGender,
        ]);
    }

    public function inactive(Request $request)
    {
        $user = Auth::user();
        // if($request->get('location'))
        // {
        //     $branchId = $request->get('location');
        // }
        $branchId = $user->location;

        $this->authorize('viewAny', [Employee::class, $branchId]);

        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $leaveTypes = LeaveType::where('status', 1)->get();
        $departmentList = Department::where('status', 1)->get();
        $designationList = Designation::where('status', 1)->get();
        $roleList = Role::get();
        $today = Carbon::today();
        $threeMonthsToCome = Carbon::today()->addMonth(3);

        $activeContracts =  StaffContract::whereDate('end_date', '>=', $today)->count();
        $expiringContracts =  StaffContract::whereDate('end_date', '<=', $threeMonthsToCome)->count(); // contracts expiring within the next 3 months.

        $staffByGender = Employee::where('status', '!=', GeneralStatus::ACTIVE)->selectRaw('gender, COUNT(*) as count')
            ->groupBy('gender')
            ->pluck('count', 'gender');

        $results = Employee::with([
            'userName' => function ($q) {
                $q->with('roles');
            },
            'department',
            'designation',
            'workLocation',
            'supervisor',
            'hourlySalaries',
            'company',
        ])->where('status', '!=', GeneralStatus::ACTIVE);


        if (request()->ajax()) {
            if ($request->role_id != '') {
                $results = Employee::whereHas('userName', function ($q) use ($request) {
                    $q->with('roles')->where('role_id', $request->role_id);
                })->where('status', 1)->with('department', 'designation', 'workLocation', 'supervisor', 'hourlySalaries', 'company')->orderBy('employee_id', 'DESC');
            } else {
                $results = Employee::with(['userName' => function ($q) use ($request) {
                    $q->with('roles')->where('role_id', $request->role_id);
                }, 'department', 'designation', 'workLocation', 'supervisor', 'hourlySalaries', 'company'])->where('status', 1)->orderBy('employee_id', 'DESC');
            }

            if ($request->department_id != '') {
                $results->where('department_id', $request->department_id);
            }

            if ($request->designation_id != '') {
                $results->where('designation_id', $request->designation_id);
            }

            if ($request->employee_name != '') {
                $results->where(function ($query) use ($request) {
                    $query->where('first_name', 'like', '%' . $request->employee_name . '%')
                        ->orWhere('middle_name', 'like', '%' . $request->employee_name . '%')
                        ->orWhere('last_name', 'like', '%' . $request->employee_name . '%');
                });
            }


            $results = $results->paginate(10000);
            return view('admin.employee.employee.pagination')->with(['signed_in_user_role' => $signed_in_user_role, 'results' => $results, 'activeContracts' => $activeContracts, 'expiredContracts' => $expiringContracts]);
        }


        $results = $results->orderBy('employee_id', 'DESC')->paginate(10000);

        return view('admin.employee.employee.index_inactive', [
            'signed_in_user_role' => $signed_in_user_role,
            'results' => $results,
            'departmentList' => $departmentList,
            'designationList' => $designationList,
            'roleList' => $roleList,
            'leaveTypes' => $leaveTypes,
            'activeContracts' => $activeContracts,
            'expiringContracts' => $expiringContracts,
            'genderCounts' => $staffByGender,
        ]);
    }

    public function create()
    {
        $userList = User::get();
        $roleList = Role::get();
        $companyList = \App\Models\Company::where('status', 1)->get();

        $departmentList = Department::where('status', 1)->get();
        $designationList = Designation::where('status', 1)->get();
        $locationList = Location::where('status', 1)->get();
        $workShiftList = WorkShift::where('status', 1)->get();
        $supervisorList = Employee::where('status', 1)->get();

        $hourlySalaryList = HourlySalary::where('status', 1)->get();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $leaveGroups = LeaveGroup::where('is_active', 1)->get();
        //
        $sectionList = EmployeeSection::where('status', 1)->get();
        $employeeGroupList = EmployeeGroup::where('status', 1)->get();
        $employeeShifts = [];

        // Generate next payroll number
        $nextPayrollNumber = $this->employeeRepositories->generatePayrollNumber();

        $data = [
            'userList' => $userList,
            'roleList' => $roleList,
            'companyList' => $companyList,
            'departmentList' => $departmentList,
            'designationList' => $designationList,
            'locationList' => $locationList,
            'supervisorList' => $supervisorList,
            'workShiftList' => $workShiftList,
            'hourlySalaryList' => $hourlySalaryList,
            'signed_in_user_role' => $signed_in_user_role,
            'leaveGroupList' => $leaveGroups,
            'employeeShifts' => $employeeShifts,
            'employeeGroupList' => $employeeGroupList,
            'sectionList' => $sectionList,
            'nextPayrollNumber' => $nextPayrollNumber,
        ];

        return view('admin.employee.employee.addEmployee', $data);
    }


    public function store(EmployeeRequest $request)
    {
        $photo = $request->file('photo');
        if ($photo) {
            $imgName = md5(Str::random(30) . time() . '_' . $request->file('photo')) . '.' . $request->file('photo')->getClientOriginalExtension();
            $request->file('photo')->move('uploads/employeePhoto/', $imgName);
            $employeePhoto['photo'] = $imgName;
        }

        $employeeDataFormat = $this->employeeRepositories->makeEmployeePersonalInformationDataFormat($request->all());

        if (isset($employeePhoto)) {
            $employeeData = $employeeDataFormat + $employeePhoto;
        } else {
            $employeeData = $employeeDataFormat;
        }


        $employee_email = $request->email;
        $password = $request->password;
        $mailContent = ([
            'username' => $request->user_name,
            'password' => $password,
        ]);

        try {
            Mail::to($employee_email)->send(new NewEmployeeEmail($mailContent));
        } catch (\Exception $e) {
            Log::info($e->getMessage() . 'Email sending failed!');
        }

        try {
            DB::beginTransaction();

            $employeeAccountDataFormat = $this->employeeRepositories->makeEmployeeAccountDataFormat($request->all());

            $parentData = User::create($employeeAccountDataFormat);
            $employeeData['id'] = $parentData->id;
            $employeeData['user_id'] = $parentData->id;
            $childData = Employee::create($employeeData);

            //Create roles for the user here
            $user1 = User::where('id', $parentData->id)->first();
            if ($request->roles) {

                $roles = Role::whereIn('id', $request->roles)->pluck('name')->toArray();
                $user1->syncRoles($roles);
            } else {
                //do nothing
            }

            // Attach 
            $leaveGroup = LeaveGroup::where('id', $request->leave_group_id)->first();
            if ($leaveGroup) {
                $leaveGroup->employees()->syncWithoutDetaching([$childData->employee_id]);
            }



            //            $roles = Role::whereIn('id', $request->roles)->pluck('name');
            //            $childData->syncRoles($roles);

            //  $childData->workShifts()->attach($request->workShifts);
            $employeeEducationData = $this->employeeRepositories->makeEmployeeEducationDataFormat($request->all(), $childData->employee_id);
            //upload employee data here
            $employeeDocuments = $this->employeeRepositories->makeEmployeeDocumentsDataFormat($request->all(), $childData, 'update');

            if (count($employeeEducationData) > 0) {
                EmployeeEducationQualification::insert($employeeEducationData);
            }

            $employeeExperienceData = $this->employeeRepositories->makeEmployeeExperienceDataFormat($request->all(), $childData->employee_id);
            if (count($employeeExperienceData) > 0) {
                EmployeeExperience::insert($employeeExperienceData);
            }

            //save documents details
            if (count($employeeDocuments) > 0) {
                foreach ($employeeDocuments as $employeeDocument) {
                    EmployeeDocuments::create($employeeDocument);
                }
            }

            // Create joiner record for new employee
            LeaversAndJoiners::create([
                'employee_id' => $childData->employee_id,
                'payroll_number' => $childData->payroll_number,
                'national_id' => $childData->national_id,
                'first_name' => $childData->first_name,
                'middle_name' => $childData->middle_name,
                'last_name' => $childData->last_name,
                'date_of_movement' => $childData->date_of_joining,
                'date_approved' => now(),
                'movement_type' => 'joining',
                'approval_status' => 1,
                'reason' => 'New employee joining',
                'created_by' => Auth::user()->id,
            ]);

            // Automatically create a staff contract for the new employee
            if ($childData->date_of_joining) {
                $dateOfJoining = Carbon::parse($childData->date_of_joining);
                $probationEndDate = $dateOfJoining->copy()->addMonths(6);

                StaffContract::create([
                    'employee_id' => $childData->employee_id,
                    'hire_date' => $childData->date_of_joining,
                    'probation_start_date' => $childData->date_of_joining,
                    'probation_end_date' => $probationEndDate->format('Y-m-d'),
                    'start_date' => $childData->date_of_joining,
                    'end_date' => null, // No end date for new contracts
                    'contract_type' => StaffContractTypes::FIXED,
                    'status' => 1,
                ]);
            }

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            Log::error('Employee creation failed: ' . $e->getMessage());
        }

        if ($bug == 0) {
            return redirect()->route('employee.index')->with('success', 'Employee information successfully saved.');
        } else {
            return redirect()->back()->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $userList = User::get();
        $roleList = Role::get();
        $companyList = \App\Models\Company::where('status', 1)->get();
        $editModeData = Employee::with('employeeGroup', 'employeeSection', 'workShifts')->findOrFail($id);

        $userData = User::where('id', $editModeData->user_id)->with('roles')->first();
        $userRoles = $userData->roles()->pluck('id')->toArray();

        $departmentList = Department::where('status', 1)->get();
        $designationList = Designation::where('status', 1)->get();
        $locationList = Location::where('status', 1)->get();
        $supervisorList = Employee::where('status', 1)->get();
        $workShiftList = WorkShift::where('status', 1)->get();
        $hourlySalaryList = HourlySalary::where('status', 1)->get();
        $employeeAccountEditModeData = User::where('id', $editModeData->user_id)->first();
        $educationQualificationEditModeData = EmployeeEducationQualification::where('employee_id', $id)->get();
        $experienceEditModeData = EmployeeExperience::where('employee_id', $id)->get();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        $leaveGroups = LeaveGroup::where('is_active', 1)->get();

        $sectionList = EmployeeSection::where('status', 1)->get();
        $employeeGroupList = EmployeeGroup::where('status', 1)->get();

        $employeeShifts = Employee::with('workShifts')->where('employee_id', $id)->first();
        $employeeShiftsArray = $employeeShifts->workShifts()->pluck('work_shift')->toArray();

        $data = [
            'userList' => $userList,
            'roleList' => $roleList,
            'companyList' => $companyList,
            'departmentList' => $departmentList,
            'designationList' => $designationList,
            'locationList' => $locationList,
            'supervisorList' => $supervisorList,
            'workShiftList' => $workShiftList,
            'editModeData' => $editModeData,
            'hourlySalaryList' => $hourlySalaryList,
            'employeeAccountEditModeData' => $employeeAccountEditModeData,
            'educationQualificationEditModeData' => $educationQualificationEditModeData,
            'experienceEditModeData' => $experienceEditModeData,
            'signed_in_user_role' => $signed_in_user_role,
            'leaveGroupList' => $leaveGroups,
            'employeeShifts' => $employeeShiftsArray,
            'sectionList' => $sectionList,
            'employeeGroupList' => $employeeGroupList,
            'userRoles' => $userRoles
        ];


        return view('admin.employee.employee.editEmployee', $data);
    }

    public function update(EmployeeRequest $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $photo = $request->file('photo');
        if ($photo) {
            // Generate unique file name
            $imgName = md5(Str::random(30) . time() . '_' . $request->file('photo')) . '.' . $request->file('photo')->getClientOriginalExtension();

            // Move uploaded photo to the designated folder
            $photo->move('uploads/employeePhoto/', $imgName);
            // $request->file('photo')->move('uploads/employeePhoto/', $imgName);

            // Remove old photo if it exists
            if (!empty($employee->photo) && file_exists('uploads/employeePhoto/' . $employee->photo)) {
                unlink('uploads/employeePhoto/' . $employee->photo);
            }

            // Assign new photo to the employee data array
            $employeePhoto['photo'] = $imgName;
        }
        $employeeDocuments = $this->employeeRepositories->makeEmployeeDocumentsDataFormat($request->all(), $employee, 'update');
        $employeeDataFormat = $this->employeeRepositories->makeEmployeePersonalInformationDataFormat($request->all());

        if (isset($employeePhoto)) {
            $employeeData = $employeeDataFormat + $employeePhoto;
        } else {
            $employeeData = $employeeDataFormat;
        }
        $employee->workShifts()->sync($request->workShifts);
        //update roles here

        $user1 = User::where('id', $employee->user_id)->first();
        $roles = Role::whereIn('id', $request->roles)->pluck('name');

        $user1->syncRoles($roles);
        $employeeAccountDataFormat = $this->employeeRepositories->makeEmployeeAccountDataFormat($request->all(), 'update');

        try {
            DB::beginTransaction();

            // Update User data including company_id
            User::where('id', $employee->user_id)->update($employeeAccountDataFormat);

            // Also update company_id specifically on user table
            if ($request->has('company_id') && !empty($request->company_id)) {
                User::where('id', $employee->user_id)->update(['company_id' => $request->company_id]);
            }

            // Update Personal Information
            $employee->update($employeeData);

            // Attach Leave Group 
            $leaveGroup = LeaveGroup::where('id', $request->leave_group_id)->first();
            if ($leaveGroup) {
                // First remove any existing assignments for this employee
                DB::table('employee_leavegroups')
                    ->where('employee_id', $employee->employee_id)
                    ->delete();

                // Then insert the new assignment
                DB::table('employee_leavegroups')->insert([
                    'leave_group_id' => $leaveGroup->id,
                    'employee_id' => $employee->employee_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }



            // Delete education qualification
            EmployeeEducationQualification::whereIn('employee_education_qualification_id', explode(',', $request->delete_education_qualifications_cid))->delete();

            // Update Education Qualification
            $employeeEducationData = $this->employeeRepositories->makeEmployeeEducationDataFormat($request->all(), $id, 'update');
            foreach ($employeeEducationData as $educationValue) {
                $cid = $educationValue['educationQualification_cid'];
                unset($educationValue['educationQualification_cid']);
                if ($cid != "") {
                    EmployeeEducationQualification::where('employee_education_qualification_id', $cid)->update($educationValue);
                } else {
                    $educationValue['employee_id'] = $id;
                    EmployeeEducationQualification::create($educationValue);
                }
            }

            // Delete experience
            EmployeeExperience::whereIn('employee_experience_id', explode(',', $request->delete_experiences_cid))->delete();

            // Update Education Qualification
            $employeeExperienceData = $this->employeeRepositories->makeEmployeeExperienceDataFormat($request->all(), $id, 'update');
            if (count($employeeExperienceData) > 0) {
                foreach ($employeeExperienceData as $experienceValue) {
                    $cid = $experienceValue['employeeExperience_cid'];
                    unset($experienceValue['employeeExperience_cid']);
                    if ($cid != "") {
                        EmployeeExperience::where('employee_experience_id', $cid)->update($experienceValue);
                    } else {
                        $experienceValue['employee_id'] = $id;
                        EmployeeExperience::create($experienceValue);
                    }
                }
            }

            //save documents details
            if (count($employeeDocuments) > 0) {
                foreach ($employeeDocuments as $employeeDocument) {

                    EmployeeDocuments::create($employeeDocument);
                }
            }

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            Log::info($e->getMessage());
        }

        if ($bug == 0) {
            return redirect()->route('employee.show', $employee->employee_id)->with('success', 'Employee information successfully updated.');
        } else {
            return redirect()->back()->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }


    public function show($id)
    {

        $currentDate          = now();
        $currentFinancialYear = FinancialYear::where('start_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate)
            ->first();
        $fiscal_start_date = $currentFinancialYear->start_date;
        $fiscal_end_date = $currentFinancialYear->end_date;

        $employeeInfo = Employee::where('employee.employee_id', $id)
            ->with(['workLocation', 'case', 'employeeSection', 'employeeGroup', 'employeeType', 'workShifts', 'employeeDocuments', 'contractDetails', 'projectAllocations.project', 'payrollEarnings', 'company', 'department', 'designation', 'supervisor'])
            ->first();



        $employeeExperience = EmployeeExperience::where('employee_id', $id)->get();
        $employeeEducation = EmployeeEducationQualification::where('employee_id', $id)->get();
        $supervisor = Employee::where('employee.employee_id', $employeeInfo->supervisor_id)->first();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $leaveTypes = $employeeInfo->applicableLeaveTypes();

        $annualLeaveDays = LeaveType::where('leave_type_id', 2)
            ->pluck('num_of_day')
            ->first();
        $rollover_leaves = LeaveRollover::where('employee_id', $id)
            ->where('final_status', LeaveStatus::APPROVE)
            ->where('financial_year_id', $currentFinancialYear->id)
            //->pluck('days_requested')
            ->get();

        $approvedLeaves = LeaveApplication::where('employee_id', $id)
            ->where('final_status', 2)
            ->where('leave_type_id', 2)
            ->whereBetween('approve_date', [date($fiscal_start_date), date($fiscal_end_date)])
            ->sum('number_of_day');

        //        $totalLeavesForTheYear = ($rollover_leaves['days_requested'] + $annualLeaveDays) - $approvedLeaves;
        $totalLeavesForTheYear = 0;

        if ($rollover_leaves->count() == 0) {
            $totalLeavesForTheYear = $annualLeaveDays - $approvedLeaves;
        } else {

            $totalLeavesForTheYear = ($rollover_leaves[0]['days_requested'] + $annualLeaveDays) - $approvedLeaves;
        }
        $payourChannels = PayoutChannel::all();
        $programs = Project::all();

        $overtimeDays = Attendance::where('employee_id', $id)->whereMonth('date', Carbon::now()->month)->distinct('date')->count();
        $overtimeAmount = $overtimeDays * 500;

        // DB::table('employee_earnings_and_deductions')->updateOrInsert(
        //     ['employee_id' => $id, 'name' => 'Overtime Days', 'type' => 1],
        //     ['amount' => $overtimeAmount, 'updated_at' => now()]
        // );

        //$employeeEarnings = DB::table('employee_earnings_and_deductions')->where('employee_id', $id)->where('type', 1)->get();
        $employeeDeductions = \App\Models\EmployeeDeductions::with('payrollDeductionType')
            ->where('employee_id', $id)
            ->where('status', 1) // Only approved deductions
            ->get();
        $allDeductionTypes = DeductionType::get();

        $leaveTyesData = [];
        foreach ($leaveTypes as $leaveType) {
            $leaveTye = []; // Initialize array for each leave type

            // Check if employee has more than one leave group
            if ($employeeInfo->leaveGroup()->count() > 1) {
                $leaveTye['name'] =  'N/A';
                $leaveTye['days_entitled'] = '-';
                $leaveTye['leave_type_id'] = $leaveType->leave_type_id;
                $leaveTye['totalDays'] = '-';
                $leaveTye['days_used'] = '-';
                $leaveTye['roll_over_days'] = '-';
                $leaveTye['totalBlance'] = '-';
                $leaveTyesData[] = $leaveTye;
                continue; // Skip to next iteration
            }

            // Normal processing for employees with 0 or 1 leave group
            $leaveUsed = LeaveApplication::where('employee_id', $id)
                ->where('final_status', 2)
                ->where('leave_type_id', $leaveType->leave_type_id)
                ->whereBetween('approve_date', [date($fiscal_start_date), date($fiscal_end_date)])
                ->sum('number_of_day');

            $totalDays = $employeeInfo->getEarnedLeaveDays($leaveType->leave_type_id);
            $rolloverDays = LeaveRollover::where('employee_id', $id)
                ->where('final_status', LeaveStatus::APPROVE)
                ->where('financial_year_id', $currentFinancialYear->id)
                ->where('leave_type_id', $leaveType->leave_type_id)
                ->value('days_requested') ?? 0;

            // Safely get annual entitlement with null check
            $annualEntitlement = 0;
            if ($employeeInfo->leaveGroup) {
                $setting = $employeeInfo->leaveGroup->settings()
                    ->where('leave_type_id', $leaveType->leave_type_id)
                    ->first();
                $annualEntitlement = $setting ? $setting->annual_entitlement : 0;
            }

            $leaveTye['name'] = $leaveType->leave_type_name;
            $leaveTye['days_entitled'] = $annualEntitlement;
            $leaveTye['leave_type_id'] = $leaveType->leave_type_id;
            $leaveTye['totalDays'] = $totalDays;
            $leaveTye['days_used'] = $leaveUsed;
            $leaveTye['roll_over_days'] = $rolloverDays;
            $leaveTye['totalBlance'] = ($totalDays + $rolloverDays) - $leaveUsed;
            $leaveTyesData[] = $leaveTye;
        }
        return view('admin.employee.employee.view-profile', [
            'signed_in_user_role' => $signed_in_user_role,
            'employeeInfo' => $employeeInfo,
            'employeeExperience' => $employeeExperience,
            'employeeEducation' => $employeeEducation,
            'supervisor_info' => $supervisor,
            'rollover_leaves' => $rollover_leaves,
            'leaveTypes' => $leaveTypes,
            'totalLeavesForTheYear' => $totalLeavesForTheYear,
            'payourChannels' => $payourChannels,
            'leaveTyesData' => $leaveTyesData,
            'programs' => $programs,
            //'employeeEarnings' => $employeeEarnings,
            'employeeDeductions' => $employeeDeductions,
            'allDeductionTypes' => $allDeductionTypes,
            'overtimeAmount' => $overtimeAmount,
        ]);
    }

    public function getShift($shifts, $checkinTime)
    {

        $checkInTime = $checkinTime;
        $checkInDate = Carbon::parse($checkinTime)->format('Y-m-d');
        $checkInDate1 = Carbon::createFromFormat('Y-m-d', $checkInDate)->addDays(1);

        $currentWorkShift['workShift'] = '';

        foreach ($shifts as $workShift) {
            $shiftStart = Carbon::parse($workShift->start_time)->format($checkInDate . ' H:i:s');
            $shiftEnd = Carbon::parse($workShift->end_time)->format($checkInDate . ' H:i:s');

            if ($checkinTime > $shiftStart && $checkinTime < $shiftEnd) {
                $currentWorkShift['workShift'] = $workShift;
                $currentWorkShift['shift_start'] = $shiftStart;
                $currentWorkShift['shift_end'] = $shiftEnd;
                return $currentWorkShift;
            }

            if ($shiftEnd < $shiftStart) {
                $shiftEnd1 = Carbon::createFromFormat('Y-m-d', $checkInDate)->addDays(1);
                $shiftEndDate = $shiftEnd1->format('Y-m-d');

                $shiftEndTime = Carbon::parse($workShift->end_time)->format($shiftEndDate . ' H:i:s');
                $shiftEnd = $shiftEndTime;
            }

            if ($shiftStart < $checkinTime && $shiftEnd > $checkinTime) {
                $currentWorkShift['workShift'] = $workShift;
                $currentWorkShift['shift_start'] = $shiftStart;
                $currentWorkShift['shift_end'] = $shiftEnd;
                return ($currentWorkShift);
            }
        }

        return $currentWorkShift;
    }

    public function destroy($id)
    {

        try {
            DB::beginTransaction();
            $data = Employee::withTrashed()->FindOrFail($id);

            if (!is_null($data->photo)) {
                if (file_exists('uploads/employeePhoto/' . $data->photo) and !empty($data->photo)) {
                    unlink('uploads/employeePhoto/' . $data->photo);
                }
            }
            $result = $data->forceDelete();
            if ($result) {
                User::withTrashed()->FindOrFail($data->user_id)->forceDelete();
                EmployeeEducationQualification::where('employee_id', $data->employee_id)->forceDelete();
                EmployeeExperience::where('employee_id', $data->employee_id)->forceDelete();
                Attendance::where('employee_id', $data->employee_id)->forceDelete();
                EmployeeAward::where('employee_id', $data->employee_id)->forceDelete();
                EmployeeBonus::where('employee_id', $data->employee_id)->forceDelete();
                Promotion::where('employee_id', $data->employee_id)->forceDelete();
                SalaryDetails::where('employee_id', $data->employee_id)->forceDelete();
                TrainingInfo::where('employee_id', $data->employee_id)->forceDelete();
                Warning::where('warning_to', $data->employee_id)->forceDelete();
                LeaveApplication::where('employee_id', $data->employee_id)->forceDelete();
                Termination::where('terminate_to', $data->employee_id)->forceDelete();
                Attendance::where('payroll_number', $data->payroll_number)->forceDelete();
            }
            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            \Log::info($e->getMessage());
        }

        if ($bug == 0) {
            echo "success";
        } elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }

    public function getEmployeeList()
    {
        $results = Employee::where('status', '=', '1')->with([
            'department',
            'designation',
            'workLocation',
            'supervisor',
            'hourlySalaries'])
            ->orderBy('employee_id', 'DESC')->get();

        return view('admin.employee.employee.report.userReport', ['results' => $results]);
    }

    public function getShifts()
    {
        $shifts = Employee::with('workShifts')->where('employee_id', 1)->first();
        $year = 2021;
        // $startTimeStamp = Carbon::create()->format('2023'.'-'.'04'.'-'.'09'.' '.$shifts->workShifts->first()['start_time']);
        $startTimeStamp = Carbon::parse($shifts->workShifts->first()['start_time'])->addMinutes(180)->format($year . '-' . '04' . '-' . '09' . ' ' . 'H:i:s');
        $endTimeStamp = Carbon::create()->format('2023' . '-' . '04' . '-' . '09' . ' ' . $shifts->workShifts->first()['end_time']);

        $threeHourBefore = Carbon::create(2023, 04, '09', 07, 30, 00)->format('Y-m-d H:i:s');
        $threeHourAfter = Carbon::create(2023, 04, '09', 21, 19, 53)->addMinutes(180)->format('Y-m-d H:i:s');

        $attendance1 = Attendance::whereBetween('time_in', [$threeHourBefore, $threeHourAfter])->get();
        dd($threeHourBefore, $startTimeStamp);
    }

    public function charts()
    {
        $api = url('/employeeManagement/chart-line-ajax');

        $chart = new Attendancechart();
        $chart->labels(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'])->load($api);
        return view('admin.employee.chart', compact('chart'));
    }

    public function chartLineAjax(Request $request)
    {
        $year = $request->has('year') ? $request->year : date('Y');
        $users = User::select(\DB::raw("COUNT(*) as count"))
            ->whereYear('created_at', $year)
            ->groupBy(\DB::raw("Month(created_at)"))
            ->pluck('count');
        $chart = new Attendancechart();

        $chart->dataset('New User Register Chart', 'line', $users)->options([
            'fill' => 'true',
            'borderColor' => '#51C1C0'
        ]);

        return $chart->api();
    }

    public function userReportDownload()
    {
        $results = Employee::where('status', '=', 1)->with(['department', 'workLocation'])
            ->orderBy('employee_id', 'DESC')->get();

        $pdf = Pdf::loadView('admin.employee.employee.report.downloadReport', ['results' => $results]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("UserReport.pdf");
        // return view('admin.employee.employee.userReport',[ 'results' => $results]);

    }

    public function disable($id)
    {
        $employee = Employee::withTrashed()->FindOrFail($id);
        $user = User::withTrashed()->where('user_id', $employee->user_id);
        try {
            $results = $employee->update([
                'status' => 0,
                'updated_at' => now()
            ]);
            $results = $user->update([
                'status' => 0,
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            // DB::rollback();
            $bug = $e->getMessage();
            \Log::info($e->getMessage());
        }
        return 'success';
    }

    public function updateBiometricCaptureStatus($id)
    {
        $employee = Employee::withTrashed()->FindOrFail($id);

        $response = Http::withOptions([
            'verify' => false, // Disable SSL verification
        ])->withHeaders(
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Token  ' . $this->token,
            ]
        )->get(
            $this->baseApiUrl . '/personnel/api/employees/',
            [
                'page_size' => 10000,
            ]
        );


        if ($response->successful()) {
            $responseData = $response->json();
            $employees = $responseData['data'] ?? $responseData;
            $biometricCaptureStatus = 0;

            $foundEmployee = null;
            foreach ($employees as $employeeData) {
                if (isset($employeeData['emp_code']) && $employeeData['emp_code'] == $employee->national_id) {
                    $foundEmployee = $employeeData;
                    $biometricCaptureStatus = 0;


                    if (
                        $employeeData['fingerprint'] !== "-" ||
                        $employeeData['palm'] !== "-" ||
                        $employeeData['face'] !== "-"
                    ) {
                        $biometricCaptureStatus = 1;
                    }

                    $results = $employee->update([
                        'biometric_user_id' => $employeeData['id'],
                        'biometric_capture_status' => $biometricCaptureStatus,
                        'biometric_upload_status' => EmployeeBiometricStatus::UPLOADED,
                        'updated_at' => now()
                    ]);


                    break;
                }
            }

            // If you're using Laravel's collections (recommended)
            // $foundEmployee = collect($employees)->firstWhere('emp_code', $employee->national_id);

        }

        return redirect()->back()->with('success', 'Biometric capture status updated successfully.');
    }
    public function enable($id)
    {
        $employee = Employee::withTrashed()->FindOrFail($id);
        $user = User::withTrashed()->FindOrFail($employee->user_id);
        try {
            $results = $employee->update([
                'status' => 1,
                'deleted_at' => null,
                'updated_at' => now()
            ]);
            $results1 = $user->update([
                'status' => 1,
                'deleted_at' => null,
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            // DB::rollback();
            $bug = $e->getMessage();
            \Log::info($e->getMessage());
        }
        return 'success';
    }

    public function restore($id)
    {

        $employee = Employee::withTrashed()->FindOrFail($id);
        $user = User::withTrashed()->FindOrFail($employee->user_id);
        try {
            $employee->restore();
            $user->restore();
            $results = $employee->update([
                'status' => 1,
            ]);

            $results = $user->update([
                'status' => 1,

            ]);
        } catch (\Exception $e) {
            // DB::rollback();
            $bug = $e->getMessage();
            \Log::info($e->getMessage());
        }
        return 'success';
    }

    public function delete($id)
    {
        $employee = Employee::withTrashed()->FindOrFail($id);
        $user = User::withTrashed()->FindOrFail($employee->user_id);
        try {
            $employee->delete();
            $user->delete();
            $results = $employee->update([
                'status' => 0,
            ]);

            $results = $user->update([
                'status' => 0,

            ]);
        } catch (\Exception $e) {
            // DB::rollback();
            $bug = $e->getMessage();
            \Log::info($e->getMessage());
        }
        return 'success';
    }

    public function storeOrUpdatePayoutChannel(StoreEmployeePayoutChannelRequest $request, $userId)
    {
        $employee = Employee::findOrFail($userId);

        $payoutChannelData = [
            'employee_id' => $employee->employee_id,
            'payout_channel_id' => $request->input('payout_channel_id'),
            'location' => $employee->location,
            'account_number' => $request->input('account_number'),
            'location' => $request->input('location'),
            'branch_code' => $request->input('branch_code'),
            'swift_code' => $request->input('swift_code')
        ];

        $employeePayoutChannel =  $employee->employeePayoutChannel()->first();

        try {
            if ($employeePayoutChannel) {
                $employeePayoutChannel->delete();
            }

            EmployeePayoutChannel::create($payoutChannelData);

            return redirect()->back()->with('success', 'Payout channel linked successfully!');
        } catch (Exception $e) {

            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function deleteFromStaff($id)
    {

        Log::info($id);
        try {
            $data = DB::table('employee_payout_channels')->where('id', $id)->first();
            if ($data) {
                DB::table('employee_payout_channels')->where('id', $id)->delete();
                $bug = 0;
            } else {
                $bug = "Record not found";
            }
        } catch (QueryException $e) {
            $bug = $e->getMessage();
            Log::error($e);
        }

        if ($bug == 0) {
            echo "success";
        } elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }

    public function storeProffessionalExperience(StoreEmployeeExperienceRequest $request, Employee $employee)
    {
        $data = $request->except("_token");

        $experienceData = [
            'employee_id' => $employee->employee_id,
            'organization_name' => $data['organization_name'],
            'designation' => $data['designation'],
            'from_date' => $data['from_date'],
            'to_date' => $data['to_date'],
            'skill' => $data['skill'],
            'responsibility' => $data['responsibility'],
            'location' => $employee->location,
        ];

        EmployeeExperience::create($experienceData);

        // Notify approvers
        $this->notifyExperienceApprovers($employee, $experienceData);

        return response()->json([
            'status' => true,
            'message' => 'Successfully added a new experience'
        ]);
    }

    protected function notifyExperienceApprovers(Employee $employee, array $experienceData)
    {
        $approvers = collect();

        // 1. Get HR admins with experience approval rights
        $hrAdmins = Employee::whereHas('user.roles', function ($query) {
            $query->where('name', 'HR Administrator');
        })->get();

        // 2. Get direct supervisor if exists
        if ($employee->supervisor_id) {
            $supervisor = Employee::where('employee_id', $employee->supervisor_id)
                ->whereHas('user.roles', function ($query) {
                    $query->where('name', 'Supervisor');
                })
                ->first();

            if ($supervisor) {
                $approvers->push($supervisor);
            }
        }

        // Combine and remove duplicates
        $approvers = $approvers->merge($hrAdmins)->unique('employee_id');

        // Send notifications
        foreach ($approvers as $approver) {
            if ($approver->user) {
                $approver->user->notify(new EmployeeExperienceUpdated($employee, $experienceData));
            }
        }
    }

    public function storeEmployeeDocument(Request $request, Employee $employee)
    {
        $request->validate([
            'document_name' => 'required|array',
            'document_name.*' => 'required|string',
            'document_type' => 'required|array',
            'document_type.*' => 'required|string',
            'document_file' => 'required|array',
            'document_file.*' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $employeeDocuments = $this->employeeRepositories->makeEmployeeDocumentsDataFormat($request->all(), $employee, 'update');

        //save documents details
        if (count($employeeDocuments) > 0) {
            foreach ($employeeDocuments as $employeeDocument) {
                EmployeeDocuments::create($employeeDocument);
            }
        }

        // Notify approvers
        $this->notifyDocumentApprovers($employee, $employeeDocuments);

        return response()->json([
            'status' => true,
            'message' => 'Document successfully uploaded'
        ]);
    }

    protected function notifyDocumentApprovers(Employee $employee, array $employeeDocuments)
    {
        // Extract just the document names from the employeeDocuments array
        $documentNames = array_column($employeeDocuments, 'document_name');

        // Get all HR admins
        $hrAdmins = Employee::whereHas('user.roles', fn($q) => $q->where('name', 'HR Administrator'))
            ->get();

        // Get all compliance officers
        $complianceOfficers = Employee::whereHas('user.roles', fn($q) => $q->where('name', 'Compliance Officer'))
            ->get();

        // Get direct supervisor if exists
        $supervisor = $employee->supervisor_id
            ? Employee::find($employee->supervisor_id)
            : null;

        // Combine all approvers, remove duplicates, and filter null values
        $approvers = collect()
            ->merge($hrAdmins)
            ->merge($complianceOfficers)
            ->when($supervisor, fn($c) => $c->push($supervisor))
            ->unique('employee_id')
            ->filter();

        // Send notifications to users who exist
        $approvers->each(function ($approver) use ($employee, $documentNames) {
            optional($approver->user)->notify(
                new EmployeeDocumentUploaded($employee, $documentNames)
            );
        });
    }

    public function editProfile()
    {
        $login_employee = employeeInfo();
        $employeeInfo = null;
        if ($login_employee) {
            $employeeInfo = Employee::where('employee.employee_id', session('logged_session_data.employee_id'))->first();
        }
        $departmentList = Department::where('status', 1)->get();
        $supervisorList = Employee::where('status', 1)->get();
        $designationList = Designation::where('status', 1)->get();
        $locationList = Location::where('status', 1)->get();
        $workShiftList = WorkShift::where('status', 1)->get();
        $employeeGroupList = EmployeeGroup::where('status', 1)->get();
        return view('admin.user.user.edit_profile', [
            'employeeInfo' => $employeeInfo,
            'departmentList' => $departmentList,
            'supervisorList' => $supervisorList,
            'designationList' => $designationList,
            'locationList' => $locationList,
            'workShiftList' => $workShiftList,
            'employeeGroupList' => $employeeGroupList,
        ]);
    }

    public function updateProfile(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $photo = $request->file('photo');
        if ($photo) {
            // Generate unique file name
            $imgName = md5(Str::random(30) . time() . '_' . $request->file('photo')) . '.' . $request->file('photo')->getClientOriginalExtension();

            // Move uploaded photo to the designated folder
            $photo->move('uploads/employeePhoto/', $imgName);
            // $request->file('photo')->move('uploads/employeePhoto/', $imgName);

            // Remove old photo if it exists
            if (!empty($employee->photo) && file_exists('uploads/employeePhoto/' . $employee->photo)) {
                unlink('uploads/employeePhoto/' . $employee->photo);
            }

            // Assign new photo to the employee data array
            $employeePhoto['photo'] = $imgName;
        }

        $employeeDataFormat = $this->employeeRepositories->updatePersonalInformationData($request->all());
        if (isset($employeePhoto)) {
            $employeeData = $employeeDataFormat + $employeePhoto;
        } else {
            $employeeData = $employeeDataFormat;
        }
        $user = User::findOrFail(Auth::user()->id);

        try {
            DB::beginTransaction();
            $employee->update($employeeData);
            $user->update($employeeData);
            // Update Personal Information

            // Get approvers and notify them
            $approvers = $this->getProfileApprovers($employee);
            $updater = Employee::where('user_id', Auth::id())->first();

            foreach ($approvers as $approver) {
                $approver->user->notify(new EmployeeProfileUpdated($employee, $updater));
            }

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->getMessage();
            Log::info($e->getMessage());
        }

        if ($bug == 0) {
            return redirect()->route('home.profile')->with('success', 'Employee information successfully updated.');
        } else {
            return redirect()->back()->with('error', 'Some Error Found !, Please try again.');
        }
    }

    /**
     * Get all approvers who should be notified about profile changes
     */
    protected function getProfileApprovers(Employee $employee)
    {
        // Get HR administrators first
        $hrAdmins = Employee::whereHas('user.roles', function ($query) {
            $query->whereIn('name', ['HR Administrator']);
        })->get();

        // Get the employee's direct supervisor if exists
        $supervisors = collect();
        if ($employee->supervisor_id) {
            $supervisors = Employee::where('employee_id', $employee->supervisor_id)->get();
        }

        // Get location/regional approvers if needed
        $branchApprovers = collect();
        if ($employee->location) {
            $branchApprovers = Employee::whereHas('user.roles', function ($query) {
                $query->whereIn('name', ['Location Manager', 'Regional Manager']);
            })
                ->where('location', $employee->location)
                ->get();
        }

        // Combine all approvers and remove duplicates
        return $hrAdmins->merge($supervisors)->merge($branchApprovers)->unique('employee_id');
    }

    public function masterRoll(Request $request)
    {
        $query = Employee::with([
            'userName',
            'department',
            'designation',
            'workLocation',
            'supervisor',
            'hourlySalaries',
            'employeeType',
            'employeeSection',
            'employeeGroup',
            'workShift',
            'contractDetails',
            'payoutChannel',
            'employeePayroll',
            'terminations',
        ]);

        // Filter by status (active/inactive)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('status', 1);
            } elseif ($request->status === 'inactive') {
                $query->where('status', '!=', 1);
            }
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by section
        if ($request->filled('section_id')) {
            $query->where('employee_section_id', $request->section_id);
        }

        // Filter by date range of joining
        if ($request->filled('date_from')) {
            $query->whereDate('date_of_joining', '>=', dateConvertFormtoDB($request->date_from));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date_of_joining', '<=', dateConvertFormtoDB($request->date_to));
        }

        $employees = $query->paginate(100)->appends($request->all());

        // Get departments and sections for filter dropdowns
        $departments = Department::orderBy('department_name')->get();
        $sections = EmployeeSection::orderBy('name')->get();

        return view('admin.employee.employee.report.masterRoll', compact('employees', 'departments', 'sections'));
    }

    public function updateEarningsAndBenefits(Request $request, $employeeId)
    {
        try {
            $employee = Employee::findOrFail($employeeId);

            $earningsFields = [
                'basic_salary',
                'allowances',
                'other_benefits',
                'overtime_days',
                'overtime_permanent',
                'overtime_casuals',
                'overtime_volunteers',
                'notice_pay',
                'teacher_allowances',
                'bonus',
            ];

            foreach ($earningsFields as $field) {
                $dbFieldName = str_replace('_', ' ', ucwords($field, '_'));
                $amount = $request->input($field);

                // If the field is not present in the request (checkbox unchecked), set amount to 0
                if (!isset($amount)) {
                    $amount = 0;
                }

                DB::table('employee_earnings_and_deductions')->updateOrInsert(
                    ['employee_id' => $employee->employee_id, 'name' => $dbFieldName, 'type' => 1],
                    ['amount' => $amount, 'updated_at' => now()]
                );
            }

            return response()->json(['status' => 'success', 'message' => 'Earnings and benefits updated successfully!']);
        } catch (Exception $e) {
            Log::error('Error updating earnings and benefits: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to update earnings and benefits.'], 500);
        }
    }

    public function updateEarning(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'amount' => 'required|numeric',
            ]);

            DB::table('employee_earnings_and_deductions')
                ->where('id', $id)
                ->update([
                    'name' => $request->input('name'),
                    'amount' => $request->input('amount'),
                    'updated_at' => now(),
                ]);

            return response()->json(['status' => 'success', 'message' => 'Earning updated successfully!']);
        } catch (Exception $e) {
            Log::error('Error updating single earning: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to update earning.'], 500);
        }
    }

    public function deleteEarning($id)
    {
        try {
            DB::table('employee_earnings_and_deductions')->where('id', $id)->delete();
            return response()->json(['status' => 'success', 'message' => 'Earning deleted successfully!']);
        } catch (Exception $e) {
            Log::error('Error deleting earning: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to delete earning.'], 500);
        }
    }

    public function addDeduction(Request $request, Employee $employee)
    {
        Log::info('Deduction request received:', $request->all());

        $validator = Validator::make($request->all(), [
            'payroll_deduction_type_id' => 'required|exists:deduction,deduction_id',
            'deduction_category' => 'required|in:loan_repayment,advance_repayment,tax,nssf,nhif,other',
            'calculation_type' => 'required|in:fixed_amount,percentage_of_basic,percentage_of_gross,hourly_rate,daily_rate',
            'amount' => 'required_if:calculation_type,fixed_amount|nullable|numeric|min:0',
            'percentage' => 'required_if:calculation_type,percentage_of_basic,percentage_of_gross|nullable|numeric|min:0|max:100',
            'rate' => 'required_if:calculation_type,hourly_rate,daily_rate|nullable|numeric|min:0',
            'units' => 'nullable|integer|min:0',
            'limit_per_month' => 'nullable|numeric|min:0',
            'limit_per_year' => 'nullable|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'payroll_year' => 'required|integer|min:2020|max:2050',
            'payroll_month' => 'required|integer|min:1|max:12',
            'frequency' => 'required|in:monthly,weekly,bi_weekly,quarterly,annually,one_time',
            'is_recurring' => 'boolean',
            'description' => 'nullable|string|max:1000',
        ], [
            'payroll_deduction_type_id.required' => 'The deduction type field is required.',
        ]);

        if ($validator->fails()) {
            Log::error('Deduction validation failed:', $validator->errors()->toArray());
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        try {
            $input = $request->all();
            $input['created_by'] = Auth::id();
            $input['status'] = 1;

            // Generate reference number if not provided
            if (empty($input['reference_number'])) {
                $prefix = 'ED';
                $year = date('Y');
                $month = date('m');
                $lastDeduction = \App\Models\EmployeeDeductions::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('id', 'desc')
                    ->first();
                $sequence = $lastDeduction ? (intval(substr($lastDeduction->reference_number, -4)) + 1) : 1;
                $input['reference_number'] = $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            }

            // Set boolean values
            $input['is_recurring'] = $request->has('is_recurring');

            $employee->deductions()->create($input);

            return response()->json(['status' => 'success', 'message' => 'Deduction added successfully!']);
        } catch (Exception $e) {
            Log::error('Error adding deduction: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to add deduction.'], 500);
        }
    }

    public function updateDeduction(Request $request, Employee $employee, $deductionId)
    {
        $validator = Validator::make($request->all(), [
            'payroll_deduction_type_id' => 'required|exists:deduction,deduction_id',
            'deduction_category' => 'required|in:loan_repayment,advance_repayment,tax,nssf,nhif,other',
            'calculation_type' => 'required|in:fixed_amount,percentage_of_basic,percentage_of_gross,hourly_rate,daily_rate',
            'amount' => 'required_if:calculation_type,fixed_amount|nullable|numeric|min:0',
            'percentage' => 'required_if:calculation_type,percentage_of_basic,percentage_of_gross|nullable|numeric|min:0|max:100',
            'rate' => 'required_if:calculation_type,hourly_rate,daily_rate|nullable|numeric|min:0',
            'units' => 'nullable|integer|min:0',
            'limit_per_month' => 'nullable|numeric|min:0',
            'limit_per_year' => 'nullable|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'payroll_year' => 'required|integer|min:2020|max:2050',
            'payroll_month' => 'required|integer|min:1|max:12',
            'frequency' => 'required|in:monthly,weekly,bi_weekly,quarterly,annually,one_time',
            'is_recurring' => 'boolean',
            'description' => 'nullable|string|max:1000',
        ], [
            'payroll_deduction_type_id.required' => 'The deduction type field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        try {
            $deduction = \App\Models\EmployeeDeductions::where('employee_id', $employee->employee_id)->findOrFail($deductionId);

            $input = $request->all();
            $input['updated_by'] = Auth::id();

            // Set boolean values
            $input['is_recurring'] = $request->has('is_recurring');

            $deduction->update($input);

            return response()->json(['status' => 'success', 'message' => 'Deduction updated successfully!']);
        } catch (Exception $e) {
            Log::error('Error updating deduction: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to update deduction.'], 500);
        }
    }

    public function deleteDeduction(Employee $employee, $deductionId)
    {
        try {
            $deduction = \App\Models\EmployeeDeductions::where('employee_id', $employee->employee_id)->findOrFail($deductionId);
            $deduction->delete();

            return response()->json(['status' => 'success', 'message' => 'Deduction deleted successfully!']);
        } catch (Exception $e) {
            Log::error('Error deleting deduction: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to delete deduction.'], 500);
        }
    }
}