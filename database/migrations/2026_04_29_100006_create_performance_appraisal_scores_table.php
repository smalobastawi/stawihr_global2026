<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_appraisal_scores', function (Blueprint $table) {
            $table->id('score_id');
            $table->unsignedBigInteger('appraisal_id');
            $table->foreign('appraisal_id')->references('appraisal_id')->on('performance_appraisals')->onDelete('cascade');
            $table->unsignedBigInteger('goal_id');
            $table->foreign('goal_id')->references('goal_id')->on('performance_goals')->onDelete('cascade');

            // Scoring
            $table->decimal('itemized_weighting', 5, 2)->default(0); // From goal setup
            $table->decimal('self_weighting', 5, 2)->default(0); // Self rating
            $table->decimal('review_weighting', 5, 2)->default(0); // Supervisor rating
            $table->text('self_comments')->nullable(); // Justification for self rating
            $table->text('review_comments')->nullable(); // Supervisor comments

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_appraisal_scores');
    }
};
