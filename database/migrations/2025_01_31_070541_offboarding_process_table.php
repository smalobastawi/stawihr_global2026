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
        Schema::create('offboarding_process', function (Blueprint $table) {
            $table->id();
            $table->string('checklist_name');
            $table->string('description');
            $table->boolean('cleared')->default(false);
            $table->string('comment');
            $table->unsignedBigInteger('cleared_by_id')->nullable()->constrained('user');
            $table->unsignedBigInteger('created_by')->constrained('user');
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
        //
    }
};
