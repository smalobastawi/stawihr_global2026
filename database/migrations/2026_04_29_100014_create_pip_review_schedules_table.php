<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pip_review_schedules', function (Blueprint $table) {
            $table->id('schedule_id');
            $table->unsignedBigInteger('pip_id');
            $table->foreign('pip_id')->references('pip_id')->on('pip_plans')->onDelete('cascade');
            $table->string('review_stage');
            $table->integer('stage_number')->default(1);
            $table->date('scheduled_date');
            $table->enum('status', ['pending', 'completed', 'missed', 'rescheduled'])->default('pending');
            $table->text('comments')->nullable();
            $table->text('findings')->nullable();
            $table->unsignedBigInteger('conducted_by')->nullable();
            $table->foreign('conducted_by')->references('employee_id')->on('employee')->onDelete('set null');
            $table->timestamp('conducted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pip_review_schedules');
    }
};
