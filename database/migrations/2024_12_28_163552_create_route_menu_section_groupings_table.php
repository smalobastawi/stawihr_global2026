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
        Schema::table('grouped_menu_route_permissions', function (Blueprint $table) {
        //$table->id();
        $table->unsignedBigInteger('module_id');
        //$table->string('module');
        //$table->string('section');
        $table->string('sub_section')->nullable(); 
        $table->string('sub_section_description')->nullable(); 
        //$table->string('route_name')->unique();
        $table->string('actiontype')->nullable();
        //$table->string('description')->nullable();
       // $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grouped_menu_route_permissions', function (Blueprint $table) {
            $table->dropColumn('module_id');
            $table->dropColumn('sub_section');
            $table->dropColumn('actiontype');
            $table->dropColumn('sub_section_description');
        });
    }
};
