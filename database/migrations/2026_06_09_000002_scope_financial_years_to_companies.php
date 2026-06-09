<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('financial_years', 'company_id')) {
            Schema::table('financial_years', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            });
        }

        $defaultCompanyId = DB::table('companies')->orderBy('id')->value('id');
        if ($defaultCompanyId) {
            DB::table('financial_years')->whereNull('company_id')->update(['company_id' => $defaultCompanyId]);
        }

        Schema::table('financial_years', function (Blueprint $table) {
            $indexes = collect(DB::select('SHOW INDEX FROM financial_years'))
                ->pluck('Key_name')
                ->unique();

            if ($indexes->contains('financial_years_name_unique')) {
                $table->dropUnique('financial_years_name_unique');
            }

            if (!$indexes->contains('financial_years_company_id_name_unique')) {
                $table->unique(['company_id', 'name'], 'financial_years_company_id_name_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('financial_years', function (Blueprint $table) {
            $indexes = collect(DB::select('SHOW INDEX FROM financial_years'))
                ->pluck('Key_name')
                ->unique();

            if ($indexes->contains('financial_years_company_id_name_unique')) {
                $table->dropUnique('financial_years_company_id_name_unique');
            }
        });

        if (Schema::hasColumn('financial_years', 'company_id')) {
            Schema::table('financial_years', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }
    }
};
