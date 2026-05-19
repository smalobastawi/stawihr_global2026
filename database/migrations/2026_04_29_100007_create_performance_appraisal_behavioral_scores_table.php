<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_appraisal_behavioral_scores', function (Blueprint $table) {
            $table->id('behavioral_score_id');
            $table->unsignedBigInteger('appraisal_id');
            $table->foreign('appraisal_id')->references('appraisal_id')->on('performance_appraisals')->onDelete('cascade');
            $table->unsignedBigInteger('behavioral_item_id');
            $table->foreign('behavioral_item_id', 'pa_behavioral_scores_item_id_foreign')->references('behavioral_item_id')->on('performance_behavioral_items')->onDelete('cascade');

            // Scoring
            $table->decimal('itemized_weighting', 5, 2)->default(0);
            $table->decimal('self_weighting', 5, 2)->default(0);
            $table->decimal('review_weighting', 5, 2)->default(0);
            $table->text('self_comments')->nullable();
            $table->text('review_comments')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_appraisal_behavioral_scores');
    }
};
