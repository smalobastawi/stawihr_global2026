<?php

namespace App\Http\Controllers\Surveys;

use App\Models\Survey;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\EmployeeSurveyResponse;
use App\Repositories\SurveyRepository;
use App\Http\Requests\StoreSurveyRequest;
use App\Http\Requests\UpdateSurveyRequest;
use App\Repositories\EmployeeSurveyResponseRepository;

class SurveyController extends Controller
{

    private $surveyRepository, $employeeSurveyResponseRepository;

    public function __construct(SurveyRepository $surveyRepository, EmployeeSurveyResponseRepository $employeeSurveyResponseRepository)
    {
        $this->surveyRepository = $surveyRepository;
        $this->employeeSurveyResponseRepository = $employeeSurveyResponseRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data = $this->surveyRepository->getAllSurveys();
        return view('admin.survey.index', [
            'data' => $data
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.survey.survey.edit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSurveyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSurveyRequest $request)
    {
        //
        $data = $request->except("_token");
        $survey = $this->surveyRepository->store($data);
        if ($survey) {
            return redirect()->route('survey.index')->with('success', 'Survey successfully saved');
        } else {
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $data = $this->surveyRepository->show($id);
        return view('admin.survey.survey.view', [
            'data' => $data
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function responses($id)
    {
        //
        $data = $this->surveyRepository->show($id);
        $responses = $data->employeeSurveyResponse()->get();
        return view('admin.survey.survey.responses', [
            'data' => $data,
            'responses' => $responses
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function getSurveyChartResponses($id)
    {
        $responses = DB::table('employee_survey_responses')
            ->join('survey_questions', 'employee_survey_responses.survey_question_id', '=', 'survey_questions.id')
            ->where('employee_survey_responses.survey_id', $id)
            ->select(
                'survey_questions.question_text',
                'employee_survey_responses.response',
                DB::raw('COUNT(employee_survey_responses.response) as count')
            )
            ->groupBy('survey_questions.question_text', 'employee_survey_responses.response')
            ->get();

        if ($responses->isEmpty()) {
            return response()->json([], 200);
        }

        // Prepare data for Chart.js
        $chartData = [
            'labels' => [],
            'data' => [],
            'backgroundColor' => []
        ];

        foreach ($responses as $response) {
            $chartData['labels'][] = "{$response->question_text}: {$response->response}";
            $chartData['data'][] = $response->count;
            $chartData['backgroundColor'][] = '#' . substr(md5(rand()), 0, 6); // Generate random colors
        }

        return response()->json($chartData);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $data = $this->surveyRepository->show($id);
        return view('admin.survey.survey.edit', [
            'editModeData' => $data
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSurveyRequest  $request
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSurveyRequest $request, Survey $survey)
    {
        //
        $data = $request->except("_token");
        $updateSurvey = $this->surveyRepository->update($data, $survey);
        if ($updateSurvey) {
            return redirect()->route('survey.show', $survey->id)->with('success', 'Have successfully updated survey');
        } else {
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $survey = Survey::withTrashed()->FindOrFail($id);

        try {
            $survey->forceDelete();
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
}
