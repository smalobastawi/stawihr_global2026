<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pip_concerns', function (Blueprint $table) {
            $table->id('concern_id');
            $table->unsignedBigInteger('pip_id');
            $table->foreign('pip_id')->references('pip_id')->on('pip_plans')->onDelete('cascade');
            $table->unsignedBigInteger('goal_id')->nullable();
            $table->foreign('goal_id')->references('goal_id')->on('performance_goals')->onDelete('set null');
            $table->unsignedBigInteger('behavioral_item_id')->nullable();
            $table->foreign('behavioral_item_id')->references('behavioral_item_id')->on('performance_behavioral_items')->onDelete('set null');
            $table->unsignedBigInteger('appraisal_score_id')->nullable();
            $table->foreign('appraisal_score_id')->references('score_id')->on('performance_appraisal_scores')->onDelete('set null');
            $table->text('description');
            $table->decimal('actual_score', 5, 2)->nullable();
            $table->decimal('target_score', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pip_concerns');
    }
};
