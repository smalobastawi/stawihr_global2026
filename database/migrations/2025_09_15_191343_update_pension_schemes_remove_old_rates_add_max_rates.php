<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the table exists first
        if (Schema::hasTable('pension_schemes')) {

            // Check if the old columns exist before dropping them
            if (Schema::hasColumn('pension_schemes', 'employee_contribution_rate')) {
                Schema::table('pension_schemes', function (Blueprint $table) {
                    $table->dropColumn('employee_contribution_rate');
                });
            }

            if (Schema::hasColumn('pension_schemes', 'employer_contribution_rate')) {
                Schema::table('pension_schemes', function (Blueprint $table) {
                    $table->dropColumn('employer_contribution_rate');
                });
            }

            // Add new columns if they don't exist
            if (!Schema::hasColumn('pension_schemes', 'max_employee_rate')) {
                Schema::table('pension_schemes', function (Blueprint $table) {
                    $table->decimal('max_employee_rate', 5, 2)->nullable()->after('provider_contact');
                });
            }

            if (!Schema::hasColumn('pension_schemes', 'max_employer_rate')) {
                Schema::table('pension_schemes', function (Blueprint $table) {
                    $table->decimal('max_employer_rate', 5, 2)->nullable()->after('max_employee_rate');
                });
            }
        } else {
            // Optional: Log or handle the case where the table doesn't exist
            \Illuminate\Support\Facades\Log::warning('pension_schemes table does not exist during migration');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Check if the table exists first
        if (Schema::hasTable('pension_schemes')) {

            // Check if the new columns exist before dropping them
            if (Schema::hasColumn('pension_schemes', 'max_employee_rate')) {
                Schema::table('pension_schemes', function (Blueprint $table) {
                    $table->dropColumn('max_employee_rate');
                });
            }

            if (Schema::hasColumn('pension_schemes', 'max_employer_rate')) {
                Schema::table('pension_schemes', function (Blueprint $table) {
                    $table->dropColumn('max_employer_rate');
                });
            }

            // Add back the old columns if they don't exist
            if (!Schema::hasColumn('pension_schemes', 'employee_contribution_rate')) {
                Schema::table('pension_schemes', function (Blueprint $table) {
                    $table->decimal('employee_contribution_rate', 5, 2)->nullable()->after('provider_contact');
                });
            }

            if (!Schema::hasColumn('pension_schemes', 'employer_contribution_rate')) {
                Schema::table('pension_schemes', function (Blueprint $table) {
                    $table->decimal('employer_contribution_rate', 5, 2)->nullable()->after('employee_contribution_rate');
                });
            }
        } else {
            // Optional: Log or handle the case where the table doesn't exist
            \Illuminate\Support\Facades\Log::warning('pension_schemes table does not exist during migration rollback');
        }
    }
};
