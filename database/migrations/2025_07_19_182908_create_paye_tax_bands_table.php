<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('paye_tax_bands', function (Blueprint $table) {
            $table->id();
            $table->integer('country_id'); // e.g., 'KE', 'UG', 'TZ'
            $table->string('country_name');
            $table->integer('band_order');
            $table->decimal('monthly_lower_bound', 12, 2);
            $table->decimal('monthly_upper_bound', 12, 2)->nullable();
            $table->decimal('annual_lower_bound', 12, 2);
            $table->decimal('annual_upper_bound', 12, 2)->nullable();
            $table->decimal('tax_rate', 5, 2);
            $table->timestamps();
            
            $table->unique(['country_id', 'band_order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('paye_tax_bands');
    }
};