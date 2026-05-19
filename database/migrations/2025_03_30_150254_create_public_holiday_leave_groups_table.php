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
        Schema::create('public_holiday_leave_groups', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('holiday_id')->foreign('holiday_id')->references('holiday_id')->on('holidays')->cascadeOnUpdate()->nullOnDelete();
            $table->unsignedBigInteger('leave_group_id')->foreign('id')->references('id')->on('leave_groups')->cascadeOnUpdate()->nullOnDelete();
 
            $table->timestamps();

            $table->unique(['holiday_id', 'leave_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('public_holiday_leave_groups');
    }
};
