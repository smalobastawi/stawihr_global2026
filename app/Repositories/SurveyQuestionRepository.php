<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

 namespace App\Repositories;

use App\Lib\Enumerations\AnswerTypes;
use App\Models\Survey;
use App\Models\SurveyQuestion;

 class SurveyQuestionRepository
 {

    public function getAllQuestions()  
    {
        return SurveyQuestion::with(['survey', 'surveyAnswer'])->latest()->get();
    }

    public function getAllQuestionsForAnswerType()  
    {
        return SurveyQuestion::whereIn('answer_type', [
            AnswerTypes::SINGLE_CHOICE,
            AnswerTypes::MULTIPLE_CHOICE,
            AnswerTypes::DROPDOWN
        ])->get();
    }

    public function storeSurveyQuestion(array $attributes)  
    {
        $survey = Survey::findOrFail(data_get($attributes, 'survey_id'));
        return $survey->surveyQuestion()->create([
            'question_text' => data_get($attributes, 'question_text'),
            'answer_type' => data_get($attributes, 'answer_type')
        ]);
    }

    public function show($id)  
    {
        return SurveyQuestion::with(['survey', 'surveyAnswer'])->findOrFail($id);
    }

    public function update(array $attributes, SurveyQuestion $surveyQuestion)  
    {
        $survey = Survey::findOrFail(data_get($attributes, 'survey_id'));
        return $surveyQuestion->update([
            'survey_id' => $survey->id,
            'question_text' => data_get($attributes, 'question_text'),
            'answer_type' => data_get($attributes, 'answer_type')
        ]);
    }
 }