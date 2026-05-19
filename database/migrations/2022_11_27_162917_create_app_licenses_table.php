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
        Schema::create('app_licenses', function (Blueprint $table) {
            $table->id();
            $table->string('license_id')->nullable();
            $table->dateTime('activation_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('domain')->nullable();
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
        Schema::dropIfExists('app_licenses');
    }
};
