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
        Schema::create('disciplinary_case_actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('case_id');
            $table->integer('action_type');
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('action_by');
            $table->date('action_date');
            $table->foreign('case_id')->references('id')->on('disciplinary_cases')->onDelete('cascade');    
            $table->foreign('action_by')->references('employee_id')->on('employee')->onDelete('cascade');
            $table->string('status')->default(0);
            $table->string('attachment')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by')->references('employee_id')->on('employee')->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('disciplinary_case_actions');
    }
};
