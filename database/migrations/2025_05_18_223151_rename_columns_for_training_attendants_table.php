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
        Schema::table('training_attendants', function (Blueprint $table) {
            //
            $table->renameColumn('approved', 'status');
            $table->dropColumn('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('training_attendants', function (Blueprint $table) {
            //
            $table->renameColumn('approved', 'status');
            $table->unsignedBigInteger('approved_by')->nullable();
        });
    }
};
