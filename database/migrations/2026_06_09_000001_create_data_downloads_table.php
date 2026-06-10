<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_downloads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject', 'data_downloads_subject');
            $table->string('event')->nullable();
            $table->nullableMorphs('causer', 'data_downloads_causer');
            $table->json('properties')->nullable();
            $table->uuid('batch_uuid')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->timestamps();

            $table->index('log_name');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_downloads');
    }
};
