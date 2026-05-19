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
    Schema::table('employee_deductions', function (Blueprint $table) {
        if (!Schema::hasColumn('employee_deductions', 'deleted_at')) {
            $table->softDeletes();
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
    Schema::table('employee_deductions', function (Blueprint $table) {
        if (Schema::hasColumn('employee_deductions', 'deleted_at')) {
            $table->dropSoftDeletes();  // or $table->dropColumn('deleted_at');
        }
    });
}
};
