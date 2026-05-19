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
        Schema::create('employee_movements', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id')->unsigned();
            $table->string('payroll_number')->nullable();
            $table->integer('current_department')->unsigned();
            $table->integer('current_designation')->unsigned();
            $table->integer('current_pay_grade');
            $table->integer('current_salary');

            $table->integer('current_section_id');
            $table->integer('current_group_id');
            $table->integer('current_work_shift_id');
            $table->integer('current_branch');
            $table->integer('current_employee_type')->nullable();

            $table->integer('new_pay_grade')->unsigned();
            $table->integer('new_salary')->nullable();
            $table->integer('new_department_id')->nullable();
            $table->integer('new_designation_id')->nullable();
            $table->integer('new_employee_status')->nullable();
            $table->date('movement_date');

            $table->integer('new_section_id')->nullable();
            $table->integer('new_group_id')->nullable();
            $table->integer('new_work_shift_id')->nullable();
            $table->integer('new_branch')->nullable();
            $table->integer('new_employee_type')->nullable();

            $table->text('description')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->tinyInteger('status')->default('1');
            $table->timestamps();
            $table->softDeletes();

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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_movements');
    }
};
