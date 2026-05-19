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
        Schema::create('weekly_holiday_departments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('holiday_id')->foreign('weekly_holiday_id')->references('weekly_holiday_id')->on('weekly_holiday')->cascadeOnUpdate()->nullOnDelete();
            $table->unsignedBigInteger('department_id')->foreign('department_id')->references('department_id')->on('departments')->cascadeOnUpdate()->nullOnDelete();
 
            
            $table->timestamps();

            $table->unique(['holiday_id', 'department_id'], 'whd_holiday_dept_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('weekly_holiday_departments');
    }
};
