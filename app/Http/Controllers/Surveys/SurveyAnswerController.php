<?php

namespace App\Http\Controllers\Surveys;

use App\Models\SurveyAnswer;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSurveyAnswerRequest;
use App\Http\Requests\UpdateSurveyAnswerRequest;
use App\Repositories\SurveyAnswerRepository;
use App\Repositories\SurveyQuestionRepository;
use App\Repositories\SurveyRepository;

class SurveyAnswerController extends Controller
{
    private $surveyAnswerRepository,  $surveyQuestionRepository, $surveyRepository;
    public function __construct(SurveyAnswerRepository $surveyAnswerRepository, SurveyQuestionRepository $surveyQuestionRepository, SurveyRepository $surveyRepository)
    {
        $this->surveyAnswerRepository = $surveyAnswerRepository;   
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
        $data = $this->surveyAnswerRepository->getAllAnswers();
        return view('admin.survey.survey_answers.index', [
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
        $surveyQuestions = $this->surveyQuestionRepository->getAllQuestionsForAnswerType();
        return view('admin.survey.survey_answers.create', [
            'surveyQuestions' => $surveyQuestions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSurveyAnswerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSurveyAnswerRequest $request)
    {
        try{

            // Get the survey ID from the question
            $surveyQuestion = \App\Models\SurveyQuestion::findOrFail($request->survey_question_id);

            // Check if the survey_id exists to prevent foreign key issues
            if (!$surveyQuestion->survey_id) {
                return back()->with('error', 'Survey does not exist for this question.');
            }
            
            foreach ($request->answer_text as $answer) {
                SurveyAnswer::create([
                    'survey_id' => $surveyQuestion->survey_id,
                    'survey_question_id' => $surveyQuestion->id,
                    'answer_text' => $answer,
                ]);

            }
            return redirect()->route('survey.answers.index')
                ->with('success', 'Successfully added answer');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
        
    }


     /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSurveyAnswerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function storeAjax(StoreSurveyAnswerRequest $request)
    {
        try{

            // Get the survey ID from the question
            $surveyQuestion = \App\Models\SurveyQuestion::findOrFail($request->survey_question_id);

            // Check if the survey_id exists to prevent foreign key issues
            if (!$surveyQuestion->survey_id) {

                return response()->json([
                    'status' => false,
                    'error' => 'Survey does not exist for this question.'
                ]);
            }
            
            foreach ($request->answer_text as $answer) {
                SurveyAnswer::create([
                    'survey_id' => $surveyQuestion->survey_id,
                    'survey_question_id' => $surveyQuestion->id,
                    'answer_text' => $answer,
                ]);

            }

            return response()->json([
                'status' => true,
                'message' => 'Successfully added answer'
            ]);

        } catch (\Exception $e) {
            
            return response()->json([
                'status' => false,
                'error' => 'Error: ' . $e->getMessage()
            ]);

        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SurveyAnswer  $surveyAnswer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $data = $this->surveyAnswerRepository->show($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SurveyAnswer  $surveyAnswer
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $surveyQuestions = $this->surveyQuestionRepository->getAllQuestionsForAnswerType();
        $data = $this->surveyAnswerRepository->show($id);
        return view('admin.survey.survey_answers.edit', [
            'surveyQuestions' => $surveyQuestions,
            'editModeData' => $data
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSurveyAnswerRequest  $request
     * @param  \App\Models\SurveyAnswer  $surveyAnswer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSurveyAnswerRequest $request, SurveyAnswer $surveyAnswer)
    {
        //
        $data = $request->except("_token");
        $surveyAnswer = $this->surveyAnswerRepository->updateAnswer($data, $surveyAnswer);
        if($surveyAnswer)
        {
            return redirect()->route('survey.answers.index')->with('success', 'Successfully added answer');
        }else{
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SurveyAnswer  $surveyAnswer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $surveyAnswer = SurveyAnswer::withTrashed()->FindOrFail($id);

        try {
            $surveyAnswer->forceDelete();
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