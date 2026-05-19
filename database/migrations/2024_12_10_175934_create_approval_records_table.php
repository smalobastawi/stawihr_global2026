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
        Schema::create('approval_records', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->longText('new')->nullable();
            $table->longText('old')->nullable();
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('approver_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approval_records');
    }
};
