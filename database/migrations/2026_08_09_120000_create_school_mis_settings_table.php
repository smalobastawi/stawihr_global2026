<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_mis_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_enabled')->default(false);
            $table->string('school_base_url')->nullable();
            $table->text('school_api_key')->nullable();
            $table->string('pull_api_token_hash', 64)->nullable();
            $table->string('pull_api_token_hint')->nullable();
            $table->timestamp('pull_api_token_generated_at')->nullable();
            $table->timestamp('pull_api_token_revoked_at')->nullable();
            $table->boolean('push_on_employee_save')->default(true);
            $table->boolean('push_on_leave_approve')->default(true);
            $table->unsignedSmallInteger('timeout')->default(30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_mis_settings');
    }
};
