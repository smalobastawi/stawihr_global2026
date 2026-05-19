<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSsfTiersToSalaryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salary_details', function (Blueprint $table) {
            $table->string('nssf_tier_1')->nullable();
            $table->string('nssf_tier_2')->nullable();
            $table->string('total_nssf')->nullable();
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
            $table->dropColumn(['nssf_tier_1']);
            $table->dropColumn(['nssf_tier_2']);
            $table->dropColumn(['total_nssf']);
        });
    }
}
