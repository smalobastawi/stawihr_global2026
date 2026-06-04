<?php

use App\Lib\Enumerations\PayrollCountry;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'payroll_country')) {
                $table->unsignedTinyInteger('payroll_country')
                    ->default(PayrollCountry::KENYA)
                    ->after('domain');
            }
        });

        $companies = DB::table('companies')->select('id', 'country')->get();

        foreach ($companies as $company) {
            $payrollCountry = PayrollCountry::KENYA;

            if (!empty($company->country)) {
                $mapped = PayrollCountry::getValue($company->country);
                if ($mapped !== null) {
                    $payrollCountry = $mapped;
                }
            }

            DB::table('companies')
                ->where('id', $company->id)
                ->update([
                    'payroll_country' => $payrollCountry,
                    'country' => PayrollCountry::getName($payrollCountry),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'payroll_country')) {
                $table->dropColumn('payroll_country');
            }
        });
    }
};
