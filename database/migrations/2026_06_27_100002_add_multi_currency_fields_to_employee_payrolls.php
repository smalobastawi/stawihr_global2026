<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_payrolls', function (Blueprint $table) {
            $table->string('payment_currency', 3)->nullable()->after('currency');
            $table->string('exchange_rate_type', 30)->default('standard')->after('payment_currency');
            $table->string('bank_payment_currency', 3)->nullable()->after('exchange_rate_type');
        });
    }

    public function down(): void
    {
        Schema::table('employee_payrolls', function (Blueprint $table) {
            $table->dropColumn(['payment_currency', 'exchange_rate_type', 'bank_payment_currency']);
        });
    }
};
