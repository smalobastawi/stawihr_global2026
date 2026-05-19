<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
         // Step 1: Find tables with unsignedBigInteger primary keys that are not auto-incrementing
     $tables = DB::select("
    SELECT TABLE_NAME, COLUMN_NAME 
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = ? 
      AND COLUMN_KEY = 'PRI' 
      AND EXTRA NOT LIKE '%auto_increment%'
      AND DATA_TYPE = 'bigint'
      AND TABLE_NAME IN (
          SELECT TABLE_NAME 
          FROM INFORMATION_SCHEMA.COLUMNS 
          WHERE TABLE_SCHEMA = ? 
            AND COLUMN_KEY = 'PRI' 
          GROUP BY TABLE_NAME 
          HAVING COUNT(*) = 1
      )
", [DB::getDatabaseName(), DB::getDatabaseName()]);
    // dd( $tables);

     foreach ($tables as $table) {
         $tableName = $table->TABLE_NAME;
         $columnName = $table->COLUMN_NAME;

         // Step 2: Find and drop foreign keys referencing this primary key
         $foreignKeys = DB::select("
             SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME 
             FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
             WHERE REFERENCED_TABLE_SCHEMA = ? 
               AND REFERENCED_TABLE_NAME = ? 
               AND REFERENCED_COLUMN_NAME = ?",
             [DB::getDatabaseName(), $tableName, $columnName]
         );

         foreach ($foreignKeys as $foreignKey) {
             Schema::table($foreignKey->TABLE_NAME, function ($table) use ($foreignKey) {
                 $table->dropForeign($foreignKey->CONSTRAINT_NAME);
             });
         }

         // Step 3: Get the maximum value of the primary key column
         $maxValue = DB::table($tableName)->max($columnName);

         // Step 4: Temporarily shift primary key values to avoid conflicts
         $shiftAmount = $maxValue + 2;
         DB::statement("UPDATE `{$tableName}` SET `{$columnName}` = `{$columnName}` + {$shiftAmount}");

         // Step 5: Modify the primary key to be auto-incrementing
         DB::statement("ALTER TABLE `{$tableName}` MODIFY `{$columnName}` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT");

         // Step 6: Reset primary key values back to the original range
         DB::statement("UPDATE `{$tableName}` SET `{$columnName}` = `{$columnName}` - {$shiftAmount}");

         // Step 7: Set AUTO_INCREMENT value to max(column) + 1
         $newAutoIncrement = $maxValue + 1;
         DB::statement("ALTER TABLE `{$tableName}` AUTO_INCREMENT = {$newAutoIncrement}");

         // Step 8: Recreate dropped foreign keys
         foreach ($foreignKeys as $foreignKey) {
             Schema::table($foreignKey->TABLE_NAME, function ($table) use ($foreignKey, $columnName, $tableName) {
                 $table->foreign($foreignKey->COLUMN_NAME) // Column in child table
                       ->references($columnName)          // Column in parent table
                       ->on($tableName)                   // Parent table
                       ->onDelete('cascade');             // Or set your desired onDelete behavior
             });
         }
     }

        Schema::table('approval_requests', function (Blueprint $table) {

            $table->string('uri')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('approval_requests', function (Blueprint $table) {
            $table->dropColumn('uri');
        });
    }
};
