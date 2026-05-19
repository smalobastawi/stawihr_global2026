<?php

namespace App\Http\Controllers\Surveys;

use App\Models\SurveyQuestion;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Repositories\SurveyRepository;
use App\Repositories\SurveyQuestionRepository;
use App\Http\Requests\StoreSurveyQuestionRequest;
use App\Http\Requests\UpdateSurveyQuestionRequest;

class SurveyQuestionController extends Controller
{

    private $surveyQuestionRepository, $surveyRepository;
    public function __construct(SurveyQuestionRepository $surveyQuestionRepository, SurveyRepository $surveyRepository)
    {
        $this->surveyQuestionRepository = $surveyQuestionRepository;
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
        $data = $this->surveyQuestionRepository->getAllQuestions();
        return view('admin.survey.survey_questions.index', [
            'data' => $data
        ]);
    }


    public function getSurveyQuestionChartResponses($id)
    {
        $responses = DB::table('employee_survey_responses')
            ->join('survey_questions', 'employee_survey_responses.survey_question_id', '=', 'survey_questions.id')
            ->where('employee_survey_responses.survey_question_id', $id)
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
            $chartData['labels'][] = "{$response->response}"; // Just show the response as label
            $chartData['data'][] = $response->count;
            $chartData['backgroundColor'][] = '#' . substr(md5(rand()), 0, 6); // Generate random colors
        }

        return response()->json($chartData);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $surveys = $this->surveyRepository->getActiveSurveys();
        return view('admin.survey.survey_questions.edit', [
            'surveyData' => $surveys
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSurveyQuestionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSurveyQuestionRequest $request)
    {
        //
        $data = $request->except("_token");
        $surveyQuestion = $this->surveyQuestionRepository->storeSurveyQuestion($data);
        if ($surveyQuestion) {
            return response()->json([
                'status' => true,
                'message' => 'You have successfully added survey question',
                'survey_id' => $data['survey_id']
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SurveyQuestion  $surveyQuestion
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $data = $this->surveyQuestionRepository->show($id);
        $responses = $data->employeeSurveyResponse()->latest()->get();
        return view('admin.survey.survey_questions.view', [
            'data' => $data,
            'responses' => $responses
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SurveyQuestion  $surveyQuestion
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $surveys = $this->surveyRepository->getActiveSurveys();
        $data = $this->surveyQuestionRepository->show($id);
        return view('admin.survey.survey_questions.edit', [
            'surveyData' => $surveys,
            'editModeData' => $data
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSurveyQuestionRequest  $request
     * @param  \App\Models\SurveyQuestion  $surveyQuestion
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSurveyQuestionRequest $request, SurveyQuestion $surveyQuestion)
    {
        //
        $data = $request->except("_token");
        $surveyQuestion = $this->surveyQuestionRepository->update($data, $surveyQuestion);
        if ($surveyQuestion) {
            return response()->json([
                'status' => true,
                'message' => 'Successfully updated survey question',
                'survey_id' => $data['survey_id']
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SurveyQuestion  $surveyQuestion
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $surveyQuestion = SurveyQuestion::withTrashed()->FindOrFail($id);

        try {
            $surveyQuestion->forceDelete();
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
