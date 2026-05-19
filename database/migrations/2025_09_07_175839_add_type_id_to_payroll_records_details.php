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
        Schema::table('payroll_record_details', function (Blueprint $table) {
            $table->unsignedBigInteger('type_id')->nullable()->after('payroll_record_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payroll_record_details', function (Blueprint $table) {
            $table->dropColumn('type_id');
        });
    }
};
