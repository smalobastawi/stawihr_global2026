<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PerformanceSeeder extends Seeder
{
    public function run(): void
    {
        $time = Carbon::now();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate existing tables (in correct order due to foreign keys)
        DB::table('performance_goals')->truncate();
        DB::table('performance_focus_areas')->truncate();
        DB::table('performance_rating_scales')->truncate();

        // Insert rating scales
        DB::table('performance_rating_scales')->insert([
            ['points' => 5, 'rating_label' => 'Outstanding', 'description' => 'Exceptional performance exceeding all expectations', 'definition' => 'Consistently exceeds all performance expectations and demonstrates exceptional contribution to organizational goals.', 'score_range' => '90% - 100%', 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['points' => 4, 'rating_label' => 'Exceeds Expectations', 'description' => 'Performance exceeds expectations in most areas', 'definition' => 'Regularly exceeds performance standards and makes valuable contributions beyond the role requirements.', 'score_range' => '80% - 89%', 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['points' => 3, 'rating_label' => 'Meets Expectations', 'description' => 'Performance meets the required standards', 'definition' => 'Consistently meets performance standards and fulfills all role requirements satisfactorily.', 'score_range' => '60% - 79%', 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['points' => 2, 'rating_label' => 'Needs Improvement', 'description' => 'Performance below expected standards', 'definition' => 'Performance is below expected standards in some key areas. Improvement plan required.', 'score_range' => '40% - 59%', 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['points' => 1, 'rating_label' => 'Unsatisfactory', 'description' => 'Performance significantly below standards', 'definition' => 'Performance is significantly below required standards. Immediate improvement required.', 'score_range' => '0% - 39%', 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
        ]);

        // Insert sample focus areas for Finance / Accountant
        DB::table('performance_focus_areas')->insert([
            [
                'focus_area_name' => 'Financial Accuracy',
                'weight' => 40.00,
                'description' => 'Error-free entries, reconciliations, and financial reporting accuracy.',
                'department_id' => null,
                'designation_id' => null,
                'is_active' => 1,
                'created_at' => $time,
                'updated_at' => $time,
            ],
            [
                'focus_area_name' => 'Reporting',
                'weight' => 25.00,
                'description' => 'Timely submission and quality of reports.',
                'department_id' => null,
                'designation_id' => null,
                'is_active' => 1,
                'created_at' => $time,
                'updated_at' => $time,
            ],
            [
                'focus_area_name' => 'Compliance',
                'weight' => 20.00,
                'description' => 'Tax compliance and policy adherence.',
                'department_id' => null,
                'designation_id' => null,
                'is_active' => 1,
                'created_at' => $time,
                'updated_at' => $time,
            ],
            [
                'focus_area_name' => 'Discipline',
                'weight' => 15.00,
                'description' => 'Attendance and conduct.',
                'department_id' => null,
                'designation_id' => null,
                'is_active' => 1,
                'created_at' => $time,
                'updated_at' => $time,
            ],
        ]);

        // Get inserted focus area IDs
        $catFinancialAccuracy = DB::table('performance_focus_areas')->where('focus_area_name', 'Financial Accuracy')->first()->focus_area_id;
        $catReporting = DB::table('performance_focus_areas')->where('focus_area_name', 'Reporting')->first()->focus_area_id;
        $catCompliance = DB::table('performance_focus_areas')->where('focus_area_name', 'Compliance')->first()->focus_area_id;
        $catDiscipline = DB::table('performance_focus_areas')->where('focus_area_name', 'Discipline')->first()->focus_area_id;

        // Insert Goals for Finance
        DB::table('performance_goals')->insert([
            ['focus_area_id' => $catFinancialAccuracy, 'strategic_objective' => 'Ensure financial data accuracy', 'performance_metric' => 'Error-free entries', 'performance_target' => '100% accuracy in daily bookkeeping entries with zero errors', 'key_initiatives' => 'Double-check all entries, use automated validation tools', 'itemized_weighting' => 20.00, 'sort_order' => 1, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['focus_area_id' => $catFinancialAccuracy, 'strategic_objective' => 'Maintain account reconciliations', 'performance_metric' => 'Reconciliation accuracy', 'performance_target' => 'Complete monthly bank and ledger reconciliations by 5th of each month', 'key_initiatives' => 'Weekly reconciliation checks, automated matching tools', 'itemized_weighting' => 20.00, 'sort_order' => 2, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['focus_area_id' => $catReporting, 'strategic_objective' => 'Deliver timely reports', 'performance_metric' => 'Timely report submission', 'performance_target' => 'Submit all reports before the deadline', 'key_initiatives' => 'Set internal deadlines, use report templates', 'itemized_weighting' => 12.50, 'sort_order' => 1, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['focus_area_id' => $catReporting, 'strategic_objective' => 'Improve report quality', 'performance_metric' => 'Report quality', 'performance_target' => 'Reports should demonstrate clarity, completeness and insight', 'key_initiatives' => 'Add executive summary, use visual charts and graphs', 'itemized_weighting' => 12.50, 'sort_order' => 2, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['focus_area_id' => $catCompliance, 'strategic_objective' => 'Maintain tax compliance', 'performance_metric' => 'Tax compliance', 'performance_target' => '100% on-time filing and remittance of all tax obligations', 'key_initiatives' => 'Calendar alerts, regular compliance audits', 'itemized_weighting' => 10.00, 'sort_order' => 1, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['focus_area_id' => $catCompliance, 'strategic_objective' => 'Follow financial policies', 'performance_metric' => 'Policy adherence', 'performance_target' => 'Zero violations of internal financial policies', 'key_initiatives' => 'Regular policy reviews, training sessions', 'itemized_weighting' => 10.00, 'sort_order' => 2, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['focus_area_id' => $catDiscipline, 'strategic_objective' => 'Maintain attendance standards', 'performance_metric' => 'Attendance', 'performance_target' => 'Regular attendance and punctuality with less than 3% absenteeism', 'key_initiatives' => 'Plan ahead, notify supervisors early for any absences', 'itemized_weighting' => 7.50, 'sort_order' => 1, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['focus_area_id' => $catDiscipline, 'strategic_objective' => 'Demonstrate professional conduct', 'performance_metric' => 'Conduct', 'performance_target' => 'Demonstrate professional behavior and teamwork consistently', 'key_initiatives' => 'Active collaboration, conflict resolution training', 'itemized_weighting' => 7.50, 'sort_order' => 2, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
        ]);

        // Sample focus areas for IT
        DB::table('performance_focus_areas')->insert([
            [
                'focus_area_name' => 'Technical Performance',
                'weight' => 40.00,
                'description' => 'Technical delivery, code quality, and system maintenance.',
                'department_id' => null,
                'designation_id' => null,
                'is_active' => 1,
                'created_at' => $time,
                'updated_at' => $time,
            ],
            [
                'focus_area_name' => 'Quality & Accuracy',
                'weight' => 20.00,
                'description' => 'Quality of deliverables and accuracy.',
                'department_id' => null,
                'designation_id' => null,
                'is_active' => 1,
                'created_at' => $time,
                'updated_at' => $time,
            ],
            [
                'focus_area_name' => 'Timeliness',
                'weight' => 15.00,
                'description' => 'Meeting deadlines and SLA adherence.',
                'department_id' => null,
                'designation_id' => null,
                'is_active' => 1,
                'created_at' => $time,
                'updated_at' => $time,
            ],
            [
                'focus_area_name' => 'Communication',
                'weight' => 15.00,
                'description' => 'Stakeholder communication and documentation.',
                'department_id' => null,
                'designation_id' => null,
                'is_active' => 1,
                'created_at' => $time,
                'updated_at' => $time,
            ],
            [
                'focus_area_name' => 'Behavioral Expectations',
                'weight' => 10.00,
                'description' => 'Attendance and conduct.',
                'department_id' => null,
                'designation_id' => null,
                'is_active' => 1,
                'created_at' => $time,
                'updated_at' => $time,
            ],
        ]);

        // IT Goals
        $catTechnical = DB::table('performance_focus_areas')->where('focus_area_name', 'Technical Performance')->first()->focus_area_id;
        $catQuality = DB::table('performance_focus_areas')->where('focus_area_name', 'Quality & Accuracy')->first()->focus_area_id;
        $catTimeliness = DB::table('performance_focus_areas')->where('focus_area_name', 'Timeliness')->first()->focus_area_id;
        $catCommunication = DB::table('performance_focus_areas')->where('focus_area_name', 'Communication')->first()->focus_area_id;
        $catBehavioral = DB::table('performance_focus_areas')->where('focus_area_name', 'Behavioral Expectations')->first()->focus_area_id;

        DB::table('performance_goals')->insert([
            ['focus_area_id' => $catTechnical, 'strategic_objective' => 'Complete development tasks', 'performance_metric' => 'Task completion', 'performance_target' => 'Complete at least 90% of assigned tasks per sprint', 'key_initiatives' => 'Daily standups, task breakdown, pair programming', 'itemized_weighting' => 20.00, 'sort_order' => 1, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['focus_area_id' => $catTechnical, 'strategic_objective' => 'Maintain system uptime', 'performance_metric' => 'System uptime contribution', 'performance_target' => 'Achieve 99.9% system uptime through proactive maintenance', 'key_initiatives' => 'Monitoring alerts, incident response drills', 'itemized_weighting' => 20.00, 'sort_order' => 2, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['focus_area_id' => $catQuality, 'strategic_objective' => 'Deliver quality releases', 'performance_metric' => 'Bug-free releases', 'performance_target' => 'Less than 2 critical bugs per release', 'key_initiatives' => 'Automated testing, code reviews, QA process', 'itemized_weighting' => 10.00, 'sort_order' => 1, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['focus_area_id' => $catQuality, 'strategic_objective' => 'Improve code quality', 'performance_metric' => 'Code review quality', 'performance_target' => 'Average peer review score of 4.5/5 or higher', 'key_initiatives' => 'Follow coding standards, refactor legacy code', 'itemized_weighting' => 10.00, 'sort_order' => 2, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['focus_area_id' => $catTimeliness, 'strategic_objective' => 'Meet sprint deadlines', 'performance_metric' => 'On-time delivery', 'performance_target' => 'Deliver all committed features by sprint end', 'key_initiatives' => 'Sprint planning, velocity tracking, risk management', 'itemized_weighting' => 7.50, 'sort_order' => 1, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['focus_area_id' => $catTimeliness, 'strategic_objective' => 'Resolve tickets on time', 'performance_metric' => 'SLA adherence', 'performance_target' => 'Resolve 95% of tickets within SLA timeframes', 'key_initiatives' => 'Priority queue, ticket monitoring, escalation process', 'itemized_weighting' => 7.50, 'sort_order' => 2, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['focus_area_id' => $catCommunication, 'strategic_objective' => 'Maintain documentation', 'performance_metric' => 'Documentation quality', 'performance_target' => 'Up-to-date technical docs and runbooks for all systems', 'key_initiatives' => 'Documentation sprints, wiki maintenance', 'itemized_weighting' => 7.50, 'sort_order' => 1, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['focus_area_id' => $catCommunication, 'strategic_objective' => 'Keep stakeholders informed', 'performance_metric' => 'Stakeholder updates', 'performance_target' => 'Weekly status updates to all project stakeholders', 'key_initiatives' => 'Weekly reports, demo sessions, feedback loops', 'itemized_weighting' => 7.50, 'sort_order' => 2, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['focus_area_id' => $catBehavioral, 'strategic_objective' => 'Maintain attendance standards', 'performance_metric' => 'Attendance', 'performance_target' => 'Regular attendance and punctuality with less than 3% absenteeism', 'key_initiatives' => 'Plan ahead, notify supervisors early for any absences', 'itemized_weighting' => 5.00, 'sort_order' => 1, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['focus_area_id' => $catBehavioral, 'strategic_objective' => 'Demonstrate professional conduct', 'performance_metric' => 'Conduct', 'performance_target' => 'Demonstrate professional behavior and teamwork consistently', 'key_initiatives' => 'Active collaboration, conflict resolution training', 'itemized_weighting' => 5.00, 'sort_order' => 2, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
        ]);

        // Sample Logistics focus areas
        DB::table('performance_focus_areas')->insert([
            [
                'focus_area_name' => 'Delivery Efficiency',
                'weight' => 40.00,
                'description' => 'On-time deliveries and route optimization.',
                'department_id' => null,
                'designation_id' => null,
                'is_active' => 1,
                'created_at' => $time,
                'updated_at' => $time,
            ],
            [
                'focus_area_name' => 'Inventory Accuracy',
                'weight' => 25.00,
                'description' => 'Stock accuracy and warehouse management.',
                'department_id' => null,
                'designation_id' => null,
                'is_active' => 1,
                'created_at' => $time,
                'updated_at' => $time,
            ],
            [
                'focus_area_name' => 'Safety & Compliance',
                'weight' => 20.00,
                'description' => 'Safety protocols and regulatory compliance.',
                'department_id' => null,
                'designation_id' => null,
                'is_active' => 1,
                'created_at' => $time,
                'updated_at' => $time,
            ],
            [
                'focus_area_name' => 'Behavioral Expectations',
                'weight' => 15.00,
                'description' => 'Attendance and conduct.',
                'department_id' => null,
                'designation_id' => null,
                'is_active' => 1,
                'created_at' => $time,
                'updated_at' => $time,
            ],
        ]);

        $catDelivery = DB::table('performance_focus_areas')->where('focus_area_name', 'Delivery Efficiency')->first()->focus_area_id;
        $catInventory = DB::table('performance_focus_areas')->where('focus_area_name', 'Inventory Accuracy')->first()->focus_area_id;
        $catSafety = DB::table('performance_focus_areas')->where('focus_area_name', 'Safety & Compliance')->first()->focus_area_id;
        $catBehavioralLogistics = DB::table('performance_focus_areas')->where('focus_area_name', 'Behavioral Expectations')->skip(1)->first()->focus_area_id;

        DB::table('performance_goals')->insert([
            ['focus_area_id' => $catDelivery, 'strategic_objective' => 'Achieve on-time deliveries', 'performance_metric' => 'On-time delivery rate', 'performance_target' => '95% of deliveries made on or before scheduled time', 'key_initiatives' => 'Route optimization software, real-time tracking', 'itemized_weighting' => 20.00, 'sort_order' => 1, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['focus_area_id' => $catDelivery, 'strategic_objective' => 'Optimize delivery routes', 'performance_metric' => 'Route efficiency', 'performance_target' => 'Reduce fuel consumption by 10% through route optimization', 'key_initiatives' => 'GPS tracking, fuel monitoring, driver training', 'itemized_weighting' => 20.00, 'sort_order' => 2, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['focus_area_id' => $catInventory, 'strategic_objective' => 'Maintain accurate inventory', 'performance_metric' => 'Stock accuracy', 'performance_target' => '99.5% inventory record accuracy', 'key_initiatives' => 'Cycle counts, barcode scanning, inventory audits', 'itemized_weighting' => 12.50, 'sort_order' => 1, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['focus_area_id' => $catInventory, 'strategic_objective' => 'Organize warehouse', 'performance_metric' => 'Warehouse organization', 'performance_target' => 'Maintain cleanliness and organization standards', 'key_initiatives' => '5S methodology, regular inspections, storage optimization', 'itemized_weighting' => 12.50, 'sort_order' => 2, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['focus_area_id' => $catSafety, 'strategic_objective' => 'Prevent safety incidents', 'performance_metric' => 'Incident-free days', 'performance_target' => '90+ consecutive days without safety incidents', 'key_initiatives' => 'Daily safety briefings, equipment checks', 'itemized_weighting' => 10.00, 'sort_order' => 1, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['focus_area_id' => $catSafety, 'strategic_objective' => 'Follow regulations', 'performance_metric' => 'Regulatory compliance', 'performance_target' => '100% adherence to transport and safety regulations', 'key_initiatives' => 'Compliance training, vehicle inspections, permit tracking', 'itemized_weighting' => 10.00, 'sort_order' => 2, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['focus_area_id' => $catBehavioralLogistics, 'strategic_objective' => 'Maintain attendance standards', 'performance_metric' => 'Attendance', 'performance_target' => 'Regular attendance and punctuality with less than 3% absenteeism', 'key_initiatives' => 'Plan ahead, notify supervisors early for any absences', 'itemized_weighting' => 7.50, 'sort_order' => 1, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
            ['focus_area_id' => $catBehavioralLogistics, 'strategic_objective' => 'Demonstrate professional conduct', 'performance_metric' => 'Conduct', 'performance_target' => 'Demonstrate professional behavior and teamwork consistently', 'key_initiatives' => 'Active collaboration, conflict resolution training', 'itemized_weighting' => 7.50, 'sort_order' => 2, 'is_active' => 1, 'created_at' => $time, 'updated_at' => $time],
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Performance focus areas, goals, and rating scales seeded successfully.');
    }
}
