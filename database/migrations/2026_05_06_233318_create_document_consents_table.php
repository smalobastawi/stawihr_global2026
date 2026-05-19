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
        Schema::create('document_consents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('consented_at');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('acknowledgment_text')->default('I have read and understood this document and agree to abide by the terms stated therein.');
            $table->timestamps();

            // Foreign keys
            $table->foreign('document_id')->references('id')->on('hr_documents')->onDelete('cascade');
            $table->foreign('employee_id')->references('employee_id')->on('employee')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');

            // Unique constraint to prevent duplicate consents
            $table->unique(['document_id', 'employee_id'], 'unique_document_employee_consent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_consents');
    }
};
