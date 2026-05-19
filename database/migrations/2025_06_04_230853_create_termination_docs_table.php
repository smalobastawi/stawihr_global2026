<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up()
    {
      

        Schema::create('termination_docs', function (Blueprint $table) {
            $table->id();
          $table->unsignedBigInteger('termination_id');
            $table->unsignedBigInteger('employee_id');
            $table->foreign('termination_id')->references('termination_id')->on('termination')->onDelete('cascade');
            $table->foreign('employee_id')->references('employee_id')->on('employee')->onDelete('cascade');

          
            $table->string('document_name');
            $table->string('file_url')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('termination_docs');
    }
};
