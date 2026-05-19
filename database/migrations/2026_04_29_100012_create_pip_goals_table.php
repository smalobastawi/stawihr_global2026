<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pip_goals', function (Blueprint $table) {
            $table->id('goal_id');
            $table->unsignedBigInteger('pip_id');
            $table->foreign('pip_id')->references('pip_id')->on('pip_plans')->onDelete('cascade');
            $table->text('objective');
            $table->text('action_required');
            $table->string('target_kpi');
            $table->date('deadline');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'overdue'])->default('pending');
            $table->text('progress_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pip_goals');
    }
};
