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
        Schema::create('projects_to_employee_payroll_allocation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('project_id'); // Changed from program_id
            $table->decimal('percentage_allocated', 5, 2);
            $table->date('allocation_start_date');
            $table->date('allocation_end_date');
            $table->string('status')->default('active');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by')->nullable();

            // Foreign key constraints
            $table->foreign('employee_id')->references('employee_id')->on('employee')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade'); // References projects table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects_to_employee_payroll_allocation');
    }
};