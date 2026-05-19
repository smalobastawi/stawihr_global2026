<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('financial_years', function (Blueprint $table) {
            $table->id(); // Primary key, auto-incrementing bigint(20) UNSIGNED
           
            $table->string('name', 255)->index(); // Indexed varchar(255), not nullable
            $table->text('description')->nullable(); // text, nullable
            $table->tinyInteger('status')->default(1); // tinyint(1), default value 1
            $table->timestamps(); // created_at and updated_at timestamps
            $table->softDeletes(); // deleted_at timestamp for soft deletes
            $table->unsignedBigInteger('created_by')->nullable(); // bigint(20) UNSIGNED, nullable
            $table->unsignedBigInteger('updated_by')->nullable(); // bigint(20) UNSIGNED, nullable
            $table->unsignedBigInteger('deleted_by')->nullable(); // bigint(20) UNSIGNED, nullable
            $table->char('uuid', 36)->unique(); // char(36), unique
            $table->date('start_date')->nullable(); // date, nullable
            $table->date('end_date')->nullable(); // date, nullable
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_years');
    }
};
