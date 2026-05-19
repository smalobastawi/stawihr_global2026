<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyAnswer extends Model
{
    //use BelongsToCompany;

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'survey_id',
        'survey_question_id',
        'answer_text'
    ];

    protected $dates = [
        'deleted_at'
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    public function surveyQuestion()
    {
        return $this->belongsTo(SurveyQuestion::class, 'survey_question_id');
    }
}
