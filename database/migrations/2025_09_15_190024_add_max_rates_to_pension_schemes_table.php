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
        Schema::table('pension_schemes', function (Blueprint $table) {
            $table->decimal('max_employee_rate', 5, 2)->nullable()->after('employer_contribution_rate');
            $table->decimal('max_employer_rate', 5, 2)->nullable()->after('max_employee_rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pension_schemes', function (Blueprint $table) {
            $table->dropColumn(['max_employee_rate', 'max_employer_rate']);
        });
    }
};
