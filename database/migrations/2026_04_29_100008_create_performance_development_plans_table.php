<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_development_plans', function (Blueprint $table) {
            $table->id('development_plan_id');
            $table->unsignedBigInteger('appraisal_id');
            $table->foreign('appraisal_id')->references('appraisal_id')->on('performance_appraisals')->onDelete('cascade');

            // Competency assessment
            $table->string('competency_name'); // e.g. "Defensive Driving skills"
            $table->string('expected_proficiency'); // e.g. "Expert", "Beginner", "Advanced"
            $table->text('smart_objective'); // What does the person need to do
            $table->decimal('self_rating', 3, 1)->nullable(); // Overall rating
            $table->text('self_comments')->nullable();
            $table->decimal('reviewer_rating', 3, 1)->nullable();
            $table->text('reviewer_comments')->nullable();
            $table->decimal('agreed_rating', 3, 1)->nullable();
            $table->text('competencies_of_focus')->nullable(); // For next review period

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_development_plans');
    }
};
