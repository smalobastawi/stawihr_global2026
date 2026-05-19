<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Get database name to check for existing foreign keys
        $databaseName = DB::getDatabaseName();

        // Check and drop existing foreign keys using explicit constraint names
        $foreignKeys = [
            'vehicle_assignments_vehicle_id_foreign',
            'vehicle_assignments_employee_id_foreign',
            'vehicle_assignments_assigned_by_foreign',
            'vehicle_assignments_returned_by_foreign',
            'vehicle_assignments_company_id_foreign',
        ];

        foreach ($foreignKeys as $constraintName) {
            try {
                // Check if the foreign key exists
                $exists = DB::select(
                    "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS 
                     WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'vehicle_assignments' AND CONSTRAINT_NAME = ?",
                    [$databaseName, $constraintName]
                );

                if (!empty($exists)) {
                    DB::statement("ALTER TABLE vehicle_assignments DROP FOREIGN KEY {$constraintName}");
                }
            } catch (\Exception $e) {
                // Ignore errors for non-existent foreign keys
            }
        }

        // Add foreign keys with correct table references
        Schema::table('vehicle_assignments', function (Blueprint $table) {
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('employee_id')->references('employee_id')->on('employee')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('user')->onDelete('set null');
            $table->foreign('returned_by')->references('id')->on('user')->onDelete('set null');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        $databaseName = DB::getDatabaseName();

        $foreignKeys = [
            'vehicle_assignments_vehicle_id_foreign',
            'vehicle_assignments_employee_id_foreign',
            'vehicle_assignments_assigned_by_foreign',
            'vehicle_assignments_returned_by_foreign',
            'vehicle_assignments_company_id_foreign',
        ];

        foreach ($foreignKeys as $constraintName) {
            try {
                $exists = DB::select(
                    "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS 
                     WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'vehicle_assignments' AND CONSTRAINT_NAME = ?",
                    [$databaseName, $constraintName]
                );

                if (!empty($exists)) {
                    DB::statement("ALTER TABLE vehicle_assignments DROP FOREIGN KEY {$constraintName}");
                }
            } catch (\Exception $e) {
                // Ignore errors
            }
        }
    }
};
