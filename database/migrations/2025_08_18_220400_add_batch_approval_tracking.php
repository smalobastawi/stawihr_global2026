<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBatchApprovalTracking extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add batch_id to approval_logs table for tracking batch operations
        if (!Schema::hasColumn('approval_logs', 'batch_id')) {
            Schema::table('approval_logs', function (Blueprint $table) {
                $table->string('batch_id')->nullable()->after('comments');
                $table->index('batch_id');
            });
        }

        // Add batch_submission_id to models that use approval workflow
        // This tracks when multiple records are submitted together
        $tables = [
            'employee_deductions',
            'employee_earnings',
            'payroll_records',
            'employee_payrolls',
            'employee_allowances',
            'payroll_claims',
            'payroll_claim_recoveries'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'batch_submission_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->string('batch_submission_id')->nullable()->after('approval_status');
                    $table->index('batch_submission_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove batch_id from approval_logs
        if (Schema::hasColumn('approval_logs', 'batch_id')) {
            Schema::table('approval_logs', function (Blueprint $table) {
                $table->dropIndex(['batch_id']);
                $table->dropColumn('batch_id');
            });
        }

        // Remove batch_submission_id from models
        $tables = [
            'employee_deductions',
            'employee_earnings',
            'payroll_records',
            'employee_payrolls',
            'employee_allowances',
            'payroll_claims',
            'payroll_claim_recoveries'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'batch_submission_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropIndex(['batch_submission_id']);
                    $table->dropColumn('batch_submission_id');
                });
            }
        }
    }
}