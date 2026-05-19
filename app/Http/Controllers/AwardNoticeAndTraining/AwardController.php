<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\AwardNoticeAndTraining;

use App\Repositories\CommonRepository;

use App\Http\Controllers\Controller;

use App\Http\Requests\AwardRequest;

use App\Models\EmployeeAward;

use Illuminate\Http\Request;


class AwardController extends Controller
{

    protected $commonRepository;

    public function __construct(CommonRepository $commonRepository){
        $this->commonRepository = $commonRepository;
    }
    
    public function index(){
        $results = EmployeeAward::with('employee')->orderBy('employee_award_id','DESC')->get();
        return view('admin.award.index',['results' => $results]);
    }



    public function create(){
        $employeeList = $this->commonRepository->employeeList();
        return view('admin.award.form',['employeeList' => $employeeList]);
    }



    public function store(AwardRequest $request) {
        $input = $request->all();
        try{
            EmployeeAward::create($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug==0){
            return redirect('award')->with('success', 'Award Successfully saved.');
        }else {
            return redirect('award')->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }



    public function edit($id){
        $employeeList = $this->commonRepository->employeeList();
        $editModeData = EmployeeAward::FindOrFail($id);
        return view('admin.award.form',compact('editModeData','employeeList'));
    }



    public function update(AwardRequest $request,$id) {
        $data = EmployeeAward::FindOrFail($id);
        $input = $request->all();
        try{
            $data->update($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug==0){
            return redirect()->back()->with('success', 'Award Successfully Updated.');
        }else {
            return redirect()->back()->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }



    public function destroy($id){
        try{
            $data = EmployeeAward::FindOrFail($id);
            $data->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug==0){
            return response()->json(['status' => 'success', 'message' => 'Award deleted successfully']);
        } elseif (strpos($bug, '1451') !== false || strpos($bug, 'foreign key') !== false) {
            return response()->json(['status' => 'foreign_key', 'message' => 'This data is used elsewhere']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Some Error Found !, Please try again.']);
        }
    }

}
