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
        Schema::create('training_invitees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('no action');
            $table->unsignedBigInteger('training_id')->foreign('training_id')->references('id')->on('trainings')->onDelete('no action');
            $table->boolean('approved')->default(false);
            $table->unsignedBigInteger('approved_by')->nullable()->foreign('approved_by')->references('user_id')->on('user')->onDelete('set null');
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
        Schema::dropIfExists('training_invitees');
    }
};
