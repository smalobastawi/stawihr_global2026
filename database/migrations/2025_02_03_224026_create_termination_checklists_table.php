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
        Schema::create('termination_checklists', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('checklist_name');
            $table->text('description');
            $table->text('comment')->nullable();
            $table->foreignId('cleared_by')->nullable()->constrained('user');
            $table->foreignId('employee_id')->constrained('employee','employee_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('termination_checklists');
    }
};
