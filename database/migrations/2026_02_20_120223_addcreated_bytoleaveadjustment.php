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
        Schema::table('leave_adjustments', function (Blueprint $table) {
            if (!Schema::hasColumn('leave_adjustments', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable();
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
        Schema::table('leave_adjustments', function (Blueprint $table) {
            if (Schema::hasColumn('leave_adjustments', 'created_by')) {
                $table->dropColumn('created_by');
            }
        });
    }
};
