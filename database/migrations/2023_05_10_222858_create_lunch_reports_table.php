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
        Schema::create('lunch_reports', function (Blueprint $table) {
            $table->id();
            $table->string('national_id');
            $table->integer('employee_id');
            $table->string('payroll_number');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('department_id');
            $table->date('date');
            $table->string('month');
            $table->dateTime('lunch_checkin_time');
            $table->string('sensor_id');
            $table->integer('created_by')->default(0);
            $table->integer('employee_type')->default(0);
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
        Schema::dropIfExists('lunch_reports');
    }
};
