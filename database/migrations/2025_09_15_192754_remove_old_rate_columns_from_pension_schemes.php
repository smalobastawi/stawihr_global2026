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
            // Check if each column exists before dropping
            if (Schema::hasColumn('pension_schemes', 'employee_contribution_rate')) {
                $table->dropColumn('employee_contribution_rate');
            }

            if (Schema::hasColumn('pension_schemes', 'employer_contribution_rate')) {
                $table->dropColumn('employer_contribution_rate');
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
        Schema::table('pension_schemes', function (Blueprint $table) {
            $table->decimal('employee_contribution_rate', 5, 2)->nullable()->after('provider_contact');
            $table->decimal('employer_contribution_rate', 5, 2)->nullable()->after('employee_contribution_rate');
        });
    }
};
