<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_earnings', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_earnings', 'payroll_period_id')) {
                $table->foreignId('payroll_period_id')
                    ->nullable()
                    ->after('financial_year_id')
                    ->constrained('payroll_periods')
                    ->nullOnDelete();
            }
            if (!Schema::hasColumn('employee_earnings', 'end_payroll_period_id')) {
                $table->foreignId('end_payroll_period_id')
                    ->nullable()
                    ->after('payroll_period_id')
                    ->constrained('payroll_periods')
                    ->nullOnDelete();
            }
        });

        Schema::table('employee_deductions', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_deductions', 'payroll_period_id')) {
                $table->foreignId('payroll_period_id')
                    ->nullable()
                    ->after('financial_year_id')
                    ->constrained('payroll_periods')
                    ->nullOnDelete();
            }
            if (!Schema::hasColumn('employee_deductions', 'end_payroll_period_id')) {
                $table->foreignId('end_payroll_period_id')
                    ->nullable()
                    ->after('payroll_period_id')
                    ->constrained('payroll_periods')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_earnings', function (Blueprint $table) {
            if (Schema::hasColumn('employee_earnings', 'end_payroll_period_id')) {
                $table->dropConstrainedForeignId('end_payroll_period_id');
            }
            if (Schema::hasColumn('employee_earnings', 'payroll_period_id')) {
                $table->dropConstrainedForeignId('payroll_period_id');
            }
        });

        Schema::table('employee_deductions', function (Blueprint $table) {
            if (Schema::hasColumn('employee_deductions', 'end_payroll_period_id')) {
                $table->dropConstrainedForeignId('end_payroll_period_id');
            }
            if (Schema::hasColumn('employee_deductions', 'payroll_period_id')) {
                $table->dropConstrainedForeignId('payroll_period_id');
            }
        });
    }
};
