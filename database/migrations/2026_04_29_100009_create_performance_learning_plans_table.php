<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_learning_plans', function (Blueprint $table) {
            $table->id('learning_plan_id');
            $table->unsignedBigInteger('appraisal_id');
            $table->foreign('appraisal_id')->references('appraisal_id')->on('performance_appraisals')->onDelete('cascade');

            $table->string('course_title');
            $table->date('due_date')->nullable();
            $table->string('learning_hours')->nullable(); // e.g. "12hrs"
            $table->enum('mid_year_status', ['not_started', 'in_progress', 'completed'])->default('not_started');
            $table->enum('end_year_status', ['not_started', 'in_progress', 'completed'])->default('not_started');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_learning_plans');
    }
};
