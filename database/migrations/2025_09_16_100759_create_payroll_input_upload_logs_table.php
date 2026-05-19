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
        if (Schema::hasTable('payroll_input_upload_logs')) {
            return;
        }
        Schema::create('payroll_input_upload_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained('payroll_periods')->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('user')->onDelete('cascade');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->string('file_name')->nullable(); // Store original file name
            $table->string('file_path')->nullable(); // Store path to uploaded file if needed
            $table->json('details')->nullable(); // Store granular statuses or other relevant details
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
        Schema::dropIfExists('payroll_input_upload_logs');
    }
};
