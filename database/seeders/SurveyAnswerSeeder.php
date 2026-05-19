<?php

namespace Database\Seeders;

use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Lib\Enumerations\AnswerTypes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SurveyAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if soft deletes are enabled, then force delete
        SurveyAnswer::query()->forceDelete();

        // Reset Auto-Increment ID to avoid unnecessary gaps
        DB::statement('ALTER TABLE surveys AUTO_INCREMENT = 1');
        //
        $questions = SurveyQuestion::whereIn('answer_type', [
            AnswerTypes::SINGLE_CHOICE,
            AnswerTypes::MULTIPLE_CHOICE,
            AnswerTypes::DROPDOWN,
        ])->get();

        $answers = [
            'How satisfied are you with your job?' => ['Very Satisfied', 'Satisfied', 'Neutral', 'Dissatisfied', 'Very Dissatisfied'],
            'Would you recommend this product to others?' => ['Yes', 'No'],
            'Is the workplace environment conducive for work?' => ['Yes', 'No'],
            'How would you rate the product quality?' => ['Excellent', 'Good', 'Average', 'Poor'],
            'How satisfied are you with the IT support?' => ['Very Satisfied', 'Satisfied', 'Neutral', 'Dissatisfied'],
            'Did the training meet your expectations?' => ['Yes', 'No'],
        ];

        foreach ($questions as $question) {
            if (isset($answers[$question->question_text])) {
                foreach ($answers[$question->question_text] as $answerText) {
                    SurveyAnswer::create([
                        'survey_id'          => $question->survey_id,
                        'survey_question_id' => $question->id,
                        'answer_text'        => $answerText,
                    ]);
                }
            }
        }
    }
}
