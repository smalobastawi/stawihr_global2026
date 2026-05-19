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
        Schema::create('job_requisition_templates', function (Blueprint $table) {
            $table->id('template_id');
            $table->string('template_name', 100);
            $table->string('template_code', 20)->unique();
            $table->text('description')->nullable();

            // Position details
            $table->string('position_title', 100);
            $table->string('job_type', 50);
            $table->string('employment_type', 50);
            $table->unsignedInteger('department_id');
            $table->unsignedBigInteger('location_id');

            // Job details
            $table->text('job_description');
            $table->text('job_requirements')->nullable();
            $table->text('key_responsibilities')->nullable();
            $table->text('skills_competencies')->nullable();

            // Qualifications
            $table->text('minimum_qualifications')->nullable();
            $table->string('experience_required', 100)->nullable();

            // Default values
            $table->integer('default_number_of_positions')->default(1);
            $table->decimal('default_minimum_salary', 12, 2)->nullable();
            $table->decimal('default_maximum_salary', 12, 2)->nullable();
            $table->string('currency', 3)->default('KES');

            // Approval workflow settings
            $table->boolean('requires_hod_approval')->default(true);
            $table->boolean('requires_hr_approval')->default(true);
            $table->boolean('requires_finance_approval')->default(false);
            $table->boolean('requires_md_approval')->default(false);

            // Status
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('department_id')
                  ->references('department_id')
                  ->on('department')
                  ->onDelete('cascade');

            $table->foreign('location_id')
                  ->references('location_id')
                  ->on('location')
                  ->onDelete('cascade');

            $table->foreign('created_by')
                  ->references('id')
                  ->on('user')
                  ->onDelete('cascade');

            // Indexes
            $table->index(['department_id', 'status']);
            $table->index(['job_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_requisition_templates');
    }
};
