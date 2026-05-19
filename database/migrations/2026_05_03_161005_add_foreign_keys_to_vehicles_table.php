<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Add foreign keys if they don't exist
        $databaseName = DB::getDatabaseName();

        // Check and add vehicle_type_id foreign key
        $fkExists = DB::select(
            "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS 
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'vehicles' AND CONSTRAINT_NAME = 'vehicles_vehicle_type_id_foreign'",
            [$databaseName]
        );

        if (empty($fkExists)) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types')->onDelete('set null');
            });
        }

        // Check and add location_id foreign key
        $fkExists = DB::select(
            "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS 
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'vehicles' AND CONSTRAINT_NAME = 'vehicles_location_id_foreign'",
            [$databaseName]
        );

        if (empty($fkExists)) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->foreign('location_id')->references('location_id')->on('location')->onDelete('set null');
            });
        }

        // Check and add company_id foreign key
        $fkExists = DB::select(
            "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS 
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'vehicles' AND CONSTRAINT_NAME = 'vehicles_company_id_foreign'",
            [$databaseName]
        );

        if (empty($fkExists)) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            try {
                $table->dropForeign(['vehicle_type_id']);
            } catch (\Exception $e) {}
            try {
                $table->dropForeign(['location_id']);
            } catch (\Exception $e) {}
            try {
                $table->dropForeign(['company_id']);
            } catch (\Exception $e) {}
        });
    }
};
