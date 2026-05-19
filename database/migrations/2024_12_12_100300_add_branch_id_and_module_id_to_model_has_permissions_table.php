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
        Schema::table('modules', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->change();
        });
        
            // Schema::table('model_has_permissions', function (Blueprint $table) {
            //     $table->unsignedBigInteger('location_id')->nullable()->after('model_id');
            //     $table->unsignedBigInteger('module_id')->nullable()->after('location_id');
        
            //     $table->foreign('location_id')->references('location_id')->on('branch')->onDelete('cascade');
            //     $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
            // });
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      
            // Schema::table('model_has_permissions', function (Blueprint $table) {
            //     $table->dropForeign(['location_id']);
            //     $table->dropForeign(['module_id']);
            //     $table->dropColumn(['location_id', 'module_id']);
            // });
    }
};
