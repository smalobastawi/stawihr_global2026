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
        // First, find and drop all foreign key constraints that reference the deduction table
        $this->dropForeignKeysReferencingDeductionTable();

        // Now it's safe to drop the table
        Schema::dropIfExists('deduction');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Since we're dropping a table, we cannot easily reverse this
        // You would need to recreate the table structure and data
        // This is a destructive operation, so down() is left empty
    }

    /**
     * Find and drop all foreign keys that reference the deduction table
     */
    private function dropForeignKeysReferencingDeductionTable()
    {
        // Find all foreign keys that reference the deduction table
        $foreignKeys = DB::select("
            SELECT 
                TABLE_NAME,
                COLUMN_NAME, 
                CONSTRAINT_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE REFERENCED_TABLE_NAME = 'deduction' 
            AND REFERENCED_TABLE_SCHEMA = DATABASE()
        ");

        foreach ($foreignKeys as $fk) {
            // Drop each foreign key constraint
            DB::statement("
                ALTER TABLE `{$fk->TABLE_NAME}` 
                DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`
            ");
        }
    }
};
