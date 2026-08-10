<?php

namespace App\Services\Annalytics;

use App\Models\Department;
use App\Models\Employee;
use App\Models\PrintHeadSetting;
use Illuminate\Support\Collection;

class OrganizationChartService
{
    /**
     * Build organization chart data from active employee supervisor relationships.
     *
     * @return array{trees: array<int, array>, unassigned: array<int, array>, summary: array<int, array{label: string, value: string|int}>, departments: Collection, filters: array}
     */
    public function build(?int $departmentId = null): array
    {
        $employees = Employee::query()
            ->active()
            ->with(['department', 'designation'])
            ->when($departmentId, fn ($query) => $query->where('department_id', $departmentId))
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get([
                'employee_id',
                'first_name',
                'middle_name',
                'last_name',
                'staff_no',
                'photo',
                'supervisor_id',
                'department_id',
                'designation_id',
            ]);

        $nodesById = [];
        foreach ($employees as $employee) {
            $nodesById[$employee->employee_id] = $this->mapEmployee($employee);
        }

        $childrenMap = [];
        foreach ($nodesById as $id => $node) {
            $supervisorId = $node['supervisor_id'];
            if ($supervisorId && isset($nodesById[$supervisorId]) && $supervisorId !== $id) {
                $childrenMap[$supervisorId][] = $id;
            }
        }

        $visited = [];
        $trees = [];

        foreach ($nodesById as $id => $node) {
            $supervisorId = $node['supervisor_id'];
            $isRoot = !$supervisorId
                || !isset($nodesById[$supervisorId])
                || $supervisorId === $id;

            if ($isRoot) {
                $trees[] = $this->buildNodeTree($id, $nodesById, $childrenMap, $visited);
            }
        }

        // Catch any nodes missed due to cycles
        $unassigned = [];
        foreach ($nodesById as $id => $node) {
            if (!isset($visited[$id])) {
                $unassigned[] = $node;
            }
        }

        $supervisorsWithReports = collect($childrenMap)->filter(fn ($children) => count($children) > 0)->count();

        return [
            'trees' => $trees,
            'unassigned' => $unassigned,
            'summary' => [
                ['label' => 'Active Employees', 'value' => count($nodesById)],
                ['label' => 'Reporting Roots', 'value' => count($trees)],
                ['label' => 'Managers with Reports', 'value' => $supervisorsWithReports],
                ['label' => 'Unlinked / Cyclic', 'value' => count($unassigned)],
            ],
            'departments' => Department::orderBy('department_name')->get(['department_id', 'department_name']),
            'filters' => [
                'department_id' => $departmentId,
            ],
            'printHead' => PrintHeadSetting::first(),
            'generated_at' => now(),
        ];
    }

    private function mapEmployee(Employee $employee): array
    {
        return [
            'employee_id' => $employee->employee_id,
            'name' => $employee->full_name,
            'staff_no' => $employee->staff_no,
            'photo' => $employee->photo,
            'supervisor_id' => $employee->supervisor_id,
            'department' => $employee->department->department_name ?? 'Unassigned',
            'designation' => $employee->designation->designation_name ?? 'N/A',
            'children' => [],
        ];
    }

    private function buildNodeTree(int $id, array $nodesById, array $childrenMap, array &$visited): array
    {
        if (isset($visited[$id])) {
            return $nodesById[$id];
        }

        $visited[$id] = true;
        $node = $nodesById[$id];
        $children = [];

        foreach ($childrenMap[$id] ?? [] as $childId) {
            if (!isset($visited[$childId])) {
                $children[] = $this->buildNodeTree($childId, $nodesById, $childrenMap, $visited);
            }
        }

        usort($children, fn ($a, $b) => strcasecmp($a['name'], $b['name']));
        $node['children'] = $children;
        $node['direct_reports'] = count($children);

        return $node;
    }
}
