<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anonymized_record_backups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->json('user_data');
            $table->json('employee_data')->nullable();
            $table->json('role_names')->nullable();
            $table->unsignedBigInteger('anonymized_by')->nullable();
            $table->timestamp('anonymized_at');
            $table->unsignedBigInteger('restored_by')->nullable();
            $table->timestamp('restored_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anonymized_record_backups');
    }
};
