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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('month');
            $table->string('national_id');
            $table->foreignId('employee_id')->constrained('employee', 'employee_id')->onUpdate('cascade')->restrictOnDelete();

            $table->string('department_id');
            $table->dateTime('time_in')->nullable();
            $table->dateTime('time_out')->nullable();
            $table->dateTime('lunch_checkin')->nullable();
            $table->string('working_time')->nullable();
            $table->string('workingHours')->nullable();
            $table->string('total_time_worked')->nullable();
            $table->string('is_late')->nullable();
            $table->integer('late_time')->nullable();
            $table->integer('over_time')->nullable();
            $table->string('approval_status')->nullable();
            $table->string('presence_status')->comment('PRESENT,ABSENT,OFF,AWP, AL, ML, SICK, PL, CL, Training');
            $table->string('sensor_id')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('approved_by')->nullable();
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
        Schema::dropIfExists('attendances');
    }
};
