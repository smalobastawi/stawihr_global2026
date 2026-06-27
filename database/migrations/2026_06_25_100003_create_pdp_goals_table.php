<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdp_goals', function (Blueprint $table) {
            $table->id('pdp_goal_id');
            $table->unsignedBigInteger('pdp_plan_id');
            $table->foreign('pdp_plan_id')->references('pdp_plan_id')->on('pdp_plans')->onDelete('cascade');

            $table->string('goal_title');
            $table->text('smart_objective');
            $table->string('competency_area')->nullable();
            $table->text('success_criteria')->nullable();
            $table->text('development_actions')->nullable();
            $table->text('resources_needed')->nullable();
            $table->date('target_completion_date')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['not_started', 'in_progress', 'on_track', 'at_risk', 'completed', 'deferred'])->default('not_started');
            $table->unsignedTinyInteger('overall_progress')->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdp_goals');
    }
};
