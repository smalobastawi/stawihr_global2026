<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('payroll_base_currency', 3)->nullable()->after('currency');
            $table->string('default_payment_currency', 3)->nullable()->after('payroll_base_currency');
            $table->string('exchange_rate_source', 20)->default('manual')->after('default_payment_currency');
            $table->string('exchange_rate_effective_date_policy', 30)->default('payroll_period_end')->after('exchange_rate_source');
            $table->boolean('allow_employee_payment_currency')->default(false)->after('exchange_rate_effective_date_policy');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'payroll_base_currency',
                'default_payment_currency',
                'exchange_rate_source',
                'exchange_rate_effective_date_policy',
                'allow_employee_payment_currency',
            ]);
        });
    }
};
