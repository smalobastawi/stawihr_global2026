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
        Schema::create('morpho_devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_name');
            $table->string('device_ip_address');
            $table->string('device_serial');
            $table->integer('port');
            $table->string('device_location')->nullable();
            $table->integer('timeout');
            $table->string('device_status')->nullable();
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
        Schema::dropIfExists('morpho_devices');
    }
};
