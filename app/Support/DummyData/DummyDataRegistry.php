<?php

namespace App\Support\DummyData;

use App\Models\DummyDataBatch;
use App\Models\DummyDataRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DummyDataRegistry
{
    public const DELETION_ORDER = [
        'payroll_record_details',
        'payroll_records',
        'training_invitees',
        'training_attendants',
        'attendances',
        'leave_application',
        'employee_feedback_responses',
        'employee_feedback',
        'disciplinary_case_actions',
        'disciplinary_cases',
        'staff_contracts',
        'employee_leavegroups',
        'employee_payrolls',
        'employee',
        'user',
        'trainings',
        'training_facilitators',
        'training_type',
        'disciplinary_categories',
        'feedback_categories',
    ];

    private ?DummyDataBatch $batch = null;

    public function startBatch(?int $userId): DummyDataBatch
    {
        $this->batch = DummyDataBatch::create([
            'user_id' => $userId,
            'summary' => [],
        ]);

        return $this->batch;
    }

    public function track(string $table, int $recordId): void
    {
        if (!$this->batch) {
            throw new \RuntimeException('Dummy data batch has not been started.');
        }

        DummyDataRecord::firstOrCreate([
            'batch_id' => $this->batch->id,
            'table_name' => $table,
            'record_id' => $recordId,
        ]);
    }

    public function finishBatch(array $summary): DummyDataBatch
    {
        if (!$this->batch) {
            throw new \RuntimeException('Dummy data batch has not been started.');
        }

        $this->batch->update(['summary' => $summary]);

        return $this->batch->fresh();
    }

    public function activeBatch(): ?DummyDataBatch
    {
        return DummyDataBatch::active();
    }

    public function counts(?DummyDataBatch $batch = null): array
    {
        $batch = $batch ?? $this->activeBatch();

        if (!$batch) {
            return [];
        }

        return DummyDataRecord::query()
            ->where('batch_id', $batch->id)
            ->select('table_name', DB::raw('COUNT(*) as total'))
            ->groupBy('table_name')
            ->orderBy('table_name')
            ->pluck('total', 'table_name')
            ->toArray();
    }

    public function removeAll(): void
    {
        $batch = $this->activeBatch();

        if (!$batch) {
            return;
        }

        $recordsByTable = DummyDataRecord::query()
            ->where('batch_id', $batch->id)
            ->get()
            ->groupBy('table_name');

        $userIds = collect($recordsByTable->get('user', collect()))
            ->pluck('record_id')
            ->all();

        if (!empty($userIds)) {
            DB::table('model_has_roles')
                ->where('model_type', User::class)
                ->whereIn('model_id', $userIds)
                ->delete();
        }

        foreach (self::DELETION_ORDER as $table) {
            if (!$recordsByTable->has($table)) {
                continue;
            }

            if (!Schema::hasTable($table)) {
                continue;
            }

            $ids = $recordsByTable->get($table)->pluck('record_id')->unique()->values()->all();

            if (empty($ids)) {
                continue;
            }

            $this->deleteRecords($table, $ids);
        }

        $batch->delete();
    }

    private function deleteRecords(string $table, array $ids): void
    {
        $query = DB::table($table)->whereIn($this->primaryKeyColumn($table), $ids);

        if ($this->usesSoftDeletes($table)) {
            $query->update(['deleted_at' => now()]);
            DB::table($table)->whereIn($this->primaryKeyColumn($table), $ids)->delete();
            return;
        }

        $query->delete();
    }

    private function primaryKeyColumn(string $table): string
    {
        return match ($table) {
            'employee' => 'employee_id',
            'leave_application' => 'leave_application_id',
            'training_type' => 'training_type_id',
            default => 'id',
        };
    }

    private function usesSoftDeletes(string $table): bool
    {
        if (!Schema::hasColumn($table, 'deleted_at')) {
            return false;
        }

        return in_array($table, [
            'employee',
            'user',
            'employee_payrolls',
            'payroll_records',
            'staff_contracts',
            'disciplinary_cases',
            'employee_feedback',
            'leave_application',
            'attendances',
            'trainings',
        ], true);
    }
}
