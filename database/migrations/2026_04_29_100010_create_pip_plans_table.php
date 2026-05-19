<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pip_plans', function (Blueprint $table) {
            $table->id('pip_id');
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('employee_id')->on('employee')->onDelete('cascade');
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->foreign('supervisor_id')->references('employee_id')->on('employee')->onDelete('set null');
            $table->unsignedBigInteger('hr_manager_id')->nullable();
            $table->foreign('hr_manager_id')->references('employee_id')->on('employee')->onDelete('set null');
            $table->unsignedBigInteger('appraisal_id')->nullable();
            $table->foreign('appraisal_id')->references('appraisal_id')->on('performance_appraisals')->onDelete('set null');

            $table->string('position')->nullable();
            $table->unsignedInteger('department_id')->nullable();
            $table->foreign('department_id')->references('department_id')->on('department')->onDelete('set null');
            $table->unsignedInteger('designation_id')->nullable();
            $table->foreign('designation_id')->references('designation_id')->on('designation')->onDelete('set null');

            $table->date('plan_period_start');
            $table->date('plan_period_end');
            $table->text('purpose'); // Reason for PIP
            $table->decimal('trigger_score', 5, 2)->nullable(); // Score that triggered PIP
            $table->enum('trigger_type', ['automatic', 'manual_supervisor', 'manual_hr'])->default('automatic');

            $table->enum('status', ['draft', 'active', 'in_review', 'completed', 'extended', 'cancelled'])->default('draft');
            $table->enum('outcome', ['pending', 'successful_completion', 'partial_improvement', 'failure'])->default('pending');
            $table->text('outcome_notes')->nullable();

            // Sign-offs
            $table->boolean('employee_acknowledged')->default(false);
            $table->timestamp('employee_ack_date')->nullable();
            $table->boolean('supervisor_signed')->default(false);
            $table->timestamp('supervisor_sign_date')->nullable();
            $table->boolean('hr_validated')->default(false);
            $table->timestamp('hr_validation_date')->nullable();
            $table->boolean('is_locked')->default(false);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('employee_id')->on('employee')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pip_plans');
    }
};
