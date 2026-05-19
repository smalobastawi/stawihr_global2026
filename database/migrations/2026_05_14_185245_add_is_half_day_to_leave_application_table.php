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
        Schema::table('leave_application', function (Blueprint $table) {
            $table->boolean('is_half_day')->default(false)->after('number_of_day')->comment('Indicates if this is a half-day leave');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_application', function (Blueprint $table) {
            $table->dropColumn('is_half_day');
        });
    }
};
