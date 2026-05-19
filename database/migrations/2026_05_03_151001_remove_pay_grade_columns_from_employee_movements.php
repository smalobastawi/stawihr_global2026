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
        Schema::table('employee_movements', function (Blueprint $table) {
            $table->dropColumn('current_pay_grade');
            $table->dropColumn('new_pay_grade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_movements', function (Blueprint $table) {
            $table->integer('current_pay_grade')->nullable();
            $table->integer('new_pay_grade')->nullable();
        });
    }
};
