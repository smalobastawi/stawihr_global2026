<?php

namespace App\Http\Controllers\Surveys;

use App\Models\Survey;
use App\Models\Employee;
use App\Http\Controllers\Controller;
use App\Models\EmployeeSurveyResponse;
use App\Repositories\SurveyRepository;
use App\Http\Requests\StoreEmployeeSurveyResponseRequest;
use App\Http\Requests\UpdateEmployeeSurveyResponseRequest;

class EmployeeSurveyResponseController extends Controller
{

    private $employeeSurveyResponse, $surveyRepository;
    public function __construct(EmployeeSurveyResponse $employeeSurveyResponse, SurveyRepository $surveyRepository)
    {
        $this->employeeSurveyResponse = $employeeSurveyResponse;
        $this->surveyRepository = $surveyRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data = $this->surveyRepository->getActiveSurveys();
        return view('admin.survey.responses.index', [
            'data' => $data
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        //
        $data = $this->surveyRepository->show($id);
        $employeeID = session('logged_session_data.employee_id');

        // Fetch existing responses for the logged-in employee
        $existingResponses = EmployeeSurveyResponse::where('survey_id', $id)
            ->where('employee_id', $employeeID)
            ->pluck('response', 'survey_question_id')
            ->toArray();


        return view('admin.survey.responses.edit', [
            'data' => $data,
            'existingResponses' => $existingResponses
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreEmployeeSurveyResponseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEmployeeSurveyResponseRequest $request, Survey $survey)
    {
        //
        $data = $request->except("_token");
        $login_employee = employeeInfo();

        if (!$login_employee) {
            return redirect()->back()->with('error', 'You have to be an employee to fill the survey');
        }

        $employeeInfo = Employee::where('employee.employee_id', session('logged_session_data.employee_id'))->first();

        if (!$employeeInfo) {
            return redirect()->back()->with('error', 'Employee information not found.');
        }

        $employeeID = $employeeInfo->employee_id;

        foreach ($data['answers'] as $questionId => $response) {

            // Handle file uploads
            if ($request->hasFile("response.$questionId")) {
                $filePath = $request->file("response.$questionId")->store('responses/files', 'public');
                $response = $filePath;
            }

            // Check if the employee has already answered this question in this survey
            $existingResponse = EmployeeSurveyResponse::where([
                'survey_id' => $survey->id,
                'survey_question_id' => $questionId,
                'employee_id' => $employeeID,
            ])->first();

            if ($existingResponse) {
                // Update existing response
                // Ensure it's an object before calling update
                $existingResponse->update([
                    'response' => is_array($response) ? json_encode($response) : $response,
                ]);
                // if (is_object($existingResponse)) {
                    
                // }
            } else {
                // Save each response
                EmployeeSurveyResponse::create([
                    'survey_id' => $survey->id,
                    'survey_question_id' => $questionId,
                    'employee_id' => $employeeID,
                    'response' => is_array($response) ? json_encode($response) : $response,
                ]);
            }
        }

        return redirect()
            ->route('survey.responses.index')
            ->with('success', 'Survey responses submitted successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EmployeeSurveyResponse  $employeeSurveyResponse
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EmployeeSurveyResponse  $employeeSurveyResponse
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeSurveyResponse $employeeSurveyResponse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEmployeeSurveyResponseRequest  $request
     * @param  \App\Models\EmployeeSurveyResponse  $employeeSurveyResponse
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEmployeeSurveyResponseRequest $request, EmployeeSurveyResponse $employeeSurveyResponse)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EmployeeSurveyResponse  $employeeSurveyResponse
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeSurveyResponse $employeeSurveyResponse)
    {
        //
    }
}
