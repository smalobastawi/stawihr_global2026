<?php

namespace Database\Seeders;

use App\Models\FeedbackCategories;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeedbackCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            'Work Environment',
            'Benefits and Compensation',
            'Work-Life Balance',
            'Professional Growth',
            'Leadership and Management',
            'Team Dynamics',
            'Policies and Processes',
            'Diversity and Inclusion',
            'Technology and Tools',
            'General Suggestions',
        ];

        foreach ($categories as $category) {
            FeedbackCategories::create(['name' => $category]);
        }
    }
}
