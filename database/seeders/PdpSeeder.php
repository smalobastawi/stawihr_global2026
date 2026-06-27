<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PdpSeeder extends Seeder
{
    public function run(): void
    {
        $time = Carbon::now();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('pdp_progress_entries')->truncate();
        DB::table('pdp_goals')->truncate();
        DB::table('pdp_plans')->truncate();
        DB::table('pdp_settings')->truncate();

        DB::table('pdp_settings')->insert([
            'company_id' => null,
            'default_review_frequency' => 'quarterly',
            'allow_employee_self_service' => true,
            'require_supervisor_approval' => true,
            'require_hr_review' => false,
            'policy_notes' => 'Staff should define SMART development goals and update progress each quarter. Supervisors review progress and provide feedback.',
            'created_at' => $time,
            'updated_at' => $time,
        ]);

        $firstEmployee = DB::table('employee')->first();
        if (!$firstEmployee) {
            $this->command->info('No employees found. Skipping PDP sample data seeding.');
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return;
        }

        $planId = DB::table('pdp_plans')->insertGetId([
            'employee_id' => $firstEmployee->employee_id,
            'supervisor_id' => $firstEmployee->supervisor_id,
            'department_id' => $firstEmployee->department_id,
            'designation_id' => $firstEmployee->designation_id ?? null,
            'plan_title' => $time->year . ' Professional Development Plan',
            'plan_year' => $time->year,
            'start_date' => $time->copy()->startOfYear()->format('Y-m-d'),
            'end_date' => $time->copy()->endOfYear()->format('Y-m-d'),
            'review_frequency' => 'quarterly',
            'development_focus' => 'Leadership, communication, and technical excellence.',
            'career_aspirations' => 'Progress toward a senior specialist role within two years.',
            'status' => 'active',
            'employee_acknowledged' => true,
            'employee_ack_date' => $time,
            'supervisor_approved' => true,
            'supervisor_approve_date' => $time,
            'hr_reviewed' => false,
            'hr_review_date' => null,
            'overall_summary' => null,
            'created_by' => $firstEmployee->employee_id,
            'created_at' => $time,
            'updated_at' => $time,
        ]);

        $goalId = DB::table('pdp_goals')->insertGetId([
            'pdp_plan_id' => $planId,
            'goal_title' => 'Complete advanced project management certification',
            'smart_objective' => 'Obtain PMP certification by December through structured study and practice exams.',
            'competency_area' => 'Project Management',
            'success_criteria' => 'Pass certification exam with required score.',
            'development_actions' => 'Attend training, complete mock exams, allocate weekly study time.',
            'resources_needed' => 'Training budget and study materials.',
            'target_completion_date' => $time->copy()->endOfYear()->format('Y-m-d'),
            'priority' => 'high',
            'status' => 'in_progress',
            'overall_progress' => 35,
            'sort_order' => 1,
            'created_at' => $time,
            'updated_at' => $time,
        ]);

        DB::table('pdp_progress_entries')->insert([
            'pdp_plan_id' => $planId,
            'pdp_goal_id' => $goalId,
            'review_frequency' => 'quarterly',
            'review_year' => $time->year,
            'review_quarter' => 1,
            'review_half' => null,
            'review_period_label' => 'Q1 ' . $time->year,
            'progress_percentage' => 35,
            'achievement_summary' => 'Completed first module of study program and two practice exams.',
            'challenges' => 'Limited time during peak project delivery.',
            'support_needed' => 'Protected study time on Fridays.',
            'next_steps' => 'Complete modules 2 and 3 before next quarter review.',
            'status' => 'submitted',
            'entered_by' => $firstEmployee->employee_id,
            'reviewed_by' => null,
            'supervisor_comments' => null,
            'submitted_at' => $time,
            'reviewed_at' => null,
            'created_at' => $time,
            'updated_at' => $time,
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('PDP sample data seeded successfully.');
    }
}
