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
        Schema::create('morpho_device_logs', function (Blueprint $table) {
            $table->id();
            $table->string('id_no');
            $table->string('user_first_name');
            $table->string('user_name');
            $table->string('device_id');
            $table->dateTime('time_logged');
            $table->string('location');
            $table->string('year');
            $table->integer('month');
            $table->integer('day');
            $table->integer('hour');
            $table->integer('minute');
            $table->integer('second');
            $table->string('ip_address');

            $table->date('date');
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
        Schema::dropIfExists('morpho_device_logs');
    }
};
