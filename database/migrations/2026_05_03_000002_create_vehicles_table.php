<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vehicles')) {
            return;
        }
        
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique();
            $table->string('make');
            $table->string('model');
            $table->year('year_of_manufacture')->nullable();
            $table->string('color')->nullable();
            $table->string('chassis_number')->nullable();
            $table->string('engine_number')->nullable();
            $table->unsignedBigInteger('vehicle_type_id')->nullable();
            $table->string('fuel_type')->nullable(); // petrol, diesel, electric, hybrid
            $table->decimal('fuel_capacity', 10, 2)->nullable(); // in liters
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->string('ownership_status')->default('company'); // company, leased, rented
            $table->unsignedBigInteger('location_id')->nullable(); // current location of vehicle
            $table->string('status')->default('active'); // active, maintenance, retired, sold
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types')->onDelete('set null');
            $table->foreign('location_id')->references('location_id')->on('location')->onDelete('set null');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->index(['status', 'company_id']);
            $table->index(['registration_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
