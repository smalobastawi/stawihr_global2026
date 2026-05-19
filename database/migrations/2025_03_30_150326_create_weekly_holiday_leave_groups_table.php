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
        Schema::create('weekly_holiday_leave_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('holiday_id')->foreign('weekly_holiday_id')->references('weekly_holiday_id')->on('weekly_holiday')->cascadeOnUpdate()->nullOnDelete();
            $table->unsignedBigInteger('leave_group_id')->foreign('id')->references('id')->on('leave_groups')->cascadeOnUpdate()->nullOnDelete();
             
            
            $table->timestamps();

            $table->unique(['holiday_id', 'leave_group_id'], 'whd_holiday_lvgrp_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('weekly_holiday_leave_groups');
    }
};
