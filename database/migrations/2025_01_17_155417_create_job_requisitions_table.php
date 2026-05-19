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
        Schema::create('job_requisitions', function (Blueprint $table) {
            $table->id('job_requisition_id');
            $table->string('requisition_number')->unique();
            $table->string('position_title', 200);
            $table->text('job_description');
            $table->text('job_requirements');
            $table->integer('number_of_positions')->default(1);
            $table->string('job_type', 50);
            $table->string('employment_type', 50);
            $table->integer('location_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->decimal('minimum_salary', 12, 2)->nullable();
            $table->decimal('maximum_salary', 12, 2)->nullable();
            $table->string('currency', 3)->default('KES');
            $table->date('required_by_date');
            $table->string('urgency_level', 20)->default('normal'); // low, normal, high, critical
            $table->text('reason_for_requisition');
            $table->text('budget_justification')->nullable();
            $table->string('reporting_manager', 100);
            $table->string('recruitment_source', 50)->default('internal'); // internal, external, both
            $table->tinyInteger('status')->default(0); // 0=Draft, 1=Pending Approval, 2=Approved, 3=Rejected, 4=Cancelled
            $table->unsignedBigInteger('requested_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_comments')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_converted_to_job')->default(false);
            $table->integer('converted_job_id')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->unsignedBigInteger('converted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('requested_by')->references('id')->on('user');
            $table->foreign('approved_by')->references('id')->on('user')->onDelete('set null');
            $table->foreign('converted_by')->references('id')->on('user')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_requisitions');
    }
};