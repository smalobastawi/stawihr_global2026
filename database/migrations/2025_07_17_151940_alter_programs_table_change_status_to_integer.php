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
        Schema::table('programs', function (Blueprint $table) {
            // Drop the default value first if it exists
            $table->integer('status')->nullable()->default(null)->change();
            // Then change the column type to integer
            $table->integer('status')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            // Revert the column type back to string and set the default back to 'active'
            $table->string('status')->default('active')->change();
        });
    }
};
