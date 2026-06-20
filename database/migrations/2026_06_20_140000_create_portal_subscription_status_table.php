<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_subscription_status', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_suspended')->default(false);
            $table->text('reason')->nullable();
            $table->string('support_email')->nullable();
            $table->string('support_phone')->nullable();
            $table->unsignedBigInteger('portal_subscription_id')->nullable();
            $table->string('domain')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('unsuspended_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_subscription_status');
    }
};
