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
        Schema::table('companies', function (Blueprint $table) {
            // Kenya Revenue Authority PIN
            $table->string('kra_pin')->nullable()->after('status');

            // Company Registration Number from Registrar of Companies
            $table->string('registration_number')->nullable()->after('kra_pin');

            // NSSF Employer Number (National Social Security Fund)
            $table->string('nssf_employer_number')->nullable()->after('registration_number');

            // SHIF Employer Code (Social Health Insurance Fund, formerly NHIF)
            $table->string('shif_employer_code')->nullable()->after('nssf_employer_number');

            // General Employer Number
            $table->string('employer_number')->nullable()->after('shif_employer_code');

            // NITA Registration Number (National Industrial Training Authority)
            $table->string('nita_registration_number')->nullable()->after('employer_number');

            // eCitizen Identifier
            $table->string('ecitizen_identifier')->nullable()->after('nita_registration_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'kra_pin',
                'registration_number',
                'nssf_employer_number',
                'shif_employer_code',
                'employer_number',
                'nita_registration_number',
                'ecitizen_identifier',
            ]);
        });
    }
};
