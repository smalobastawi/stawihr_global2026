<?php

namespace App\Http\Controllers\Api\Internal;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EmployeeSyncController extends Controller
{
    /**
     * Org-wide employee feed for school MIS outbound pull.
     *
     * GET /api/internal/sync/employees?updated_since=2026-08-01T00:00:00Z
     */
    public function index(Request $request): JsonResponse
    {
        $query = Employee::query()
            ->withoutGlobalScopes()
            ->with([
                'department:department_id,department_name',
                'designation:designation_id,designation_name',
            ])
            ->orderBy('employee_id');

        if ($request->filled('updated_since')) {
            try {
                $since = Carbon::parse((string) $request->query('updated_since'));
                $query->where(function ($builder) use ($since) {
                    $builder->where('updated_at', '>=', $since)
                        ->orWhere('created_at', '>=', $since)
                        ->orWhere(function ($left) use ($since) {
                            $left->whereNotNull('date_of_leaving')
                                ->where('date_of_leaving', '>=', $since->toDateString());
                        });
                });
            } catch (\Throwable) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid updated_since value. Use an ISO-8601 datetime.',
                ], 422);
            }
        }

        $employees = $query->get()->map(fn (Employee $employee) => $this->serialize($employee))->values();

        return response()->json([
            'success' => true,
            'data' => $employees,
            'meta' => [
                'count' => $employees->count(),
                'updated_since' => $request->query('updated_since'),
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function serialize(Employee $employee): array
    {
        return [
            'employee_id' => $employee->employee_id,
            'staff_no' => $employee->staff_no,
            'first_name' => $employee->first_name,
            'middle_name' => $employee->middle_name,
            'last_name' => $employee->last_name,
            'email' => $employee->email,
            'phone' => $employee->phone !== null ? (string) $employee->phone : null,
            'status' => (int) $employee->status,
            'date_of_joining' => optional($employee->date_of_joining)->toDateString()
                ?? (is_string($employee->date_of_joining) ? $employee->date_of_joining : null),
            'date_of_leaving' => optional($employee->date_of_leaving)->toDateString()
                ?? (is_string($employee->date_of_leaving) ? $employee->date_of_leaving : null),
            'department' => $employee->department ? [
                'department_id' => $employee->department->department_id,
                'department_name' => $employee->department->department_name,
            ] : null,
            'designation' => $employee->designation ? [
                'designation_id' => $employee->designation->designation_id,
                'designation_name' => $employee->designation->designation_name,
            ] : null,
            'updated_at' => optional($employee->updated_at)?->toIso8601String(),
        ];
    }
}
