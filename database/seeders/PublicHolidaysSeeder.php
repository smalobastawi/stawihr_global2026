<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PublicHolidaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $holidays = [
            [
                'name' => 'New Year\'s Day',
                'date' => '2024-01-01',
            ],
            [
                'name' => 'Good Friday',
                'date' => '2024-03-29',
            ],
            [
                'name' => 'Easter Monday',
                'date' => '2024-04-01',
            ],
            [
                'name' => 'Eid al-Fitr',
                'date' => '2024-04-10', // This date is approximate
                'is_movable' => true,
            ],
            [
                'name' => 'International Workers\' Day (Labour Day)',
                'date' => '2024-05-01',
            ],
            [
                'name' => 'Madaraka Day',
                'date' => '2024-06-01',
            ],
            [
                'name' => 'Eid al-Adha',
                'date' => '2024-06-17', // This date is approximate
                'is_movable' => true,
            ],
            [
                'name' => 'Moi Day/Huduma Day/Utamaduni Day',
                'date' => '2024-10-10',
            ],
            [
                'name' => 'Mashujaa Day',
                'date' => '2024-10-20',
            ],
            [
                'name' => 'Jamhuri Day (Independence Day)',
                'date' => '2024-12-12',
            ],
            [
                'name' => 'Christmas Day',
                'date' => '2024-12-25',
            ],
            [
                'name' => 'Boxing Day',
                'date' => '2024-12-26',
            ],
        ];

        // Insert holidays into database
        DB::table('holidays')->insert($holidays);
    }
}
