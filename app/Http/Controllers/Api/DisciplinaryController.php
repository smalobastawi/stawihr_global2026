<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Lib\Enumerations\DisciplinaryActionTypes;
use App\Lib\Enumerations\DisciplinaryCaseStatus;
use App\Models\DisciplinaryCase;
use App\Models\DisciplinaryCaseAction;
use App\Models\Employee;
use Illuminate\Http\Request;

class DisciplinaryController extends Controller
{
    /**
     * List cases where the employee is the subject or assigned officer.
     */
    public function index(Request $request)
    {
        $employee = $this->resolveEmployee($request);
        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee profile not found.',
            ], 404);
        }

        $cases = DisciplinaryCase::query()
            ->where(function ($query) use ($employee) {
                $query->where('employee_id', $employee->employee_id)
                    ->orWhere('assigned_officer', $employee->employee_id);
            })
            ->with(['category', 'assignedOfficer', 'employee'])
            ->orderByDesc('id')
            ->get()
            ->map(fn (DisciplinaryCase $case) => $this->formatCaseSummary($case, $employee));

        return response()->json([
            'status' => 'success',
            'data' => $cases,
        ]);
    }

    /**
     * Show case details for the subject or assigned officer only.
     */
    public function show(Request $request, $id)
    {
        $employee = $this->resolveEmployee($request);
        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee profile not found.',
            ], 404);
        }

        $case = DisciplinaryCase::with([
            'category',
            'employee',
            'assignedOfficer',
            'officeLocation',
            'actions',
        ])->find($id);

        if (!$case) {
            return response()->json([
                'status' => 'error',
                'message' => 'Disciplinary case not found.',
            ], 404);
        }

        if (!$this->canViewCase($case, $employee)) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to view this case.',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $this->formatCaseDetail($case, $employee),
        ]);
    }

    protected function resolveEmployee(Request $request): ?Employee
    {
        $user = $request->user();
        if (!$user) {
            return null;
        }

        return Employee::where('user_id', $user->id)->first();
    }

    protected function canViewCase(DisciplinaryCase $case, Employee $viewer): bool
    {
        return (int) $case->employee_id === (int) $viewer->employee_id
            || (int) $case->assigned_officer === (int) $viewer->employee_id;
    }

    protected function isSubject(DisciplinaryCase $case, Employee $viewer): bool
    {
        return (int) $case->employee_id === (int) $viewer->employee_id;
    }

    protected function isAssignedOfficer(DisciplinaryCase $case, Employee $viewer): bool
    {
        return (int) $case->assigned_officer === (int) $viewer->employee_id;
    }

    protected function resolveViewerRole(DisciplinaryCase $case, Employee $viewer): string
    {
        $isSubject = $this->isSubject($case, $viewer);
        $isOfficer = $this->isAssignedOfficer($case, $viewer);

        if ($isSubject && $isOfficer) {
            return 'subject_and_officer';
        }

        if ($isSubject) {
            return 'subject';
        }

        return 'assigned_officer';
    }

    protected function resolveViewerRoleLabel(string $role): string
    {
        return match ($role) {
            'subject' => 'You are the subject of this case',
            'assigned_officer' => 'You are the assigned disciplinary officer',
            'subject_and_officer' => 'You are the subject and assigned officer',
            default => '',
        };
    }

    protected function formatEmployeeName(?Employee $employee): ?string
    {
        if (!$employee) {
            return null;
        }

        $name = trim($employee->first_name . ' ' . $employee->last_name);

        return $name !== '' ? $name : ($employee->full_name ?? null);
    }

    protected function formatCaseSummary(DisciplinaryCase $case, Employee $viewer): array
    {
        $viewerRole = $this->resolveViewerRole($case, $viewer);

        return [
            'id' => $case->id,
            'case_number' => $case->case_number,
            'description' => $case->description,
            'status' => $case->status,
            'status_name' => DisciplinaryCaseStatus::getName($case->status),
            'category' => $case->category ? [
                'id' => $case->category->id,
                'name' => $case->category->name,
            ] : null,
            'assigned_officer' => $case->assignedOfficer ? [
                'id' => $case->assignedOfficer->employee_id,
                'name' => $this->formatEmployeeName($case->assignedOfficer),
            ] : null,
            'subject' => $case->employee ? [
                'id' => $case->employee->employee_id,
                'name' => $this->formatEmployeeName($case->employee),
            ] : null,
            'date_of_incident' => $case->date_of_incident,
            'date_of_report' => $case->date_of_report,
            'viewer_role' => $viewerRole,
            'viewer_role_label' => $this->resolveViewerRoleLabel($viewerRole),
            'is_subject' => $this->isSubject($case, $viewer),
            'is_assigned_officer' => $this->isAssignedOfficer($case, $viewer),
        ];
    }

    protected function formatCaseDetail(DisciplinaryCase $case, Employee $viewer): array
    {
        $summary = $this->formatCaseSummary($case, $viewer);
        $isSubject = $this->isSubject($case, $viewer);
        $isOfficer = $this->isAssignedOfficer($case, $viewer);

        $detail = array_merge($summary, [
            'location' => $case->location,
            'office_location' => $case->officeLocation ? [
                'id' => $case->officeLocation->location_id ?? $case->officeLocation->id ?? null,
                'name' => $case->officeLocation->location_name ?? null,
            ] : null,
            'attachment_url' => $case->attachment
                ? asset('storage/' . $case->attachment)
                : null,
            'actions' => $case->actions
                ->map(fn (DisciplinaryCaseAction $action) => [
                    'id' => $action->id,
                    'action_type' => $action->action_type,
                    'action_type_name' => DisciplinaryActionTypes::getName($action->action_type),
                    'remarks' => $action->remarks,
                    'action_date' => $action->action_date,
                    'status' => $action->status,
                    'status_name' => DisciplinaryCaseStatus::getName($action->status),
                    'attachment_url' => $action->attachment
                        ? asset('storage/' . $action->attachment)
                        : null,
                ])
                ->values()
                ->all(),
        ]);

        if ($isOfficer && $case->employee) {
            $detail['employee'] = [
                'id' => $case->employee->employee_id,
                'name' => $this->formatEmployeeName($case->employee),
            ];
        }

        if ($isSubject && $case->assignedOfficer) {
            $detail['assigned_officer'] = [
                'id' => $case->assignedOfficer->employee_id,
                'name' => $this->formatEmployeeName($case->assignedOfficer),
            ];
        }

        return $detail;
    }
}
