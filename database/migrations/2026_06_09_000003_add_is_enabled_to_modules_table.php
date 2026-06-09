<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $systemModules = [
        ['name' => 'Administration', 'icon_class' => 'mdi mdi-contacts'],
        ['name' => 'Self Service', 'icon_class' => 'mdi mdi-contacts'],
        ['name' => 'Employee Management', 'icon_class' => 'mdi mdi-account-multiple-plus'],
        ['name' => 'Vehicle Management', 'icon_class' => 'mdi mdi-car'],
        ['name' => 'Leave Management', 'icon_class' => 'mdi mdi-format-line-weight'],
        ['name' => 'Attendance', 'icon_class' => 'mdi mdi-clock-fast'],
        ['name' => 'Payroll', 'icon_class' => 'mdi mdi-cash'],
        ['name' => 'Disciplinary', 'icon_class' => 'mdi mdi-gavel'],
        ['name' => 'Performance Management', 'icon_class' => 'mdi mdi-calculator'],
        ['name' => 'PIP Management', 'icon_class' => 'mdi mdi-chart-timeline-variant'],
        ['name' => 'Recruitment', 'icon_class' => 'mdi mdi-newspaper'],
        ['name' => 'Training', 'icon_class' => 'mdi mdi-web'],
        ['name' => 'Award', 'icon_class' => 'mdi mdi-trophy-variant'],
        ['name' => 'Notice Board', 'icon_class' => 'mdi mdi-flag'],
        ['name' => 'Annalytics', 'icon_class' => 'mdi mdi-chart-line'],
        ['name' => 'Employee Feedback', 'icon_class' => 'mdi mdi-message-text'],
        ['name' => 'Settings', 'icon_class' => 'mdi mdi-settings'],
        ['name' => 'Survey', 'icon_class' => 'mdi mdi-clipboard-text'],
        ['name' => 'Hr Uploads', 'icon_class' => 'mdi mdi-file-document'],
        ['name' => 'approvals', 'icon_class' => 'mdi mdi-check-decagram'],
        ['name' => 'project', 'icon_class' => 'mdi mdi-briefcase'],
    ];

    public function up(): void
    {
        if (!Schema::hasColumn('modules', 'is_enabled')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->boolean('is_enabled')->default(true)->after('icon_class');
            });
        }

        foreach ($this->systemModules as $module) {
            $existing = DB::table('modules')->where('name', $module['name'])->first();

            if ($existing) {
                DB::table('modules')
                    ->where('id', $existing->id)
                    ->update([
                        'icon_class' => $module['icon_class'],
                        'is_enabled' => $existing->is_enabled ?? true,
                    ]);
            } else {
                DB::table('modules')->insert([
                    'name' => $module['name'],
                    'icon_class' => $module['icon_class'],
                    'is_enabled' => true,
                ]);
            }
        }

        DB::table('modules')
            ->whereIn('name', ['Administration', 'Settings'])
            ->update(['is_enabled' => true]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('modules', 'is_enabled')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->dropColumn('is_enabled');
            });
        }
    }
};
