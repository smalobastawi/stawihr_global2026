<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdp_settings', function (Blueprint $table) {
            $table->id('pdp_setting_id');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->enum('default_review_frequency', ['quarterly', 'bi_annually', 'annually'])->default('quarterly');
            $table->boolean('allow_employee_self_service')->default(true);
            $table->boolean('require_supervisor_approval')->default(true);
            $table->boolean('require_hr_review')->default(false);
            $table->text('policy_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdp_settings');
    }
};
