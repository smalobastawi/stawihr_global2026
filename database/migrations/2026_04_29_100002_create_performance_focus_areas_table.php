<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_focus_areas', function (Blueprint $table) {
            $table->id('focus_area_id');
            $table->string('focus_area_name'); // e.g. "Internal Business Processes", "Learning & Growth", "Behavioral Expectations"
            $table->decimal('weight', 5, 2)->default(0); // e.g. 40.00, 45.00, 15.00
            $table->text('description')->nullable();
            $table->integer('department_id')->unsigned()->nullable();
            $table->foreign('department_id')->references('department_id')->on('department')->onDelete('set null');
            $table->integer('designation_id')->unsigned()->nullable();
            $table->foreign('designation_id')->references('designation_id')->on('designation')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_focus_areas');
    }
};
