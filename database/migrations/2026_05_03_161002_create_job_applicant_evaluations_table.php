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
        if (Schema::hasTable('job_applicant_evaluations')) {
            return;
        }

        Schema::create('job_applicant_evaluations', function (Blueprint $table) {
            $table->id('evaluation_id');
            $table->unsignedInteger('job_applicant_id');
            $table->unsignedBigInteger('evaluated_by');
            $table->unsignedBigInteger('job_requisition_id')->nullable();

            // Evaluation criteria
            $table->integer('education_score')->nullable()->comment('1-10 scale');
            $table->integer('experience_score')->nullable()->comment('1-10 scale');
            $table->integer('technical_skills_score')->nullable()->comment('1-10 scale');
            $table->integer('communication_score')->nullable()->comment('1-10 scale');
            $table->integer('cultural_fit_score')->nullable()->comment('1-10 scale');
            $table->integer('problem_solving_score')->nullable()->comment('1-10 scale');

            // Overall score (calculated)
            $table->decimal('overall_score', 4, 2)->nullable();

            // Evaluation details
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->text('notes')->nullable();
            $table->string('recommendation', 20)->nullable()->comment('hire, reject, maybe, second_interview');

            // Interview specific fields
            $table->unsignedInteger('interview_id')->nullable();
            $table->string('evaluation_stage', 30)->default('screening')->comment('screening, first_interview, second_interview, final');

            $table->timestamps();
            $table->softDeletes();

            // Indexes (without foreign keys to avoid data type issues)
            $table->index(['job_applicant_id', 'evaluation_stage'], 'jae_applicant_stage_idx');
            $table->index(['evaluated_by', 'created_at'], 'jae_evaluator_created_idx');
            $table->index(['job_requisition_id'], 'jae_requisition_idx');
            $table->index(['interview_id'], 'jae_interview_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_applicant_evaluations');
    }
};
