<?php 
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

 namespace App\Repositories;

use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;

 class SurveyAnswerRepository
 {
    public function getAllAnswers()  
    {
        return SurveyAnswer::with(['survey', 'surveyQuestion'])->latest()->get();
    }

    public function storeAnswer(array $attributes)  
    {
        $surveyQuestion = SurveyQuestion::findOrFail(data_get($attributes, 'survey_question_id'));
        return SurveyAnswer::create([
            'survey_id' => $surveyQuestion->survey->id,
            'survey_question_id' => $surveyQuestion->id,
            'answer_text' => data_get($attributes, 'answer_text')
        ]);
    }

    public function show($id)  
    {
        return SurveyAnswer::with(['survey', 'surveyQuestion'])->findOrFail($id);
    }

    public function updateAnswer(array $attributes, SurveyAnswer $surveyAnswer)  
    {
        $surveyQuestion = SurveyQuestion::findOrFail(data_get($attributes, 'survey_question_id'));
        return $surveyAnswer->update([
            'survey_id' => $surveyQuestion->survey_id,
            'survey_question_id' => $surveyQuestion->id,
            'answer_text' => data_get($attributes, 'answer_text')
        ]);
    }
 }