<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Leave;

use App\Http\Requests\PublicHolidayRequest;

use App\Repositories\CommonRepository;

use App\Http\Controllers\Controller;

use App\Models\HolidayDetails;

use App\Models\User;
use Illuminate\Http\Request;

class PublicHolidayController extends Controller
{

    protected $commonRepository;

    public function __construct(CommonRepository $commonRepository){
        $this->commonRepository = $commonRepository;
    }


    public function index(){

        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        $results = HolidayDetails::with('holiday')->orderBy('holiday_details_id', 'desc')->get();
        return view('admin.leave.publicHoliday.index',['results'=>$results, 'signed_in_user_role'=>$signed_in_user_role]);
    }


    public function create(){
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        $holidayList = $this->commonRepository->holidayList();
        return view('admin.leave.publicHoliday.form',['holidayList' => $holidayList, 'signed_in_user_role'=>$signed_in_user_role]);
    }


    public function store(PublicHolidayRequest $request){
        $input              = $request->all();
        $input['from_date'] = dateConvertFormtoDB($input['from_date']);
        $input['to_date']   = dateConvertFormtoDB($input['to_date']);
        try{
            HolidayDetails::create($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug==0){
            return redirect()->route('publicHoliday.index')->with('success', 'Public holiday successfully saved.');
        }else {
            return redirect()->back()->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }


    public function edit($id){
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        $holidayList    = $this->commonRepository->holidayList();
        $editModeData   = HolidayDetails::findOrFail($id);
        return view('admin.leave.publicHoliday.form',['editModeData' => $editModeData,'holidayList'=>$holidayList, 'signed_in_user_role'=>$signed_in_user_role]);
    }


    public function update(PublicHolidayRequest $request,$id) {
        $holidayDetails     = HolidayDetails::findOrFail($id);
        $input              = $request->all();
        $input['from_date'] = dateConvertFormtoDB($input['from_date']);
        $input['to_date']   = dateConvertFormtoDB($input['to_date']);
        try{
            $holidayDetails->update($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug==0){
            return redirect()->back()->with('success', 'Public holiday successfully updated. ');
        }else {
            return redirect()->back()->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }


    public function destroy($id){
        try{
            $holidayDetails = HolidayDetails::findOrFail($id);
            $holidayDetails->delete();
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
