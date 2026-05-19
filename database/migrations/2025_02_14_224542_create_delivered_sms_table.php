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
        Schema::create('delivered_sms', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('message_id');
            $table->string('message_status');
            $table->string('API_response');
            $table->string('message');
            $table->string('mobile');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivered_sms');
    }
};
