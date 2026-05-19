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
        Schema::table('payroll_earning_types', function (Blueprint $table) {
            if (!Schema::hasColumn('payroll_earning_types', 'calculation_type')) {
                $table->string('calculation_type')->default('fixed_amount')->after('percentage_of_basic');
            }
            if (!Schema::hasColumn('payroll_earning_types', 'is_pensionable')) {
                $table->boolean('is_pensionable')->default(false)->after('taxable');
            }
            if (!Schema::hasColumn('payroll_earning_types', 'is_recurring')) {
                $table->boolean('is_recurring')->default(false)->after('is_pensionable');
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
        Schema::table('payroll_earning_types', function (Blueprint $table) {
            if (Schema::hasColumn('payroll_earning_types', 'calculation_type')) {
                $table->dropColumn('calculation_type');
            }
            if (Schema::hasColumn('payroll_earning_types', 'is_pensionable')) {
                $table->dropColumn('is_pensionable');
            }
            if (Schema::hasColumn('payroll_earning_types', 'is_recurring')) {
                $table->dropColumn('is_recurring');
            }
        });
    }
};