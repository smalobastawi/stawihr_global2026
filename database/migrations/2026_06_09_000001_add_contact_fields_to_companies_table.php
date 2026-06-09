<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->text('address')->nullable()->after('country');
            $table->string('official_contact_number')->nullable()->after('address');
            $table->string('official_email')->nullable()->after('official_contact_number');
            $table->string('company_contact_name')->nullable()->after('official_email');
            $table->string('representative_phone')->nullable()->after('company_contact_name');
            $table->string('representative_email')->nullable()->after('representative_phone');
            $table->text('print_head_description')->nullable()->after('representative_email');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'official_contact_number',
                'official_email',
                'company_contact_name',
                'representative_phone',
                'representative_email',
                'print_head_description',
            ]);
        });
    }
};
