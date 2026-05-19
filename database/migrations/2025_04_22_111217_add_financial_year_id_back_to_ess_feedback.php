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
        // Check if column doesn't exist first
        if (!Schema::hasColumn('employee_feedback', 'financial_year_id')) {
            Schema::table('employee_feedback', function (Blueprint $table) {
                $table->unsignedBigInteger('financial_year_id')
                    ->nullable()
                    ->comment('References financial_years table');
            });
        }

        // Check if foreign key doesn't exist using database query
        $foreignKeyExists = DB::select("
            SELECT COUNT(*) as count 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE CONSTRAINT_SCHEMA = ? 
            AND TABLE_NAME = 'employee_feedback' 
            AND CONSTRAINT_NAME = 'employee_feedback_financial_year_id_foreign'
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ", [config('database.connections.mysql.database')]);

        if ($foreignKeyExists[0]->count == 0 && Schema::hasTable('financial_years')) {
            Schema::table('employee_feedback', function (Blueprint $table) {
                $table->foreign('financial_year_id')
                    ->references('id')
                    ->on('financial_years')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Drop the foreign key constraint if it exists
        Schema::table('employee_feedback', function (Blueprint $table) {
            $table->dropForeignIfExists(['financial_year_id']);
        });

        // Then drop the column if it exists
        if (Schema::hasColumn('employee_feedback', 'financial_year_id')) {
            Schema::table('employee_feedback', function (Blueprint $table) {
                $table->dropColumn('financial_year_id');
            });
        }
    }
};
