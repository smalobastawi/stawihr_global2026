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
        Schema::create('public_holiday_departments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('holiday_id')->foreign('holiday_id')->references('holiday_id')->on('holidays')->cascadeOnUpdate()->nullOnDelete();
            $table->unsignedBigInteger('department_id')->foreign('department_id')->references('department_id')->on('departments')->cascadeOnUpdate()->nullOnDelete();
 
            $table->timestamps();

            $table->unique(['holiday_id', 'department_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('public_holiday_departments');
    }
};
