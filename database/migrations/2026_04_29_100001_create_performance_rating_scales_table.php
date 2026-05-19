<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_rating_scales', function (Blueprint $table) {
            $table->id('rating_scale_id');
            $table->integer('points'); // 5, 4, 3, 2, 1
            $table->string('rating_label'); // Top performer, Exceeds expectations, etc.
            $table->string('description'); // Achieve well beyond expectations, etc.
            $table->text('definition');
            $table->string('score_range'); // e.g. "<120%", "101% - 119%"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_rating_scales');
    }
};
