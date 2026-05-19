<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Training;
use App\Models\TrainingFacilitator;
use App\Models\TrainingType;
use App\Models\Employee;
use App\Models\User;
use App\Models\TrainingAttendant;
use App\Models\TrainingInvitee;
use Illuminate\Support\Facades\DB;

class TrainingAttendanceAndInviteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Training Types
        $trainingTypes = [
            'Technical',
            'Soft Skills',
            'Management',
            'Health & Safety'
        ];

        foreach ($trainingTypes as $type) {
            DB::table('training_type')->insert([
                'training_type_name' => $type,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create Training Facilitators
        $facilitators = [
            ['name' => 'John Doe', 'contact_email' => 'john.doe@example.com', 'contact_phone' => '123456789', 'type' => 'internal', 'expertise' => 'Project Management'],
            ['name' => 'Jane Smith', 'contact_email' => 'jane.smith@example.com', 'contact_phone' => '987654321', 'type' => 'external', 'expertise' => 'Agile Methodology'],
            ['name' => 'Mark Johnson', 'contact_email' => 'mark.johnson@example.com', 'contact_phone' => '123123123', 'type' => 'internal', 'expertise' => 'Data Analysis'],
            ['name' => 'Emma Brown', 'contact_email' => 'emma.brown@example.com', 'contact_phone' => '321321321', 'type' => 'external', 'expertise' => 'Leadership']
        ];

        foreach ($facilitators as $facilitator) {
            DB::table('training_facilitators')->insert($facilitator);
        }

        // Create Trainings
        $trainingTypes = DB::table('training_type')->pluck('training_type_id');
        $facilitators = DB::table('training_facilitators')->pluck('id');

        foreach (range(1, 10) as $index) {
            DB::table('trainings')->insert([
                'training_type_id' => $trainingTypes->random(),
                'facilitator_id' => $facilitators->random(),
                'subject' => 'Training ' . $index,
                'attendance_type' => ['physical', 'online'][rand(0, 1)],
                'attendance_link' => (rand(0, 1) == 1) ? 'https://example.com' : null,
                'attendance_location' => (rand(0, 1) == 1) ? 'Room ' . rand(1, 10) : null,
                'start_date' => now()->addDays(rand(1, 30)),
                'end_date' => now()->addDays(rand(31, 60)),
                'description' => 'Description of Training ' . $index,
                'created_by' => 1, // Assuming user_id 1
                'updated_by' => 1, // Assuming user_id 1
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Get a list of training and employee ids to associate the data with
        $trainings = Training::all()->pluck('id');
        $employees = Employee::all()->pluck('employee_id');
        $users = User::all()->pluck('user_id'); // for approved_by field

        // Create 100 training invitees
        foreach (range(1, 100) as $index) {
            DB::table('training_invitees')->insert([
                'employee_id' => $employees->random(),
                'training_id' => $trainings->random(),
                'approved' => (rand(0, 1) == 1), // Random approval (true/false)
                'approved_by' => $users->random(), // Random approved_by user
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create 100 training attendants
        foreach (range(1, 100) as $index) {
            DB::table('training_attendants')->insert([
                'employee_id' => $employees->random(),
                'training_id' => $trainings->random(),
                'approved' => (rand(0, 1) == 1), // Random approval (true/false)
                'approved_by' => $users->random(), // Random approved_by user
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
