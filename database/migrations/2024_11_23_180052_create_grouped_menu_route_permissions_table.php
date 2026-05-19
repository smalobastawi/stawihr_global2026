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
        Schema::create('grouped_menu_route_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('menu_name')->nullable(true);
            $table->string('permission_group');
            $table->string('group_description');
            $table->string('permission');
            $table->string('permission_description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grouped_menu_route_permissions');
    }
};
