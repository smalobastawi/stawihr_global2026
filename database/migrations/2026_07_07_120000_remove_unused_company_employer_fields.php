<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'employer_number')) {
                $table->dropColumn('employer_number');
            }
            if (Schema::hasColumn('companies', 'ecitizen_identifier')) {
                $table->dropColumn('ecitizen_identifier');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'employer_number')) {
                $table->string('employer_number')->nullable()->after('shif_employer_code');
            }
            if (!Schema::hasColumn('companies', 'ecitizen_identifier')) {
                $table->string('ecitizen_identifier')->nullable()->after('nita_registration_number');
            }
        });
    }
};
