<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdp_progress_entries', function (Blueprint $table) {
            $table->id('pdp_progress_id');
            $table->unsignedBigInteger('pdp_plan_id');
            $table->foreign('pdp_plan_id')->references('pdp_plan_id')->on('pdp_plans')->onDelete('cascade');
            $table->unsignedBigInteger('pdp_goal_id')->nullable();
            $table->foreign('pdp_goal_id')->references('pdp_goal_id')->on('pdp_goals')->onDelete('cascade');

            $table->enum('review_frequency', ['quarterly', 'bi_annually', 'annually']);
            $table->unsignedSmallInteger('review_year');
            $table->unsignedTinyInteger('review_quarter')->nullable();
            $table->unsignedTinyInteger('review_half')->nullable();
            $table->string('review_period_label');

            $table->unsignedTinyInteger('progress_percentage')->default(0);
            $table->text('achievement_summary');
            $table->text('challenges')->nullable();
            $table->text('support_needed')->nullable();
            $table->text('next_steps')->nullable();

            $table->enum('status', ['draft', 'submitted', 'reviewed'])->default('draft');
            $table->unsignedBigInteger('entered_by')->nullable();
            $table->foreign('entered_by')->references('employee_id')->on('employee')->onDelete('set null');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->foreign('reviewed_by')->references('employee_id')->on('employee')->onDelete('set null');
            $table->text('supervisor_comments')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['pdp_goal_id', 'review_year', 'review_quarter', 'review_half'],
                'pdp_progress_goal_period_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdp_progress_entries');
    }
};
