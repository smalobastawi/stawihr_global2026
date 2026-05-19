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
        Schema::table('surveys', function (Blueprint $table) {
            //
            $table->tinyInteger('target_gender')->nullable()->after('end_date');
            $table->unsignedBigInteger('created_by')->nullable()->after('target_gender');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('surveys', function (Blueprint $table) {
            //
            $table->dropColumn('target_gender');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
    }
};
