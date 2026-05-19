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
       
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('training_type_id')->constrained('training_types')->onDelete('no action');
            $table->unsignedBigInteger('facilitator_id')->constrained('training_facilitators')->onDelete('no action');
            $table->string('subject');
            $table->enum('attendance_type', ['physical', 'online'])->default('physical');
            $table->string('attendance_link')->nullable();
            $table->string('attendance_location')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description');
            $table->unsignedBigInteger('created_by')->constrained('user')->onDelete('no action');
            $table->unsignedBigInteger('updated_by')->constrained('user')->onDelete('no action');
            $table->boolean('attendance_approved');
            $table->boolean('invites_approved');
            $table->unsignedBigInteger('invite_approved_by')->nullable();
            $table->unsignedBigInteger('attendance_approved_by')->nullable();
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
        Schema::dropIfExists('trainings');
    }
};
