<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('employee_overtimes', function (Blueprint $table) {
            $table->decimal('weekday_amount_calculated', 12, 2)->default(0);
            $table->decimal('weekend_amount_calculated', 12, 2)->default(0);
            $table->decimal('holiday_amount_calculated', 12, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('employee_overtimes', function (Blueprint $table) {
            $table->dropColumn([
                'weekday_amount_calculated',
                'weekend_amount_calculated',
                'holiday_amount_calculated'
            ]);
        });
    }
};