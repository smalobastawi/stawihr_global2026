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

        Schema::table('employee_deductions', function (Blueprint $table) {
            // Make deduction_type_id nullable (harmonized column name)
            if (Schema::hasColumn('employee_deductions', 'deduction_type_id')) {
                $table->unsignedBigInteger('deduction_type_id')->nullable()->change();
            }
        });

        // Drop old foreign key name if it exists from previous broken migration
        try {
            DB::statement("ALTER TABLE employee_deductions DROP FOREIGN KEY IF EXISTS employee_deductions_payroll_deduction_type_id_foreign");
        } catch (\Exception $e) {
            // Ignore
        }

        // Add correct foreign key using the standardized column name
        DB::statement("
            ALTER TABLE employee_deductions 
            ADD CONSTRAINT employee_deductions_deduction_type_id_foreign 
            FOREIGN KEY (deduction_type_id) 
            REFERENCES deduction_types(id) 
            ON DELETE CASCADE 
            ON UPDATE CASCADE
        ");

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Drop the current foreign key
        try {
            DB::statement("
                ALTER TABLE employee_deductions 
                DROP FOREIGN KEY IF EXISTS employee_deductions_deduction_type_id_foreign
            ");
        } catch (\Exception $e) {
            // Ignore
        }

        // Restore non-nullable + original state
        Schema::table('employee_deductions', function (Blueprint $table) {
            $table->unsignedBigInteger('deduction_type_id')->nullable(false)->change();
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
