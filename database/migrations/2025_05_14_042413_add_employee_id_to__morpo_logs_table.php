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
        // First, add the column and foreign key as you've shown
        Schema::table('morpho_device_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id')->nullable()->after('id_no');

            // Add foreign key constraint
            $table->foreign('employee_id')
                ->references('employee_id')
                ->on('employee')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->index('employee_id', 'morpho_device_logs_employee_id_index');
        });

        // Then run a migration to populate the employee_id
        DB::table('morpho_device_logs')
            ->whereNull('employee_id')
            ->chunkById(200, function ($logs) {
                foreach ($logs as $log) {
                    $employee = DB::table('employee')
                        ->where('payroll_number', $log->payroll_number)
                        ->first();

                    if ($employee) {
                        DB::table('morpho_device_logs')
                            ->where('id', $log->id)
                            ->update(['employee_id' => $employee->employee_id]);
                    }
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
        Schema::table('morpho_device_logs', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['employee_id']);

            // Drop the index
            $table->dropIndex('morpho_device_logs_employee_id_index');

            // Drop the column
            $table->dropColumn('employee_id');
        });
    }
};
