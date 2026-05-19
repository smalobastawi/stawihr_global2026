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
        Schema::create('approval_settings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('model_type')->nullable();
            $table->longText('approvers_list')->nullable();
            $table->unsignedBigInteger('approver_numbers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approval_settings');
    }
};
