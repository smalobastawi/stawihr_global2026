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
        Schema::table('morpho_device_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('morpho_device_logs', 'updated_status')) {
                $table->integer('updated_status')->default(0)->after('payroll_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('morpho_device_logs', function (Blueprint $table) {
            $table->dropColumn('updated_status');
        });
    }
};
