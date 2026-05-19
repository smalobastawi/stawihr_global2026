<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tables to EXCLUDE from getting approval columns
    protected $excludedTables = [
        'migrations',
        'password_resets',
        'failed_jobs',
        'personal_access_tokens',
        'sessions',
        'failed_jobs',
        'logs',
        'error_logs',
        'morpho_device_logs',
        'biometric_run_logs',
        'approval_query_logs',
        'approval_logs',
        'appraisal_performance_logs',
        'appraisal_goal_kpi_assignment_logs',
        'attendances',
        'attendance_locations',
        'attendance_overtime_approvals',
        'employee_attendance_approve',
        'salary_deduction_for_late_attendance',
        'training_attendances',
        'training_attendants',
        'activity_log',
        // Add any other system tables you want to exclude
    ];

    /**
     * Run the migrations.
     */
    public function up()
    {
        // Get all tables in the database using SQL query
        $tables = $this->getAllTables();

        foreach ($tables as $table) {
            // Skip excluded tables
            if (in_array($table, $this->excludedTables)) {
                continue;
            }

            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $tableSchema) use ($table) {
                    if (!Schema::hasColumn($table, 'approval_status')) {
                        $tableSchema->integer('approval_status')
                            ->nullable()
                            ->default(0)
                            ->comment('Approval status: 0=pending, 1=approved, 2=rejected, 3=cancelled, -1=draft');
                    }

                    if (!Schema::hasColumn($table, 'date_approved')) {
                        $tableSchema->timestamp('date_approved')
                            ->nullable()
                            ->comment('Date when the record was approved');
                    }

                    // Fixed: Changed 'status' column type from timestamp to appropriate type
                    if (!Schema::hasColumn($table, 'status')) {
                        $tableSchema->string('status', 50) // or integer, enum, etc.
                            ->nullable()
                            ->comment('Status of the record');
                    }

                    // Optional: Add approved_by column if you want to track who approved
                    if (!Schema::hasColumn($table, 'approved_by')) {
                        $tableSchema->unsignedBigInteger('approved_by')
                            ->nullable()
                            ->comment('User ID who approved the record');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Get all tables in the database using SQL query
        $tables = $this->getAllTables();

        foreach ($tables as $table) {
            // Skip excluded tables
            if (in_array($table, $this->excludedTables)) {
                continue;
            }

            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $tableSchema) use ($table) {
                    $columnsToDrop = [];

                    if (Schema::hasColumn($table, 'approval_status')) {
                        $columnsToDrop[] = 'approval_status';
                    }

                    if (Schema::hasColumn($table, 'date_approved')) {
                        $columnsToDrop[] = 'date_approved';
                    }

                    if (Schema::hasColumn($table, 'approved_by')) {
                        $columnsToDrop[] = 'approved_by';
                    }

                    // Only drop status if it was added by this migration
                    // Be careful with this as 'status' might be an existing important column
                    // if (Schema::hasColumn($table, 'status')) {
                    //     $columnsToDrop[] = 'status';
                    // }

                    if (!empty($columnsToDrop)) {
                        $tableSchema->dropColumn($columnsToDrop);
                    }
                });
            }
        }
    }

    /**
     * Get all table names in the database
     */
    private function getAllTables()
    {
        $databaseName = config('database.connections.mysql.database');

        $tables = DB::select("SHOW TABLES FROM `{$databaseName}`");

        return array_map(function ($table) use ($databaseName) {
            $propertyName = "Tables_in_{$databaseName}";
            return $table->$propertyName;
        }, $tables);
    }
};
