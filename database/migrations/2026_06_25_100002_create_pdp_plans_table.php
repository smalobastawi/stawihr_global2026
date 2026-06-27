<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdp_plans', function (Blueprint $table) {
            $table->id('pdp_plan_id');
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('employee_id')->on('employee')->onDelete('cascade');
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->foreign('supervisor_id')->references('employee_id')->on('employee')->onDelete('set null');
            $table->unsignedInteger('department_id')->nullable();
            $table->foreign('department_id')->references('department_id')->on('department')->onDelete('set null');
            $table->unsignedInteger('designation_id')->nullable();
            $table->foreign('designation_id')->references('designation_id')->on('designation')->onDelete('set null');

            $table->string('plan_title');
            $table->unsignedSmallInteger('plan_year');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('review_frequency', ['quarterly', 'bi_annually', 'annually'])->default('quarterly');
            $table->text('development_focus')->nullable();
            $table->text('career_aspirations')->nullable();

            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft');
            $table->boolean('employee_acknowledged')->default(false);
            $table->timestamp('employee_ack_date')->nullable();
            $table->boolean('supervisor_approved')->default(false);
            $table->timestamp('supervisor_approve_date')->nullable();
            $table->boolean('hr_reviewed')->default(false);
            $table->timestamp('hr_review_date')->nullable();
            $table->text('overall_summary')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('employee_id')->on('employee')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['employee_id', 'plan_year'], 'pdp_plans_employee_year_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdp_plans');
    }
};
