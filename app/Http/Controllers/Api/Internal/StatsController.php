<?php

namespace App\Http\Controllers\Api\Internal;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StatsController extends Controller
{
    public function getStats(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'stats' => 'nullable|array',
            'stats.*' => 'in:companies,users,employees,departments',
            'domain' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $requestedStats = $request->input('stats', ['companies', 'users', 'employees']);
        $stats = [];

        if (in_array('companies', $requestedStats, true)) {
            $companies = Company::query()
                ->orderBy('name')
                ->get(['id', 'name', 'domain', 'status']);

            $stats['companies'] = [
                'count' => $companies->count(),
                'list' => $companies->map(fn (Company $company) => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'domain' => $company->domain,
                    'status' => $company->status,
                ])->values(),
            ];
        }

        if (in_array('users', $requestedStats, true)) {
            $stats['users'] = [
                'count' => User::query()->count(),
                'active' => User::query()->where('status', 1)->count(),
            ];
        }

        if (in_array('employees', $requestedStats, true)) {
            $employeeQuery = Employee::withoutGlobalScopes();
            $stats['employees'] = [
                'count' => (clone $employeeQuery)->count(),
                'active' => (clone $employeeQuery)->where('status', 1)->count(),
            ];
        }

        if (in_array('departments', $requestedStats, true)) {
            $stats['departments'] = [
                'count' => Department::withoutGlobalScopes()->count(),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'domain' => $request->input('domain'),
                'timestamp' => now()->toIso8601String(),
                'stats' => $stats,
            ],
        ]);
    }

    public function getSummary(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'counts' => [
                    'companies' => Company::query()->count(),
                    'users' => User::query()->count(),
                    'employees' => Employee::withoutGlobalScopes()->count(),
                    'departments' => Department::withoutGlobalScopes()->count(),
                ],
                'timestamp' => now()->toIso8601String(),
            ],
        ]);
    }
}
