<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    /**
     * Get all departments
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $departments = Department::where('status', 1)
                ->whereNull('deleted_at')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $departments
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching departments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function profile()
    {
        try {
            $user = auth()->user();
            $department = Department::where('location_id', $user->location_id)
                ->whereNull('deleted_at')
                ->first();

            if (!$department) {
                return response()->json([
                    'success' => false,
                    'message' => 'Department not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $department
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching department',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new department
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'department_name' => 'required|string|max:150|unique:departments',
                'location_id' => 'required|integer|exists:locations,id',
                'status' => 'required|integer|in:0,1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $department = Department::create([
                'department_name' => $request->department_name,
                'location_id' => $request->location_id,
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Department created successfully',
                'data' => $department
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating department',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific department
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $department = Department::where('department_id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$department) {
                return response()->json([
                    'success' => false,
                    'message' => 'Department not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $department
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching department',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update department
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $department = Department::where('department_id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$department) {
                return response()->json([
                    'success' => false,
                    'message' => 'Department not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'department_name' => 'required|string|max:150|unique:departments,department_name,' . $id . ',department_id',
                'location_id' => 'required|integer|exists:locations,id',
                'status' => 'required|integer|in:0,1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $department->update([
                'department_name' => $request->department_name,
                'location_id' => $request->location_id,
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Department updated successfully',
                'data' => $department
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating department',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Soft delete department
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $department = Department::where('department_id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$department) {
                return response()->json([
                    'success' => false,
                    'message' => 'Department not found'
                ], 404);
            }

            $department->deleted_at = now();
            $department->save();

            return response()->json([
                'success' => true,
                'message' => 'Department deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting department',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
