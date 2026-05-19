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
        Schema::table('termination', function (Blueprint $table) {
            $table->tinyInteger('arrears_paid')->default(0)->after('status')->comment('0=Not Paid, 1=Arrears Paid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('termination', function (Blueprint $table) {
            $table->dropColumn('arrears_paid');
        });
    }
};
