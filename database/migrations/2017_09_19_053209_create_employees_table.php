<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee', function (Blueprint $table) {
            $table->id('employee_id');
            $table->integer('user_id')->unsigned();
            $table->string('national_id');
            $table->string('staff_no')->nullable()->unique();
            $table->integer('department_id')->unsigned();
            $table->integer('designation_id')->unsigned();
            $table->integer('location_id')->unsigned()->nullable();
            $table->integer('supervisor_id')->nullable();
            $table->integer('work_shift_id')->unsigned();
            $table->integer('hourly_salaries_id')->unsigned()->nullable()->default(0);
            $table->string('email',50)->unique()->nullable();
            $table->string('first_name',30);
            $table->string('last_name',30)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('date_of_joining')->nullable();
            $table->date('date_of_leaving')->nullable();
            $table->string('gender',10)->nullable();
            $table->string('religion',50)->nullable();
            $table->string('marital_status',10)->nullable();
            $table->string('photo',250)->nullable();
            $table->text('address')->nullable();
            $table->text('emergency_contacts')->nullable();
            $table->integer('phone')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('permanent_status')->default(0);
            $table->integer('created_by');
            $table->integer('updated_by');
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
        Schema::dropIfExists('employee');
    }
}
