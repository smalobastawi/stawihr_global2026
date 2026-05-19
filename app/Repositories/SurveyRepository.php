<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Repositories;

use App\Models\Survey;
use Illuminate\Support\Str;
use App\Lib\Enumerations\SurveyStatus;

class SurveyRepository{

    public function getAllSurveys()  
    {
        return Survey::latest()->get();
    }

    public function getActiveSurveys($status = SurveyStatus::PUBLISHED)  
    {
        return Survey::with(['surveyQuestion', 'surveyAnswer', 'employeeSurveyResponse'])->where('status', $status)->latest()->get();
    }

    public function store(array $attributes)  
    {
        return Survey::create([
            'title' => data_get($attributes, 'title'),
            'slug' => Str::slug(data_get($attributes, 'title')),
            'description' => data_get($attributes, 'description'),
            'status' => data_get($attributes, 'status'),
            'start_date' => data_get($attributes, 'start_date'),
            'end_date' => data_get($attributes, 'end_date')
        ]);
    }

    public function show($id)  
    {
        return Survey::with(['surveyQuestion', 'surveyAnswer', 'employeeSurveyResponse'])
        ->findOrFail($id);
    }

    public function update(array $attributes, Survey $survey)  
    {
        return $survey->update([
            'title' => data_get($attributes, 'title'),
            'slug' => Str::slug(data_get($attributes, 'title')),
            'description' => data_get($attributes, 'description'),
            'status' => data_get($attributes, 'status'),
            'start_date' => data_get($attributes, 'start_date'),
            'end_date' => data_get($attributes, 'end_date')
        ]);
    }

    public function destroySurvey(Survey $survey)  
    {
        return $survey->delete();
    }
}