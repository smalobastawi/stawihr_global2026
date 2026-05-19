<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('job_requisitions', function (Blueprint $table) {
            // 1. POSITION DETAILS - additional fields
            $table->string('work_location', 255)->nullable()->after('location_id');
            $table->date('proposed_start_date')->nullable()->after('required_by_date');

            // 2. REASON FOR REQUISITION
            $table->string('requisition_type', 50)->default('new_position')->after('reason_for_requisition'); // new_position, replacement
            $table->string('replaced_employee_name', 200)->nullable()->after('requisition_type');
            $table->string('replacement_reason', 50)->nullable()->after('replaced_employee_name'); // resignation, termination, transfer, other
            $table->string('replacement_reason_other', 255)->nullable()->after('replacement_reason');

            // 3. JOB DETAILS
            $table->text('key_responsibilities')->nullable()->after('job_description');

            // 4. REQUIREMENTS
            $table->text('minimum_qualifications')->nullable()->after('job_requirements');
            $table->string('experience_required', 255)->nullable()->after('minimum_qualifications');
            $table->text('skills_competencies')->nullable()->after('experience_required');

            // 5. COMPENSATION DETAILS
            $table->text('other_benefits')->nullable()->after('maximum_salary');

            // 6. JUSTIFICATION FOR HIRE
            $table->text('justification_for_hire')->nullable()->after('budget_justification');

            // 7. APPROVALS - already have approved_by, approved_at, approval_comments
            // Adding signature tracking fields for approval workflow
            $table->text('hod_approval_signature')->nullable()->after('approval_comments');
            $table->date('hod_approval_date')->nullable()->after('hod_approval_signature');
            $table->text('hr_approval_signature')->nullable()->after('hod_approval_date');
            $table->date('hr_approval_date')->nullable()->after('hr_approval_signature');
            $table->text('finance_approval_signature')->nullable()->after('hr_approval_date');
            $table->date('finance_approval_date')->nullable()->after('finance_approval_signature');
            $table->text('md_approval_signature')->nullable()->after('finance_approval_date');
            $table->date('md_approval_date')->nullable()->after('md_approval_signature');

            // 8. HR USE ONLY
            $table->date('date_received')->nullable()->after('md_approval_date');
            $table->string('approved_salary_range', 255)->nullable()->after('date_received');
            $table->string('hr_recruitment_method', 50)->nullable()->after('approved_salary_range'); // internal, external
            $table->text('hr_remarks')->nullable()->after('hr_recruitment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_requisitions', function (Blueprint $table) {
            $table->dropColumn([
                'work_location',
                'proposed_start_date',
                'requisition_type',
                'replaced_employee_name',
                'replacement_reason',
                'replacement_reason_other',
                'key_responsibilities',
                'minimum_qualifications',
                'experience_required',
                'skills_competencies',
                'other_benefits',
                'justification_for_hire',
                'hod_approval_signature',
                'hod_approval_date',
                'hr_approval_signature',
                'hr_approval_date',
                'finance_approval_signature',
                'finance_approval_date',
                'md_approval_signature',
                'md_approval_date',
                'date_received',
                'approved_salary_range',
                'hr_recruitment_method',
                'hr_remarks',
            ]);
        });
    }
};
