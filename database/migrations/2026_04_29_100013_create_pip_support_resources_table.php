<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pip_support_resources', function (Blueprint $table) {
            $table->id('resource_id');
            $table->unsignedBigInteger('pip_id');
            $table->foreign('pip_id')->references('pip_id')->on('pip_plans')->onDelete('cascade');
            $table->enum('support_type', ['training', 'mentorship', 'tools', 'counseling', 'other']);
            $table->text('description');
            $table->enum('provider', ['hr', 'supervisor', 'external', 'peer']);
            $table->date('scheduled_date')->nullable();
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pip_support_resources');
    }
};
