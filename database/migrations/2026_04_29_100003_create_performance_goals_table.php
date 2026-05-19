<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_goals', function (Blueprint $table) {
            $table->id('goal_id');
            $table->unsignedBigInteger('focus_area_id');
            $table->foreign('focus_area_id')->references('focus_area_id')->on('performance_focus_areas')->onDelete('cascade');
            $table->string('strategic_objective'); // e.g. "Ensure seamless logistics operation..."
            $table->string('performance_metric'); // e.g. "Automation", "Internal reporting"
            $table->text('performance_target'); // e.g. "100% automation of the driver compliance documents"
            $table->text('key_initiatives')->nullable(); // e.g. "Maintain a live digital records..."
            $table->decimal('itemized_weighting', 5, 2)->default(0); // e.g. 0.05
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_goals');
    }
};
