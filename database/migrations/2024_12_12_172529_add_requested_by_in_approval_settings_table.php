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
        Schema::table('approval_records', function (Blueprint $table) {
            
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->longText('approval_notes')->nullable();
            $table->longText('rejection_notes')->nullable();
            $table->string('action_type')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('approval_settings', function (Blueprint $table) {
            //
        });
    }
};
