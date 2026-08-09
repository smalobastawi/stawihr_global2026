<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (! Schema::hasColumn('vehicles', 'school_vehicle_id')) {
                $table->unsignedBigInteger('school_vehicle_id')->nullable()->unique()->after('id');
            }
            if (! Schema::hasColumn('vehicles', 'sync_origin')) {
                $table->string('sync_origin', 20)->nullable()->after('remarks');
            }
            if (! Schema::hasColumn('vehicles', 'last_synced_at')) {
                $table->timestamp('last_synced_at')->nullable()->after('sync_origin');
            }
        });

        Schema::table('school_mis_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('school_mis_settings', 'sync_vehicles')) {
                $table->boolean('sync_vehicles')->default(true)->after('push_on_leave_approve');
            }
            if (! Schema::hasColumn('school_mis_settings', 'push_on_vehicle_change')) {
                $table->boolean('push_on_vehicle_change')->default(true)->after('sync_vehicles');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            foreach (['school_vehicle_id', 'sync_origin', 'last_synced_at'] as $column) {
                if (Schema::hasColumn('vehicles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('school_mis_settings', function (Blueprint $table) {
            foreach (['sync_vehicles', 'push_on_vehicle_change'] as $column) {
                if (Schema::hasColumn('school_mis_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
