<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Leave;

use App\Http\Requests\WeeklyHolidayRequest;

use App\Repositories\CommonRepository;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\LeaveGroup;
use App\Models\WeeklyHoliday;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\Request;


class WeeklyHolidayController extends Controller
{

    protected $commonRepository;

    public function __construct(CommonRepository $commonRepository){
        $this->commonRepository = $commonRepository;
    }


    public function index(){
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $results = WeeklyHoliday::get();
        return view('admin.leave.weeklyHoliday.index',['results'=>$results, 'signed_in_user_role'=>$signed_in_user_role]);
    }


    public function create(){
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $weekList = $this->commonRepository->weekList(); 
        $departments=Department::pluck('department_name','department_id',)->toArray();
        $leaveGroups=LeaveGroup::pluck('name','id')->toArray();
        return view('admin.leave.weeklyHoliday.form', ['weekList' => $weekList,
        'departments'=>$departments, 'signed_in_user_role'=>$signed_in_user_role,'leaveGroups'=>$leaveGroups]);
    }


    public function store(WeeklyHolidayRequest $request)
    {
        DB::beginTransaction();
        try {
            $weeklyHoliday = WeeklyHoliday::create($request->validated());
    
            // Attach selected departments (if any)
            if ($request->has('departments')) {
                $weeklyHoliday->departments()->sync($request->input('departments'));
            }
    
            DB::commit();
            return redirect()->route('weeklyHoliday.index')->with('success', 'Weekly holiday successfully saved.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error storing weekly holiday: " . $e->getMessage());
            return redirect()->route('weeklyHoliday.index')->with('error', 'Something went wrong! Please try again.');
        }
    }
    


    public function edit($id){
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        $weekList       = $this->commonRepository->weekList();
        $editModeData   = WeeklyHoliday::findOrFail($id);
        $departments=Department::pluck('department_name','department_id',)->toArray();
        $leaveGroups=LeaveGroup::pluck('name','id')->toArray();
        return view('admin.leave.weeklyHoliday.form', ['editModeData'=> $editModeData,'departments'=>$departments,'weekList' => $weekList, 'leaveGroups' => $leaveGroups, 
        'signed_in_user_role'=>$signed_in_user_role]);
    }


    public function update(WeeklyHolidayRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $weeklyHoliday = WeeklyHoliday::findOrFail($id);
            $weeklyHoliday->update($request->validated());
    
            // Sync updated departments
            if ($request->has('departments')) {
                $weeklyHoliday->departments()->sync($request->input('departments'));
            }

            if ($request->has('leave_groups')) {
                $weeklyHoliday->leaveGroups()->sync($request->input('leave_groups'));
            }
    
            DB::commit();
            return redirect()->back()->with('success', 'Weekly holiday successfully updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating weekly holiday (ID: $id): " . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }
    

    public function destroy($id){
        try{
            $data = WeeklyHoliday::findOrFail($id);
            $data->delete();
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
