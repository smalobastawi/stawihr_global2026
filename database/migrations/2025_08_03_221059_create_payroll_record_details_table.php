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
        Schema::create('payroll_record_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_record_id')->constrained('payroll_records')->onDelete('cascade');
            $table->enum('type', ['allowance', 'deduction', 'statutory_deduction']);
            $table->string('name');
            $table->string('code');
            $table->decimal('amount', 10, 2);
            $table->decimal('calculation_basis', 10, 2)->nullable();
            $table->decimal('rate', 6, 4)->nullable();
            $table->boolean('is_taxable')->default(false);
            $table->boolean('is_pensionable')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_record_details');
    }
};