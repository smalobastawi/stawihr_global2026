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
        Schema::table('payroll_record_details', function (Blueprint $table) {
            $table->decimal('units', 10, 2)->nullable()->after('rate');
            $table->json('metadata')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_record_details', function (Blueprint $table) {
            $table->dropColumn(['units', 'metadata']);
        });
    }
};