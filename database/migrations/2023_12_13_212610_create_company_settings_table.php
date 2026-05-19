<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('legal_Name')->default('Test Company');
            $table->string('legal_Address')->default('Legal Address 1');
            $table->string('official_contact_number')->default('254712345678');
            $table->string('official_email')->default('email@example.com');

            $table->string('company_contact_name')->default('John Does');
            $table->string('representative_phone')->default('254712345678');
            $table->string('representative_email')->default('email@example.com');

            $table->string('KRA_PIN')->default('P01111111');
            $table->string('employer_number')->default('11111111');
            $table->string('NSSF_employer_number')->default('11111111');
            $table->string('NHIF_employer_code')->default('1111111111');

            $table->date('financial_year_start')->default('2024-01-01');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_settings');
    }
};
