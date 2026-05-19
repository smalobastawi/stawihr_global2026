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
        Schema::table('payroll_records', function (Blueprint $table) {
            $table->decimal('nssf_tier1_contribution', 10, 2)->default(0)->after('nssf_contribution');
            $table->decimal('nssf_tier2_contribution', 10, 2)->default(0)->after('nssf_tier1_contribution');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payroll_records', function (Blueprint $table) {
            $table->dropColumn('nssf_tier1_contribution');
            $table->dropColumn('nssf_tier2_contribution');
        });
    }
};