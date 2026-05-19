<?php

namespace App\Http\Controllers\AwardNoticeAndTraining;

use App\Http\Controllers\Controller;
use App\Http\Requests\Trainings\StoreFacilitatorRequest;
use App\Http\Requests\Trainings\UpdateFacilitatorRequest;
use App\Models\Training;
use App\Models\TrainingFacilitator;
use App\Models\TrainingType;
use Illuminate\Http\Request;

class TrainingFacilitatorController extends Controller
{
    // Display a listing of the resource
    public function index()
    {
        $facilitators = TrainingFacilitator::all();
        return view('admin.training.facilitator.index') ->with(['results'=>$facilitators]);
    }

    // Show the form for creating a new resource
    public function create()
    {
        return view('admin.training.facilitator.form');
    }

    // Store a newly created resource in storage
    public function store(StoreFacilitatorRequest $request)
    {
        TrainingFacilitator::create($request->validated());
        return redirect()->route('training.facilitator.index')->with('success', 'Facilitator created successfully.');
    }

    // Display the specified resource
    public function show(TrainingFacilitator $facilitator)
    {
        return view('admin.training.facilitator.form', compact('facilitator'));
    }

    // Show the form for editing the specified resource
    public function edit(TrainingFacilitator $facilitator)
    { 
        return view('admin.training.facilitator.form')-> with(['editModeData'=>$facilitator]);
    }

    // Update the specified resource in storage
    public function update(UpdateFacilitatorRequest $request, TrainingFacilitator $facilitator)
    {
        $facilitator->update($request->validated());
        return redirect()->route('training.facilitator.index')->with('success', 'Facilitator updated successfully.');
    }

    // Remove the specified resource from storage
    public function destroy(TrainingFacilitator $facilitator)
    {
        $facilitator->delete();
       echo 'success';
    }

    public function filter(Request $request)
    {
        $dataType='Facilitators';
        $typeId=$request->training_type_id;
        $facilitator=$request->facilitator; 
        $facilitators=TrainingFacilitator::whereIn('id',Training::where('training_type_id',$typeId)->pluck('facilitator_id')->toArray())->get();

            $data = $facilitators;     

        return response()->json([
            'html' => view('admin.training.invites_attendances.options.facilitators', compact('data','dataType'))->render(),
        ]);
    }
}
