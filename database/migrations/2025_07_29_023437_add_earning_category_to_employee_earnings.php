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
        Schema::table('employee_earnings', function (Blueprint $table) {
            $table->string('earning_category')->nullable()->after('approval_notes');
            $table->integer('status')->default(1)->after('earning_category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_earnings', function (Blueprint $table) {
            $table->dropColumn('earning_category');
            $table->dropColumn('status');
        });
    }
};
