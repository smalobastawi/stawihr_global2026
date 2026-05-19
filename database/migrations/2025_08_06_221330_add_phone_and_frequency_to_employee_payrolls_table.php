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
        Schema::table('employee_payrolls', function (Blueprint $table) {
            // Add phone number field
            $table->string('phone_number', 20)->nullable()
                  ->comment('Employee phone number for payroll communication');
            
            // Add income frequency field
            $table->enum('income_frequency', ['daily', 'weekly', 'monthly'])
                  ->default('monthly')
                  ->comment('Frequency of income payment (daily, weekly, monthly)');
            
            // Add indexes for performance
            $table->index(['income_frequency']);
            $table->index(['phone_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_payrolls', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['income_frequency']);
            $table->dropIndex(['phone_number']);
            
            // Drop columns
            $table->dropColumn([
                'income_frequency',
                'phone_number'
            ]);
        });
    }
};