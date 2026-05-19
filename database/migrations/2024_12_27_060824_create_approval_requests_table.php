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
        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('approval_setting_id');
            $table->unsignedBigInteger('request_by');
            $table->string('request_data');
            $table->string('route_name');
            $table->string('request_method');
            $table->string('action_type');
            $table->enum('status',['pending','declined','approved'])->default('pending');
            $table->boolean('effected');
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
        Schema::dropIfExists('approval_requests');
    }
};
