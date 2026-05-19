<?php

namespace Database\Seeders;

use App\Models\DisciplinaryCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DisciplinaryCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Attendance Issues',
                'description' => 'Repeated lateness or absence without valid reasons.',
                'category_code' => 'HR001',
            ],
            [
                'name' => 'Code of Conduct Violation',
                'description' => 'Breach of company rules and ethical guidelines.',
                'category_code' => 'HR002',
            ],
            [
                'name' => 'Harassment',
                'description' => 'Behavior causing distress or harm to colleagues.',
                'category_code' => 'HR003',
            ],
            [
                'name' => 'Performance Issues',
                'description' => 'Consistent underperformance or failure to meet objectives.',
                'category_code' => 'HR004',
            ],
        ];

        foreach ($categories as $category) {
            DisciplinaryCategory::create($category);
        }
    }
}
