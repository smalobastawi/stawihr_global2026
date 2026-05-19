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
        Schema::table('user', function (Blueprint $table) {
            // Change google_id column to nullable (optional, if you want to keep it)
            $table->string('google_id')->nullable()->change();

            // Add new column for multiple Google IDs
            $table->text('google_ids')->nullable()->after('google_id');

            // Or if you want to replace google_id completely:
            // $table->dropColumn('google_id');
            // $table->text('google_ids')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('google_ids');
            // If you changed google_id to nullable, revert it
            $table->string('google_id')->nullable(false)->change();
        });
    }
};
