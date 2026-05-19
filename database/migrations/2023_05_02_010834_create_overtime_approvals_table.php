<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_overtime_approvals', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('month');
            $table->string('national_id');
            $table->foreignId('employee_id')->constrained('employee', 'employee_id')->onUpdate('cascade')->restrictOnDelete();

            $table->string('department_id');
            $table->string('approved_over_time');
            $table->dateTime('time_in')->nullable();
            $table->dateTime('time_out')->nullable();
            $table->string('working_time')->nullable();
            $table->string('workingHours')->nullable();
            $table->string('total_time_worked')->nullable();
            $table->string('is_late')->nullable();
            $table->integer('late_time')->nullable();
            $table->integer('over_time')->nullable();
            $table->string('approval_status')->nullable();
            $table->string('presence_status')->comment('PRESENT,ABSENT,OFF,AWP, AL, ML, SICK, PL, CL, Training');
            $table->string('entry_type')->nullable();
            $table->string('work_shift_id')->nullable();
            $table->string('employee_type')->nullable();
            $table->string('attendance_entry_id')->nullable();

            //
            $table->integer('stage1_approval_status')->default(0);
            $table->integer('stage2_approval_status')->default(0);
            $table->integer('stage3_approval_status')->default(0);

            $table->foreignId('stage1_approved_by')->nullable()->constrained('employee', 'employee_id')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('stage2_approved_by')->nullable()->constrained('employee', 'employee_id')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('stage3_approved_by')->nullable()->constrained('employee', 'employee_id')->onUpdate('cascade')->onDelete('cascade');

            $table->string('stage1_approval_comments')->nullable();
            $table->string('stage2_approval_comments')->nullable();
            $table->string('stage3_approval_comments')->nullable();

            $table->dateTime('stage1_approval_date')->nullable();
            $table->dateTime('stage2_approval_date')->nullable();
            $table->dateTime('stage3_approval_date')->nullable();

            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            //
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('overtime_approvals');
    }
};
