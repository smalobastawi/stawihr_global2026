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
        Schema::table('salary_details', function (Blueprint $table) {
            $table->integer('actual_gross_pay')->nullable()->default(0)->comment('gross pay after deducting lost days');
            $table->integer('total_gross_pay')->nullable()->default(0)->comment('gross before lost days');
            $table->dateTime('payment_period_start')->nullable();
            $table->dateTime('payment_period_end')->nullable();
            $table->integer('payout_channel')->nullable()->default(0)->comment('Payout channels are Banks, saccos etc');
            $table->integer('payout_status')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salary_details', function (Blueprint $table) {
            $table->dropColumn('actual_gross_pay');
            $table->dropColumn('payment_period_start');
            $table->dropColumn('payment_period_start');
            $table->dropColumn('payment_period_end');
            $table->dropColumn('payout_channel');
            $table->dropColumn('payout_status');
        });
    }
};
