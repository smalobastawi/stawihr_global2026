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
        Schema::create('employee_feedback_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feedback_id');
            $table->unsignedBigInteger('responder_id');
            $table->unsignedBigInteger('location_id');
            $table->text('content');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->foreign('feedback_id')->references('id')->on('employee_feedback');
            $table->foreign('responder_id')->references('employee_id')->on('employee');
            $table->foreign('location_id')->references('location_id')->on('location')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('deleted_by')->references('id')->on('user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_feedback_responses');
    }
};