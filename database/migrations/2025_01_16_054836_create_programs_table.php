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
        Schema::create('programs', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Program name
            $table->date('start_date'); // Program start date
            $table->date('end_date'); // Program end date
            $table->unsignedBigInteger('main_program')->nullable(); // Parent program (self-referencing)
            $table->foreign('main_program')->references('id')->on('programs')->nullOnDelete(); // Foreign key constraint
            $table->unsignedBigInteger('created_by'); // User who created the program
            $table->unsignedBigInteger('updated_by')->nullable(); // User who last updated the program
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active'); // Program status
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('programs');
    }
};
