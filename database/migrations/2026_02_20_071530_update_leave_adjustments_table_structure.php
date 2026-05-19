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
    public function up()
    {
        // Only run if table exists
        if (Schema::hasTable('leave_adjustments')) {
            Schema::table('leave_adjustments', function (Blueprint $table) {
                // Add missing columns if they don't exist
                if (!Schema::hasColumn('leave_adjustments', 'approved_by')) {
                    $table->unsignedBigInteger('approved_by')->nullable();
                }

                if (!Schema::hasColumn('leave_adjustments', 'status')) {
                    $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
                }

                if (!Schema::hasColumn('leave_adjustments', 'approved_at')) {
                    $table->timestamp('approved_at')->nullable();
                }

                if (!Schema::hasColumn('leave_adjustments', 'rejection_reason')) {
                    $table->text('rejection_reason')->nullable();
                }

                if (!Schema::hasColumn('leave_adjustments', 'deleted_at')) {
                    $table->softDeletes();
                }
                
                if (!Schema::hasColumn('leave_adjustments', 'financial_year_id')) {
                    $table->unsignedBigInteger('financial_year_id')->nullable();
                }
            });

            // Add indexes if they don't exist - using raw SQL queries or checking via schema builder
            $prefix = DB::getTablePrefix();
            $databaseName = DB::getDatabaseName();
            
            // Get existing indexes
            $existingIndexes = DB::select("
                SELECT INDEX_NAME 
                FROM information_schema.STATISTICS 
                WHERE TABLE_SCHEMA = ? 
                AND TABLE_NAME = ?
            ", [$databaseName, $prefix . 'leave_adjustments']);
            
            $existingIndexNames = array_column($existingIndexes, 'INDEX_NAME');
            
            Schema::table('leave_adjustments', function (Blueprint $table) use ($existingIndexNames) {
                if (!in_array('leave_adjustments_employee_id_index', $existingIndexNames) && 
                    !in_array('employee_id', $existingIndexNames)) {
                    $table->index('employee_id');
                }

                if (!in_array('leave_adjustments_leave_type_id_index', $existingIndexNames) && 
                    !in_array('leave_type_id', $existingIndexNames)) {
                    $table->index('leave_type_id');
                }

                if (!in_array('leave_adjustments_financial_year_id_index', $existingIndexNames) && 
                    !in_array('financial_year_id', $existingIndexNames)) {
                    $table->index('financial_year_id');
                }

                if (!in_array('leave_adjustments_status_index', $existingIndexNames) && 
                    !in_array('status', $existingIndexNames)) {
                    $table->index('status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('leave_adjustments')) {
            Schema::table('leave_adjustments', function (Blueprint $table) {
                // Drop indexes (suppress errors if they don't exist)
                try {
                    $table->dropIndex(['employee_id']);
                } catch (\Exception $e) {
                    // Index might not exist
                }
                
                try {
                    $table->dropIndex(['leave_type_id']);
                } catch (\Exception $e) {
                    // Index might not exist
                }
                
                try {
                    $table->dropIndex(['financial_year_id']);
                } catch (\Exception $e) {
                    // Index might not exist
                }
                
                try {
                    $table->dropIndex(['status']);
                } catch (\Exception $e) {
                    // Index might not exist
                }

                // Drop columns (check if they exist first)
                $columns = ['approved_by', 'status', 'approved_at', 'rejection_reason', 'deleted_at', 'financial_year_id'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('leave_adjustments', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};