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
        Schema::table('employee', function (Blueprint $table) {
            //
            $table->dropColumn('emergency_contacts');
            $table->string('emergency_name')->after('address')->nullable();
            $table->string('emergency_phone')->after('emergency_name')->nullable();
            $table->tinyInteger('emergency_relationship')->after('emergency_phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee', function (Blueprint $table) {
            //
            $table->text('emergency_contacts')->after('address')->nullable();
            $table->dropColumn('emergency_name');
            $table->dropColumn('emergency_phone');
            $table->dropColumn('emergency_relationship');
        });
    }
};
