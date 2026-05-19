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
        $databaseName = DB::getDatabaseName();

        // Drop foreign key constraints if they exist
        $foreignKeys = [
            'employee_job_category_foreign',
            'employee_job_group_id_foreign',
            'employee_jobgroupid_foreign',
        ];

        foreach ($foreignKeys as $constraintName) {
            try {
                $exists = DB::select(
                    "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
                     WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'employee' AND CONSTRAINT_NAME = ?",
                    [$databaseName, $constraintName]
                );

                if (!empty($exists)) {
                    DB::statement("ALTER TABLE employee DROP FOREIGN KEY {$constraintName}");
                }
            } catch (\Exception $e) {
                // Ignore errors for non-existent foreign keys
            }
        }

        // Also drop any indexes on these columns
        try {
            DB::statement('ALTER TABLE employee DROP INDEX IF EXISTS employee_job_category_foreign');
        } catch (\Exception $e) {}
        try {
            DB::statement('ALTER TABLE employee DROP INDEX IF EXISTS employee_job_category_index');
        } catch (\Exception $e) {}

        // Now drop the columns if they exist
        Schema::table('employee', function (Blueprint $table) {
            if (Schema::hasColumn('employee', 'job_category')) {
                $table->dropColumn('job_category');
            }
            if (Schema::hasColumn('employee', 'jobGroupId')) {
                $table->dropColumn('jobGroupId');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee', function (Blueprint $table) {
            $table->integer('job_category')->nullable();
            $table->integer('jobGroupId')->nullable();
        });
    }
};
