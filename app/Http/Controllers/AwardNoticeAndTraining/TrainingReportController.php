<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\AwardNoticeAndTraining;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;

use App\Models\PrintHeadSetting;

use App\Models\Training;

use App\Models\TrainingFacilitator;

use App\Models\TrainingInfo;

use App\Models\TrainingsView;
use App\Models\TrainingType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TrainingReportController extends Controller
{


    public function employeeTrainingReport(Request $request)
    {
        // Fetch data for filters
        $departments = Department::all();
        $facilitators = TrainingFacilitator::all();
        $trainingTypes = TrainingType::all();
        $trainings = Training::all();
        $employeeList = Employee::where('status', 1)->get();
    
        // Start with the base query using the view
        $data=TrainingsView::all();
        
        if($request->filtering==1)
        {
            $data = TrainingsView::query(); 
            if ($request->filled('department_id')) {
                $data = $data->where('departmentID', $request->department_id);
            }
            if ($request->filled('training_type_id')) {
                $data = $data->where('trainingTypeId', $request->training_type_id);
            }
            if ($request->filled('training_id')) {
                $data = $data->where('trainingID', $request->training_id);
            }
            if ($request->filled('employee_id')) {
                $data = $data->where('employeeID', $request->employee_id);
            }
            if ($request->filled('facilitator_id')) { // Unfinished filter applied
                $data = $data->where('facilitatorID', $request->facilitator_id);
            }
            $dataForFilter=$data;
            $data = $data->get();
            $tableData = view('admin.training.report.filtererdTable')->with(['results' => $data])->render();
            $departments=Department::whereIn('department_id',$dataForFilter->pluck('departmentID')->toArray())->get();
            $facilitators=TrainingFacilitator::whereIn('id',$dataForFilter->pluck('facilitatorID')->toArray())->get();
            $trainingTypes=TrainingType::whereIn('training_type_id',$dataForFilter->pluck('trainingTypeId')->toArray())->get();
            $trainings=Training::whereIn('id',$dataForFilter->pluck('trainingID')->toArray())->get();
            $employeeList=Employee::whereIn('employee_id',$dataForFilter->pluck('employeeID')->toArray())->get();
            $formData=view('admin.training.report.filterform')->with([
                'departments' => $departments,
                'facilitators' => $facilitators,
                'trainingTypes' => $trainingTypes,
                'trainings' => $trainings,
                'employeeList' => $employeeList,
                'results' => $data,
                'department_id' => $request->department_id,
                'training_type_id' => $request->training_type_id,
                'training_id' => $request->training_id,
                'employee_id' => $request->employee_id,
                'facilitator_id' => $request->facilitator_id,
            ])->render();
            return response()->json(['tableData' => $tableData,'formData'=>$formData]);
            
        }

        // Execute the query with applied filters
        
        // Pass all necessary data to the view
        return view('admin.training.report.employeeTrainingReport')->with([
            'departments' => $departments,
            'facilitators' => $facilitators,
            'trainingTypes' => $trainingTypes,
            'trainings' => $trainings,
            'employeeList' => $employeeList,
            'results' => $data,
            'department_id' => $request->department_id,
            'training_type_id' => $request->training_type_id,
            'training_id' => $request->training_id,
            'employee_id' => $request->employee_id,
            'facilitator_id' => $request->facilitator_id,
        ]);
    }



    public function employeeTrainingDataFormat($employee_id)
    {
        $trainingType = TrainingType::where('status',1)->get();
        $trainingInfo = TrainingInfo::where('employee_id',$employee_id)->get()->toArray();
        $arrayFormat = [];
        foreach ($trainingType as $value)
        {
            $temp = [];
            $hasData = array_search($value->training_type_id, array_column($trainingInfo, 'training_type_id'));
            if(gettype($hasData) == 'integer'){
                $temp['training_type_name'] = $value->training_type_name;
                $temp['action']             = "Yes";
                $temp['subject']            = $trainingInfo[$hasData]['subject'];
                $temp['start_date']         = $trainingInfo[$hasData]['start_date'];
                $temp['end_date']           = $trainingInfo[$hasData]['end_date'];
                $temp['certificate']        = $trainingInfo[$hasData]['certificate'];
            }else{
                $temp['training_type_name'] = $value->training_type_name;
                $temp['action']             = "No";
                $temp['subject']            = '';
                $temp['start_date']         = '';
                $temp['end_date']           = '';
                $temp['certificate']        = '';
            }
            $arrayFormat[] = $temp;
        }

        return $arrayFormat;
    }



    public function downloadTrainingReport(Request $request)

    {
       
        $employeeInfo    = Employee::with('department')->where('employee_id',$request->employee_id)->first();
        if(!$employeeInfo){
            $employeeInfo=new Employee();
        }
       // $results         = $this->employeeTrainingDataFormat($request->employee_id);
        $printHead       = PrintHeadSetting::first();
        $department=null;
        $trainingType=null;
        $training=null;
        $facilitator=null;
        $employee=null;

        $results = TrainingsView::query(); 
            if ($request->filled('department_id')) {
                $department=Department::where('department_id',$request->department_id)->first();
                $results = $results->where('departmentID', $request->department_id);
            }
            if ($request->filled('training_type_id')) {
                $trainingType=TrainingType::where('training_type_id',$request->training_type_id)->first();
                $results = $results->where('trainingTypeId', $request->training_type_id);
            }
            if ($request->filled('training_id')) {
                $training=Training::whereId($request->training_id)->first();
                $results = $results->where('trainingID', $request->training_id);
            }
            if ($request->filled('employee_id')) {
                $employee=Employee::where('employee_id',$request->employee_id)->first();
                $results = $results->where('employeeID', $request->employee_id);
            }
            if ($request->filled('facilitator_id')) { // Unfinished filter applied
                $facilitator=TrainingFacilitator::whereId($request->facilitator_id)->first();
                $results = $results->where('facilitatorID', $request->facilitator_id);
            }
          $results = $results->get(); 

        $data = [
            'results'   => $results,
            'printHead' => $printHead,
            'employee_name' => $employeeInfo->first_name.' '.$employeeInfo->last_name,
            'department_name' => $employeeInfo->department->department_name ?? "",
            'department'=>$department,
            'trainingType'=>$trainingType,
            'training'=>$training,
            'facilitator'=>$facilitator,
            'employee'=>$employee,
        ];

        $pdf = Pdf::loadView('admin.training.report.pdf.employeeTrainingReportPdf',$data);
        $pdf->setPaper('A4', 'landscape');
        $pageName = "training.pdf";
        return $pdf->download($pageName);
    }


}
