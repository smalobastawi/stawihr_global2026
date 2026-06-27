<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dummy_data_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('summary')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('user')->nullOnDelete();
        });

        Schema::create('dummy_data_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_id');
            $table->string('table_name', 100);
            $table->unsignedBigInteger('record_id');
            $table->timestamps();

            $table->foreign('batch_id')->references('id')->on('dummy_data_batches')->cascadeOnDelete();
            $table->index(['batch_id', 'table_name']);
            $table->unique(['batch_id', 'table_name', 'record_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dummy_data_records');
        Schema::dropIfExists('dummy_data_batches');
    }
};
