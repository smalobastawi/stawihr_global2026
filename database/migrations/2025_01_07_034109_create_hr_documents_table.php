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
        // Create the hr_documents table
        Schema::create('hr_documents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('file_path');
            $table->longText('description');
            $table->unsignedBigInteger('reviewed_by')->nullable()->constrained('user');
            $table->longText('review_comment')->nullable();
            $table->longText('approval_comment')->nullable();
            $table->unsignedBigInteger('category_id')->constrained('document_categories');
            $table->unsignedBigInteger('created_by')->constrained('user');
            $table->unsignedBigInteger('updated_by')->constrained('user')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable()->constrained('user');
            $table->longText('file_hash')->nullable();
            $table->json('approved_by')->nullable();
            $table->json('rejected_by')->nullable();
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
        // Drop the hr_documents table
        Schema::dropIfExists('hr_documents');
    }
};
