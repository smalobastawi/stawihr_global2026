<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FrontSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::table('front_settings')->delete();
        DB::statement("ALTER TABLE `front_settings` AUTO_INCREMENT = 1");
        DB::table('front_settings')->insert(
            [
                ['company_title' => 'Test Company', 'home_page_big_title' => 'Title', 'short_description' => 'Tetet', 'service_title' => 'title',
                    'contact_email'=>'support@stawitech.com', 'contact_phone' =>'12345678901',
                    'job_title' => 'title1', 'about_us_image' => 'image.jpg', 'footer_text' => 'Footer', 'about_us_description' => 'Description', 'created_at' => $time, 'updated_at' => $time, 'logo' => 'logo.jpg'],

            ]

        );
    }
}
