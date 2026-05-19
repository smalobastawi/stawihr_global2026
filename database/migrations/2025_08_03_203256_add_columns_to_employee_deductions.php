<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('employee_deductions', function (Blueprint $table) {
            // Add columns that exist in employee_earnings but missing in employee_deductions
            if (!Schema::hasColumn('employee_deductions', 'limit_per_month')) {
                $table->decimal('limit_per_month', 10, 2)->nullable()->after('amount');
            }

            if (!Schema::hasColumn('employee_deductions', 'limit_per_year')) {
                $table->decimal('limit_per_year', 10, 2)->nullable()->after('limit_per_month');
            }

            if (!Schema::hasColumn('employee_deductions', 'is_tax_deductible')) {
                $table->boolean('is_tax_deductible')->default(false)->after('limit_per_year');
            }

            if (!Schema::hasColumn('employee_deductions', 'is_recurring')) {
                $table->boolean('is_recurring')->default(false)->after('is_tax_deductible');
            }

            if (!Schema::hasColumn('employee_deductions', 'frequency')) {
                $table->string('frequency', 50)->nullable()->after('is_recurring');
            }

            if (!Schema::hasColumn('employee_deductions', 'effective_from')) {
                $table->date('effective_from')->nullable()->after('frequency');
            }

            if (!Schema::hasColumn('employee_deductions', 'effective_to')) {
                $table->date('effective_to')->nullable()->after('effective_from');
            }

            if (!Schema::hasColumn('employee_deductions', 'payroll_year')) {
                $table->integer('payroll_year')->nullable()->after('effective_to');
            }

            if (!Schema::hasColumn('employee_deductions', 'payroll_month')) {
                $table->integer('payroll_month')->nullable()->after('payroll_year');
            }

            if (!Schema::hasColumn('employee_deductions', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('payroll_month');
            }

            if (!Schema::hasColumn('employee_deductions', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }

            if (!Schema::hasColumn('employee_deductions', 'approval_notes')) {
                $table->text('approval_notes')->nullable()->after('approved_at');
            }

            if (!Schema::hasColumn('employee_deductions', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('approval_notes');
            }

            if (!Schema::hasColumn('employee_deductions', 'financial_year_id')) {
                $table->unsignedBigInteger('financial_year_id')->nullable()->after('updated_by');
            }

            if (!Schema::hasColumn('employee_deductions', 'is_pensionable')) {
                $table->boolean('is_pensionable')->default(false)->after('financial_year_id');
            }
        });

        // Add indexes separately (after columns are added)
        $this->addIndexes();

        // Add foreign keys separately
        $this->addForeignKeys();
    }

    /**
     * Add indexes to the table
     */
    private function addIndexes()
    {
        // Add composite index for payroll_year and payroll_month
        if (!$this->indexExists('employee_deductions', 'employee_deductions_payroll_year_payroll_month_index')) {
            Schema::table('employee_deductions', function (Blueprint $table) {
                $table->index(['payroll_year', 'payroll_month']);
            });
        }

        // Add single column indexes
        $indexes = [
            'effective_from' => 'employee_deductions_effective_from_index',
            'effective_to' => 'employee_deductions_effective_to_index',
            'financial_year_id' => 'employee_deductions_financial_year_id_index',
        ];

        foreach ($indexes as $column => $indexName) {
            if (
                Schema::hasColumn('employee_deductions', $column) &&
                !$this->indexExists('employee_deductions', $indexName)
            ) {
                Schema::table('employee_deductions', function (Blueprint $table) use ($column) {
                    $table->index([$column]);
                });
            }
        }
    }

    /**
     * Add foreign key constraints
     */
    private function addForeignKeys()
    {
        // Add approved_by foreign key
        if (
            !$this->foreignKeyExists('employee_deductions', 'approved_by') &&
            Schema::hasColumn('employee_deductions', 'approved_by')
        ) {
            Schema::table('employee_deductions', function (Blueprint $table) {
                $table->foreign('approved_by')->references('id')->on('user')->onDelete('set null');
            });
        }

        // Add updated_by foreign key
        if (
            !$this->foreignKeyExists('employee_deductions', 'updated_by') &&
            Schema::hasColumn('employee_deductions', 'updated_by')
        ) {
            Schema::table('employee_deductions', function (Blueprint $table) {
                $table->foreign('updated_by')->references('id')->on('user')->onDelete('set null');
            });
        }

        // Add financial_year_id foreign key
        if (
            !$this->foreignKeyExists('employee_deductions', 'financial_year_id') &&
            Schema::hasColumn('employee_deductions', 'financial_year_id') &&
            Schema::hasTable('financial_years')
        ) {
            Schema::table('employee_deductions', function (Blueprint $table) {
                $table->foreign('financial_year_id')->references('id')->on('financial_years')->onDelete('set null');
            });
        }
    }

    /**
     * Check if an index exists
     */
    private function indexExists($table, $indexName)
    {
        $connection = DB::connection();
        $databaseName = config('database.connections.mysql.database');

        $result = DB::select("
            SELECT COUNT(*) as count 
            FROM information_schema.statistics 
            WHERE table_schema = ? 
            AND table_name = ? 
            AND index_name = ?
        ", [$databaseName, $table, $indexName]);

        return $result[0]->count > 0;
    }

    /**
     * Check if a foreign key exists
     */
    private function foreignKeyExists($table, $column)
    {
        $connection = DB::connection();
        $databaseName = config('database.connections.mysql.database');

        $result = DB::select("
            SELECT COUNT(*) as count 
            FROM information_schema.key_column_usage 
            WHERE table_schema = ? 
            AND table_name = ? 
            AND column_name = ? 
            AND referenced_table_name IS NOT NULL
        ", [$databaseName, $table, $column]);

        return $result[0]->count > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Drop foreign keys first
        Schema::table('employee_deductions', function (Blueprint $table) {
            // Use dropForeignIfExists for Laravel 8+ or try-catch for older versions
            try {
                $table->dropForeign(['approved_by']);
            } catch (\Exception $e) {
            }

            try {
                $table->dropForeign(['updated_by']);
            } catch (\Exception $e) {
            }

            try {
                $table->dropForeign(['financial_year_id']);
            } catch (\Exception $e) {
            }
        });

        // Drop indexes
        $indexes = [
            'employee_deductions_payroll_year_payroll_month_index',
            'employee_deductions_effective_from_index',
            'employee_deductions_effective_to_index',
            'employee_deductions_financial_year_id_index',
        ];

        foreach ($indexes as $index) {
            if ($this->indexExists('employee_deductions', $index)) {
                Schema::table('employee_deductions', function (Blueprint $table) use ($index) {
                    $table->dropIndex($index);
                });
            }
        }

        // Drop columns
        Schema::table('employee_deductions', function (Blueprint $table) {
            $columns = [
                'limit_per_month',
                'limit_per_year',
                'is_tax_deductible',
                'is_recurring',
                'frequency',
                'effective_from',
                'effective_to',
                'payroll_year',
                'payroll_month',
                'approved_by',
                'approved_at',
                'approval_notes',
                'updated_by',
                'financial_year_id',
                'is_pensionable'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('employee_deductions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
