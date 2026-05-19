<?php

namespace Database\Seeders;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Lib\Enumerations\AnswerTypes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SurveyQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

         // Check if soft deletes are enabled, then force delete
        SurveyQuestion::query()->forceDelete(); 
        
        // Reset Auto-Increment ID to avoid unnecessary gaps
        DB::statement('ALTER TABLE surveys AUTO_INCREMENT = 1');

        //
        $surveys = Survey::all();

        $questions = [
            'Employee Satisfaction Survey' => [
                ['question_text' => 'How satisfied are you with your job?', 'answer_type' => AnswerTypes::RATING_SCALE],
                ['question_text' => 'What can be improved in your workplace?', 'answer_type' => AnswerTypes::TEXTAREA],
                ['question_text' => 'Do you feel valued at work?', 'answer_type' => AnswerTypes::YES_NO],
            ],
            'Product Feedback Survey' => [
                ['question_text' => 'How would you rate the product quality?', 'answer_type' => AnswerTypes::RATING_SCALE],
                ['question_text' => 'Would you recommend this product to others?', 'answer_type' => AnswerTypes::YES_NO],
                ['question_text' => 'What improvements would you like to see?', 'answer_type' => AnswerTypes::TEXTAREA],
            ],
            'Workplace Environment Survey' => [
                ['question_text' => 'Is the workplace environment conducive for work?', 'answer_type' => AnswerTypes::YES_NO],
                ['question_text' => 'How would you describe the company culture?', 'answer_type' => AnswerTypes::TEXT],
                ['question_text' => 'What changes would make the workplace better?', 'answer_type' => AnswerTypes::TEXTAREA],
            ],
            'IT Services Satisfaction Survey' => [
                ['question_text' => 'How satisfied are you with the IT support?', 'answer_type' => AnswerTypes::RATING_SCALE],
                ['question_text' => 'Have you faced any major IT issues recently?', 'answer_type' => AnswerTypes::YES_NO],
                ['question_text' => 'Describe any IT-related issues you’ve experienced.', 'answer_type' => AnswerTypes::TEXTAREA],
            ],
            'Training Effectiveness Survey' => [
                ['question_text' => 'Did the training meet your expectations?', 'answer_type' => AnswerTypes::YES_NO],
                ['question_text' => 'What aspects of the training were most useful?', 'answer_type' => AnswerTypes::TEXTAREA],
                ['question_text' => 'How would you rate the trainer’s effectiveness?', 'answer_type' => AnswerTypes::RATING_SCALE],
            ],
        ];

        foreach ($surveys as $survey) {
            if (isset($questions[$survey->title])) {
                foreach ($questions[$survey->title] as $question) {
                    SurveyQuestion::create([
                        'survey_id'    => $survey->id,
                        'question_text' => $question['question_text'],
                        'answer_type'  => $question['answer_type'],
                    ]);
                }
            }
        }
    }
}