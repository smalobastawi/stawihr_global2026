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
        Schema::create('training_facilitators', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Facilitator's name
            $table->string('contact_email')->nullable(); // Facilitator's email
            $table->string('contact_phone')->nullable(); // Facilitator's phone number
            $table->enum('type',['internal','external'])->default('internal'); // 'internal' or 'external'
            $table->string('expertise'); // Expertise area or field
            $table->text('notes')->nullable(); // Any additional information about the facilitator
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
        Schema::dropIfExists('training_facilitators');
    }
};
