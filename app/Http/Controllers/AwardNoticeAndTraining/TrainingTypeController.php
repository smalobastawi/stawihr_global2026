<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\AwardNoticeAndTraining;

use App\Http\Requests\TrainingTypeRequest;

use App\Http\Controllers\Controller;
use App\Models\Training;
use Illuminate\Http\Request;

use App\Models\TrainingType;

use App\Models\TrainingInfo;



class TrainingTypeController extends Controller
{


    public function index()
    {
        $results = TrainingType::orderBy('training_type_id','DESC')->get();
        return view('admin.training.trainingType.index',['results' => $results]);
    }



    public function create()
    {
        return view('admin.training.trainingType.form');
    }



    public function store(TrainingTypeRequest $request)
    {
        $input = $request->all();

        try{
            TrainingType::create($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug==0){
            return redirect('trainingType')->with('success', 'Training type successfully saved.');
        }else {
            return redirect('trainingType')->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }



    public function edit($id)
    {
        $editModeData = TrainingType::FindOrFail($id);
        return view('admin.training.trainingType.form',compact('editModeData'));
    }



    public function update(TrainingTypeRequest $request,$id)
    {
        $data = TrainingType::FindOrFail($id);
        $input = $request->all();

        try{
            $data->update($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug==0){
            return redirect()->back()->with('success', 'Training type successfully updated.');
        }else {
            return redirect()->back()->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }



    public function destroy($id)
    {
        try{
            $data = TrainingType::FindOrFail($id);
            $data->delete();
            TrainingInfo::where('training_type_id','=',$id)->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug == 0){
            echo "success";
        }elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }

    public function listTrainings(Request $request){

        $dataType='Trainings';
        $typeId=$request->type;
        $facilitator=$request->facilitator;
    

            $data = Training::where('training_type_id', $typeId)->where('facilitator_id',$facilitator)->get();           

        return response()->json([
            'html' => view('admin.training.invites_attendances.options.trainings', compact('data','dataType'))->render(),
        ]);

    }


}
