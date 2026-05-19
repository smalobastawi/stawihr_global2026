<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyQuestion extends Model
{
    //use BelongsToCompany;

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'survey_id',
        'question_text',
        'answer_type'
    ];

    protected $dates = [
        'deleted_at'
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    public function surveyAnswer()
    {
        return $this->hasMany(SurveyAnswer::class, 'survey_question_id');
    }

    public function employeeSurveyResponse()
    {
        return $this->hasMany(EmployeeSurveyResponse::class, 'survey_question_id');
    }
}
