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
        Schema::create('disciplinary_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('assigned_officer')->nullable();
            $table->string('location')->nullable();
            $table->string('attachment')->nullable();
            $table->date('date_of_incident');
            $table->date('date_of_report');
            $table->unsignedBigInteger('reporter_id')->nullable();
            $table->integer('status')->default(0);
            $table->foreign('category_id')->references('id')->on('disciplinary_categories')->onDelete('cascade');
            $table->foreign('employee_id')->references('employee_id')->on('employee')->onDelete('cascade');
            $table->foreign('reporter_id')->references('employee_id')->on('employee')->onDelete('cascade');
            $table->foreign('assigned_officer')->references('employee_id')->on('employee')->onDelete('cascade');    
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
        Schema::dropIfExists('disciplinary_cases');
    }
};
