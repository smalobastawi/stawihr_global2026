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
        Schema::create('employee_leavegroups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('leave_group_id')->foreign('leave_group_id')->references('id')->on('leave_groups')->onDelete('no action');
            $table->unsignedBigInteger('employee_id')->foreign('employee_id')->references('employee_id')->on('employee')->onDelete('no action');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_leavegroups');
    }
};
