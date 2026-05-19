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
        // Add status column if it doesn't exist
        if (!Schema::hasColumn('vehicles', 'status')) {
            Schema::table('vehicles', function (Blueprint $table) {
                // Drop existing index if it exists
                try {
                    $table->dropIndex('vehicles_status_company_id_index');
                } catch (\Exception $e) {
                    // Index may not exist, continue
                }

                $table->tinyInteger('status')->default(1)->comment('0=Inactive, 1=Active, 2=Suspended')->after('location_id');
                $table->index(['status', 'company_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('vehicles', 'status')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
