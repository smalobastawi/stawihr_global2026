<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currency_exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('from_currency', 3);
            $table->string('to_currency', 3);
            $table->decimal('rate', 18, 8);
            $table->date('effective_date');
            $table->unsignedBigInteger('payroll_period_id')->nullable();
            $table->string('source', 20)->default('manual');
            $table->string('status', 20)->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
            $table->foreign('payroll_period_id')->references('id')->on('payroll_periods')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('user')->nullOnDelete();
            $table->foreign('approved_by')->references('id')->on('user')->nullOnDelete();

            $table->index(['company_id', 'from_currency', 'to_currency', 'effective_date'], 'cer_company_pair_date_idx');
            $table->index(['status', 'payroll_period_id'], 'cer_status_period_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currency_exchange_rates');
    }
};
