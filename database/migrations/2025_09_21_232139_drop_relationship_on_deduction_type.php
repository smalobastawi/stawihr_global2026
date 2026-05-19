<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // First, drop any existing foreign key
        try {
            Schema::table('employee_deductions', function (Blueprint $table) {
                $table->dropForeign(['payroll_deduction_type_id']);
            });
        } catch (\Exception $e) {
            // Ignore error if foreign key doesn't exist
        }

        // Ensure the column is nullable and has the correct type
        Schema::table('employee_deductions', function (Blueprint $table) {
            $table->unsignedBigInteger('payroll_deduction_type_id')->nullable()->change();
        });

        // Use raw SQL to add the foreign key (more reliable)
        DB::statement("
            ALTER TABLE employee_deductions 
            ADD CONSTRAINT employee_deductions_payroll_deduction_type_id_foreign 
            FOREIGN KEY (payroll_deduction_type_id) 
            REFERENCES deduction_types(id) 
            ON DELETE SET NULL 
            ON UPDATE CASCADE
        ");

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Drop using raw SQL
        DB::statement("
            ALTER TABLE employee_deductions 
            DROP FOREIGN KEY employee_deductions_payroll_deduction_type_id_foreign
        ");

        // Restore original if needed
        if (Schema::hasTable('payroll_deduction_types')) {
            DB::statement("
                ALTER TABLE employee_deductions 
                ADD FOREIGN KEY (payroll_deduction_type_id) 
                REFERENCES payroll_deduction_types(id) 
                ON DELETE CASCADE 
                ON UPDATE CASCADE
            ");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
