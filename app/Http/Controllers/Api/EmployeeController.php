<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    // Fetch all employees
    public function index()
    {
        $employees = Employee::all();
        return response()->json($employees, 200);
    }

    // Fetch a single employee by ID (original method)
    public function show($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        return response()->json($employee, 200);
    }

    // Fetch logged-in employee profile (new method)
    public function profile()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $employee = Employee::where('user_id', $user->id)
            ->with(['department', 'branch', 'designation', 'workShift'])
            ->first();

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        return response()->json($employee, 200);
    }

    // Create a new employee
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'national_id' => 'required|string|max:255',
            'staff_no' => 'nullable|string|max:255',
            'department_id' => 'required|integer',
            'designation_id' => 'required|integer',
            'first_name' => 'required|string|max:30',
            'last_name' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:50',
            'phone' => 'nullable|integer',
        ]);

        $employee = Employee::create($validated);
        return response()->json(['message' => 'Employee created successfully', 'data' => $employee], 201);
    }

    // Update an employee
    public function update(Request $request, $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $validated = $request->validate([
            'user_id' => 'integer',
            'national_id' => 'string|max:255',
            'staff_no' => 'nullable|string|max:255',
            'department_id' => 'integer',
            'designation_id' => 'integer',
            'first_name' => 'string|max:30',
            'last_name' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:50',
            'phone' => 'nullable|integer',
        ]);

        $employee->update($validated);
        return response()->json(['message' => 'Employee updated successfully', 'data' => $employee], 200);
    }

    // Delete an employee
    public function destroy($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $employee->delete();
        return response()->json(['message' => 'Employee deleted successfully'], 200);
    }
}
