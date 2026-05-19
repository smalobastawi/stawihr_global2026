<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = DB::select('SHOW TABLES');
        $skipTable = [
            'permissions',
            'activity_log',
            'course_unit_student',
            'designations',
            'error_logs',
            'failed_jobs',
            'jobs',
            'model_has_permissions',
            'model_has_roles',
            'modules',
            'oauth_access_tokens',
            'oauth_auth_codes',
            'oauth_clients',
            'oauth_personal_access_clients',
            'oauth_refresh_tokens',
            'password_reset_tokens',
            'personal_access_tokens',
            'role_has_permissions',
            'logs',
            'training_view',
        ];

        foreach ($tables as $table) {
            $tableName = $table->{'Tables_in_' . config('database.connections.mysql.database')};

            if (in_array($tableName, $skipTable)) {
                continue;
            }

            // Perform schema modifications
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'location_id')) {
                    $table->unsignedBigInteger('location_id')->nullable();
                    $table->foreign('location_id')->references('location_id')->on('location')->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = DB::select('SHOW TABLES');

        foreach ($tables as $table) {
            $tableName = $table->{'Tables_in_' . config('database.connections.mysql.database')};

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'location_id')) {
                    $table->dropForeign(['location_id_foreign']); // Fixed foreign key name
                    $table->dropColumn('location_id');
                }
            });
        }
    }
};
