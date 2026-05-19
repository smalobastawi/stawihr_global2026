<?php

namespace Database\Seeders;

use App\Models\Survey;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SurveySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         // Check if soft deletes are enabled, then force delete
         Survey::query()->forceDelete(); 
        
         // Reset Auto-Increment ID to avoid unnecessary gaps
         DB::statement('ALTER TABLE surveys AUTO_INCREMENT = 1');
        //
        $surveys = [
            [
                'title'       => 'Employee Satisfaction Survey',
                'description' => 'A survey to measure employee satisfaction levels.',
                'status'      => 'active',
                'start_date'  => now(),
                'end_date'    => now()->addDays(30),
            ],
            [
                'title'       => 'Product Feedback Survey',
                'description' => 'A survey to gather customer feedback on our latest product.',
                'status'      => 'active',
                'start_date'  => now(),
                'end_date'    => now()->addDays(60),
            ],
            [
                'title'       => 'Workplace Environment Survey',
                'description' => 'An assessment of workplace conditions and culture.',
                'status'      => 'inactive',
                'start_date'  => now()->subDays(10),
                'end_date'    => now()->addDays(20),
            ],
            [
                'title'       => 'IT Services Satisfaction Survey',
                'description' => 'Survey to evaluate satisfaction with IT support and infrastructure.',
                'status'      => 'active',
                'start_date'  => now(),
                'end_date'    => now()->addDays(45),
            ],
            [
                'title'       => 'Training Effectiveness Survey',
                'description' => 'A survey to assess the effectiveness of training programs.',
                'status'      => 'active',
                'start_date'  => now(),
                'end_date'    => now()->addDays(90),
            ],
        ];

        foreach ($surveys as $survey) {
            Survey::create([
                'title'       => $survey['title'],
                'slug'        => Str::slug($survey['title']),
                'description' => $survey['description'],
                'status'      => $survey['status'],
                'start_date'  => $survey['start_date'],
                'end_date'    => $survey['end_date'],
            ]);
        }
    }
}