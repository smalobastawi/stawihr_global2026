<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Get all table names via SQL (NO Doctrine needed)
        $allTables = collect(DB::select('SHOW TABLES'))->map(function ($table) {
            return array_values((array)$table)[0];
        })->toArray();

        // Tables you do NOT want modified
        $excludedTables = [
            'permissions',
            'roles',
            'model_has_permissions',
            'model_has_roles',
            'role_has_permissions',
            'activity_logs',
            'logs',
            'error_logs',
            'biometric_run_logs',
            'morpho_device_logs',
            'password_resets',
            'failed_jobs',
            'personal_access_tokens',
            'users',
            'company_settings',
            'migrations',
            'job_batches',
            'cache',
            'sessions',
            'training_view',
        ];

        $dataTables = array_diff($allTables, $excludedTables);

        foreach ($dataTables as $tableName) {
            if (Schema::hasColumn($tableName, 'company_id') === false) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unsignedBigInteger('company_id')->nullable();
                    $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                });
            }
        }
    }

    public function down(): void
    {
        // Get all table names
        $allTables = collect(DB::select('SHOW TABLES'))->map(function ($table) {
            return array_values((array)$table)[0];
        })->toArray();

        $excludedTables = [
            'permissions',
            'roles',
            'model_has_permissions',
            'model_has_roles',
            'role_has_permissions',
            'activity_logs',
            'logs',
            'error_logs',
            'biometric_run_logs',
            'morpho_device_logs',
            'password_resets',
            'failed_jobs',
            'personal_access_tokens',
            'users',
            'company_settings',
            'migrations',
            'job_batches',
            'cache',
            'sessions',
            'training_view',
        ];

        $dataTables = array_diff($allTables, $excludedTables);

        foreach ($dataTables as $tableName) {
            if (Schema::hasColumn($tableName, 'company_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['company_id']);
                    $table->dropColumn('company_id');
                });
            }
        }
    }
};
