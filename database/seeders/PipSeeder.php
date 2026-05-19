<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PipSeeder extends Seeder
{
    public function run(): void
    {
        $time = Carbon::now();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate PIP tables
        DB::table('pip_review_schedules')->truncate();
        DB::table('pip_support_resources')->truncate();
        DB::table('pip_goals')->truncate();
        DB::table('pip_concerns')->truncate();
        DB::table('pip_plans')->truncate();

        // Get first employee as a sample
        $firstEmployee = DB::table('employee')->first();
        if (!$firstEmployee) {
            $this->command->info('No employees found. Skipping PIP seeding.');
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return;
        }

        // Get first appraisal if available
        $firstAppraisal = DB::table('performance_appraisals')->first();

        // Sample PIP plan linked to first employee
        $pipId = DB::table('pip_plans')->insertGetId([
            'employee_id' => $firstEmployee->employee_id,
            'position' => 'Accountant',
            'department_id' => $firstEmployee->department_id,
            'supervisor_id' => $firstEmployee->supervisor_id,
            'hr_manager_id' => null,
            'designation_id' => $firstEmployee->designation_id ?? null,
            'appraisal_id' => $firstAppraisal ? $firstAppraisal->appraisal_id : null,
            'plan_period_start' => $time->copy()->subDays(30)->format('Y-m-d'),
            'plan_period_end' => $time->copy()->addDays(30)->format('Y-m-d'),
            'purpose' => 'Employee performance fell below threshold on financial accuracy and reporting KPIs. This PIP outlines improvement targets and support mechanisms.',
            'trigger_score' => 62.50,
            'trigger_type' => 'automatic',
            'status' => 'active',
            'outcome' => 'pending',
            'outcome_notes' => null,
            'employee_acknowledged' => true,
            'employee_ack_date' => $time->copy()->subDays(28),
            'supervisor_signed' => true,
            'supervisor_sign_date' => $time->copy()->subDays(27),
            'hr_validated' => true,
            'hr_validation_date' => $time->copy()->subDays(26),
            'is_locked' => false,
            'created_by' => $firstEmployee->employee_id,
            'created_at' => $time,
            'updated_at' => $time,
        ]);

        // Generate bi-weekly review schedules
        $stages = [
            ['First Review', 2],
            ['Second Review', 4],
            ['Third Review', 6],
            ['Fourth Review', 8],
            ['Final Review', 10],
        ];

        foreach ($stages as $index => $stage) {
            DB::table('pip_review_schedules')->insert([
                'pip_id' => $pipId,
                'review_stage' => $stage[0],
                'stage_number' => $index + 1,
                'scheduled_date' => $time->copy()->subDays(30)->addWeeks($stage[1])->format('Y-m-d'),
                'status' => $index < 1 ? 'completed' : 'pending',
                'comments' => $index < 1 ? 'Employee showed initial progress on daily entries accuracy.' : null,
                'findings' => $index < 1 ? 'Positive trend observed in first week.' : null,
                'conducted_by' => $index < 1 ? $firstEmployee->supervisor_id : null,
                'conducted_at' => $index < 1 ? $time->copy()->subDays(16) : null,
                'created_at' => $time,
                'updated_at' => $time,
            ]);
        }

        // Insert sample concerns - using actual table structure
        $firstGoal = DB::table('performance_goals')->first();
        $firstBehavioralItem = DB::table('performance_behavioral_items')->first();
        $firstScore = DB::table('performance_appraisal_scores')->first();

        if ($firstGoal || $firstBehavioralItem) {
            DB::table('pip_concerns')->insert([
                'pip_id' => $pipId,
                'goal_id' => $firstGoal ? $firstGoal->goal_id : null,
                'behavioral_item_id' => $firstBehavioralItem ? $firstBehavioralItem->behavioral_item_id : null,
                'appraisal_score_id' => $firstScore ? $firstScore->score_id : null,
                'description' => 'Below-target performance on financial accuracy metrics.',
                'actual_score' => 45.00,
                'target_score' => 80.00,
                'created_at' => $time,
                'updated_at' => $time,
            ]);
        }

        // Insert sample goals
        DB::table('pip_goals')->insert([
            [
                'pip_id' => $pipId,
                'objective' => 'Improve daily entry accuracy',
                'action_required' => 'Double-check all entries before posting; use checklist',
                'target_kpi' => '>= 90% error-free entries',
                'deadline' => $time->copy()->addDays(15)->format('Y-m-d'),
                'status' => 'in_progress',
                'progress_notes' => 'Checklist implemented; initial results encouraging.',
                'created_at' => $time,
                'updated_at' => $time,
            ],
            [
                'pip_id' => $pipId,
                'objective' => 'Submit reports on time',
                'action_required' => 'Block calendar time every Friday for report preparation',
                'target_kpi' => '100% on-time submission',
                'deadline' => $time->copy()->addDays(20)->format('Y-m-d'),
                'status' => 'pending',
                'progress_notes' => null,
                'created_at' => $time,
                'updated_at' => $time,
            ],
        ]);

        // Insert sample support resources
        DB::table('pip_support_resources')->insert([
            [
                'pip_id' => $pipId,
                'support_type' => 'training',
                'description' => 'Advanced bookkeeping and reconciliation workshop',
                'provider' => 'external',
                'scheduled_date' => $time->copy()->addDays(5)->format('Y-m-d'),
                'status' => 'planned',
                'created_at' => $time,
                'updated_at' => $time,
            ],
            [
                'pip_id' => $pipId,
                'support_type' => 'mentorship',
                'description' => 'Weekly 1-on-1 mentoring sessions with senior accountant',
                'provider' => 'supervisor',
                'scheduled_date' => $time->copy()->addDays(7)->format('Y-m-d'),
                'status' => 'in_progress',
                'created_at' => $time,
                'updated_at' => $time,
            ],
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('PIP sample data seeded successfully.');
    }
}
