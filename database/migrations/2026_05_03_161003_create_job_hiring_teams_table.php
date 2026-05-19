<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_hiring_teams', function (Blueprint $table) {
            $table->id('hiring_team_id');
            $table->unsignedBigInteger('job_requisition_id');
            $table->unsignedInteger('employee_id');

            // Role in the hiring team
            $table->string('role', 50)->default('interviewer')->comment('hiring_manager, interviewer, hr_business_partner, recruiter');
            $table->boolean('is_primary_hiring_manager')->default(false);

            // Responsibilities
            $table->boolean('can_screen_candidates')->default(true);
            $table->boolean('can_conduct_interviews')->default(true);
            $table->boolean('can_make_offers')->default(false);
            $table->boolean('can_approve_hire')->default(false);

            // Availability for interviews
            $table->json('interview_availability')->nullable()->comment('Store preferred interview days/times');

            $table->text('notes')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('added_by');

            $table->timestamps();
            $table->softDeletes();

            // Unique constraint to prevent duplicate team members
            $table->unique(['job_requisition_id', 'employee_id']);

            // Indexes (without foreign keys to avoid data type issues)
            $table->index(['job_requisition_id', 'status']);
            $table->index(['employee_id', 'role']);
            $table->index(['added_by']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_hiring_teams');
    }
};
