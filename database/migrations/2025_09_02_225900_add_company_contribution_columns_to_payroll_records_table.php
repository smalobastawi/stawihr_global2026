<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payroll_records', function (Blueprint $table) {
            $table->decimal('industrial_training_levy', 12, 2)->default(0)->after('pension_contribution');
            $table->decimal('nssf_tier1_company_contribution', 12, 2)->default(0)->after('industrial_training_levy');
            $table->decimal('nssf_tier2_company_contribution', 12, 2)->default(0)->after('nssf_tier1_company_contribution');
            $table->decimal('housing_levy_company_contribution', 12, 2)->default(0)->after('nssf_tier2_company_contribution');
            $table->decimal('employer_pension_contribution', 12, 2)->default(0)->after('housing_levy_company_contribution');
            $table->decimal('unpaid_amount', 12, 2)->default(0)->after('employer_pension_contribution');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_records', function (Blueprint $table) {
            $table->dropColumn([
                'industrial_training_levy',
                'nssf_tier1_company_contribution',
                'nssf_tier2_company_contribution',
                'housing_levy_company_contribution',
                'employer_pension_contribution',
                'unpaid_amount'
            ]);
        });
    }
};