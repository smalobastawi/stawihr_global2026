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
        Schema::table('leave_group_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('leave_group_settings', 'allow_advanced_leave')) {
                $table->boolean('allow_advanced_leave')->default(false)->after('max_consecutive_days');
            }

            if (!Schema::hasColumn('leave_group_settings', 'advanced_period_months')) {
                $table->integer('advanced_period_months')->default(1)->after('allow_advanced_leave');
            }

            if (!Schema::hasColumn('leave_group_settings', 'advanced_limit_days')) {
                $table->decimal('advanced_limit_days', 8, 2)->nullable()->after('advanced_period_months');
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
        Schema::table('leave_group_settings', function (Blueprint $table) {
            if (Schema::hasColumn('leave_group_settings', 'advanced_limit_days')) {
                $table->dropColumn('advanced_limit_days');
            }

            if (Schema::hasColumn('leave_group_settings', 'advanced_period_months')) {
                $table->dropColumn('advanced_period_months');
            }

            if (Schema::hasColumn('leave_group_settings', 'allow_advanced_leave')) {
                $table->dropColumn('allow_advanced_leave');
            }
        });
    }
};
