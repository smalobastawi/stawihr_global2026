<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\AwardNoticeAndTraining;

use Carbon\Carbon;
use App\Models\Training;
use App\Models\TrainingInfo;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

use App\Repositories\CommonRepository;
use App\Http\Requests\Trainings\StoreTrainingRequest;
use App\Http\Requests\Trainings\UpdateTrainingRequest;



class EmployeeTrainingController extends Controller
{

    protected $commonRepository;

    public function __construct(CommonRepository $commonRepository)
    {
        $this->commonRepository = $commonRepository;
    }



    public function index()
    {
        $results = Training::with(['trainingType'])->orderBy('id', 'DESC')->get();
        return view('admin.training.employeeTraining.index', ['results' => $results]);
    }



    public function create()
    {
        $employeeList      = $this->commonRepository->employeeList();
        $trainingTypeList  = $this->commonRepository->trainingTypeList();
        $facilitators = $this->commonRepository->trainingFacilitorList();
        return view('admin.training.employeeTraining.form', [
            'employeeList' => $employeeList,
            'trainingTypeList' => $trainingTypeList,
            'facilitatorList' => $facilitators,
        ]);
    }

    public function store(StoreTrainingRequest $request)
    {

        try {
            $training = Training::create(array_merge($request->validated(), [
                'created_by' => Auth::id(),
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
            ]));

            $bug = 0;
        } catch (\Exception $e) {
            dd($e);
        }

        if ($bug == 0) {
            return redirect('trainingInfo')->with('success', 'Employee training successfully saved.');
        } else {
            return redirect('trainingInfo')->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }

    public function edit(Training $training)
    {
        $editModeData = $training;
        $employeeList = $this->commonRepository->employeeList();
        $trainingTypeList = $this->commonRepository->trainingTypeList();
        $facilitators = $this->commonRepository->trainingFacilitorList();

        return view('admin.training.employeeTraining.form', [
            'employeeList' => $employeeList,
            'trainingTypeList' => $trainingTypeList,
            'facilitatorList' => $facilitators,
            'editModeData' => $editModeData,
            'start_date' => $training->start_date ? Carbon::parse($training->start_date)->format('Y-m-d') : null,
            'end_date' => $training->end_date ? Carbon::parse($training->end_date)->format('Y-m-d') : null,
            'start_time' => $training->start_time ? Carbon::parse($training->start_time)->format('H:i') : null,
            'end_time' => $training->end_time ? Carbon::parse($training->end_time)->format('H:i') : null
        ]);
    }

    public function update(UpdateTrainingRequest $request, Training $training)
    {

        $updateData = $request->validated();
        $updateData['updated_by'] = Auth::id();

        // Format dates correctly
        $updateData['start_date'] = Carbon::parse($updateData['start_date']);
        $updateData['end_date'] = Carbon::parse($updateData['end_date']);
        $updateData['start_time'] = $request->start_time;
        $updateData['end_time'] = $request->end_time;

        try {
            // Use fill() + save() for better debugging
            $training->fill($updateData);
            $saved = $training->save();

            if ($saved) {
                return redirect()->route('trainingInfo.index')->with('success', 'Training successfully updated.');
            }

            return redirect()->back()
                ->with('error', 'Update failed with no error thrown.');
        } catch (\Exception $e) {
            Log::error("Training update failed: " . $e->getMessage(), [
                'training_id' => $training->id,
                'error' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Update failed. Please try again. ' . $e->getMessage());
        }
    }

    public function show(Training $training)
    {
        $showOnly = '1';
        $editModeData = $training;
        $employeeList      = $this->commonRepository->employeeList();
        $trainingTypeList  = $this->commonRepository->trainingTypeList();
        $facilitators = $this->commonRepository->trainingFacilitorList();

        return view('admin.training.employeeTraining.form', [
            'employeeList' => $employeeList,
            'trainingTypeList' => $trainingTypeList,
            'facilitatorList' => $facilitators,
            'showOnly' => $showOnly,
            'editModeData' => $editModeData,
            'start_date' => $training->start_date ? Carbon::parse($training->start_date)->format('Y-m-d') : null,
            'end_date' => $training->end_date ? Carbon::parse($training->end_date)->format('Y-m-d') : null
        ]);
    }



    public function destroy($id)
    {
        try {
            $data = Training::FindOrFail($id);

            // if (!is_null($data->certificate)) {
            //     if (file_exists('uploads/employeeTrainingCertificate/' . $data->certificate) and !empty($data->certificate)) {
            //         unlink('uploads/employeeTrainingCertificate/' . $data->certificate);
            //     }
            // }
            $data->delete();
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

    public function attendances(Training $training)
    {
        $showOnly = '1';
        $editModeData = $training;
        $employeeList      = $this->commonRepository->employeeList();
        $trainingTypeList  = $this->commonRepository->trainingTypeList();
        $facilitators = $this->commonRepository->trainingFacilitorList();

        return view('admin.training.employeeTraining.form', [
            'employeeList' => $employeeList,
            'trainingTypeList' => $trainingTypeList,
            'facilitatorList' => $facilitators,
            'showOnly' => $showOnly,
            'editModeData' => $editModeData
        ]);
    }

    public function participants(Training $training)
    {
        $showOnly = '1';
        $editModeData = $training;
        $employeeList      = $this->commonRepository->employeeList();
        $trainingTypeList  = $this->commonRepository->trainingTypeList();
        $facilitators = $this->commonRepository->trainingFacilitorList();

        return view('admin.training.employeeTraining.form', [
            'employeeList' => $employeeList,
            'trainingTypeList' => $trainingTypeList,
            'facilitatorList' => $facilitators,
            'showOnly' => $showOnly,
            'editModeData' => $editModeData
        ]);
    }

    public function approveParticipants(Training $training)
    {
        return redirect()->back();
    }

    public function approveAttendance(Training $training)
    {
        return redirect()->back();
    }
}
