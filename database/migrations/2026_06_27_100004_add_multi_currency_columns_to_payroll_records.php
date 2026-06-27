<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_records', function (Blueprint $table) {
            $table->string('base_currency', 3)->nullable()->after('metadata');
            $table->string('payment_currency', 3)->nullable()->after('base_currency');
            $table->decimal('exchange_rate_used', 18, 8)->nullable()->after('payment_currency');
            $table->date('exchange_rate_date')->nullable()->after('exchange_rate_used');
            $table->unsignedBigInteger('exchange_rate_id')->nullable()->after('exchange_rate_date');

            $table->decimal('taxable_income_base_currency', 14, 2)->nullable()->after('exchange_rate_id');
            $table->decimal('gross_payment_currency', 14, 2)->nullable()->after('taxable_income_base_currency');
            $table->decimal('total_deductions_payment_currency', 14, 2)->nullable()->after('gross_payment_currency');
            $table->decimal('net_pay_payment_currency', 14, 2)->nullable()->after('total_deductions_payment_currency');
            $table->text('currency_conversion_notes')->nullable()->after('net_pay_payment_currency');

            $table->foreign('exchange_rate_id')->references('id')->on('currency_exchange_rates')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payroll_records', function (Blueprint $table) {
            $table->dropForeign(['exchange_rate_id']);
            $table->dropColumn([
                'base_currency',
                'payment_currency',
                'exchange_rate_used',
                'exchange_rate_date',
                'exchange_rate_id',
                'taxable_income_base_currency',
                'gross_payment_currency',
                'total_deductions_payment_currency',
                'net_pay_payment_currency',
                'currency_conversion_notes',
            ]);
        });
    }
};
