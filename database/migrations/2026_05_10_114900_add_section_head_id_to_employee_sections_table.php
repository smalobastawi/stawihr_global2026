<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employee_sections', function (Blueprint $table) {
            $table->unsignedBigInteger('section_head_id')->nullable()->after('description');
            $table->foreign('section_head_id')->references('employee_id')->on('employee')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_sections', function (Blueprint $table) {
            $table->dropForeign(['section_head_id']);
            $table->dropColumn('section_head_id');
        });
    }
};
