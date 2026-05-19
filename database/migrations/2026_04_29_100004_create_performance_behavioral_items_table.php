<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_behavioral_items', function (Blueprint $table) {
            $table->id('behavioral_item_id');
            $table->string('item_name'); // e.g. "Initiative", "Teamwork", "Leadership & Integrity"
            $table->decimal('weight', 5, 2)->default(0); // e.g. 0.02, 0.03
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_behavioral_items');
    }
};
