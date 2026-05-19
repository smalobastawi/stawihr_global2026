<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProjectEmployeePayrollAllocation;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('parent', 'creator')->get();
        return view('admin.employee.project.index', compact('projects'));
    }

    public function create()
    {
        $mainProjects = Project::whereNull('main_project')->get();
        return view('admin.employee.project.form', compact('mainProjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:projects',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'main_project' => 'nullable|exists:projects,id',
            'status' => 'required|in:active,inactive,completed',
        ]);

        Project::create(array_merge($request->all(), ['created_by' => Auth::id()]));

        return redirect()->route('employee.project.index')->with('success', 'Project created successfully!');
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);
        $mainProjects = Project::whereNull('main_project')->where('id', '!=', $id)->get();
        return view('admin.employee.project.form', compact('project', 'mainProjects'));
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:projects,code,' . $id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'main_project' => 'nullable|exists:projects,id',
            'status' => 'required|in:active,inactive,completed',
        ]);

        $project->update($request->all());

        return redirect()->route('employee.project.index')->with('success', 'Project updated successfully!');
    }

    public function destroy($id)
    {
        $hasAllocations = ProjectEmployeePayrollAllocation::where('project_id', $id)->exists();

        if ($hasAllocations) {
            return response()->json(['status' => 'error', 'message' => 'Project Deletion Failed: This project is currently allocated to one or more employees. To delete, you must first unallocate all employees from this project.']);
        }

        Project::findOrFail($id)->delete();
        return response()->json(['status' => 'success', 'message' => 'Project deleted successfully!']);
    }
}
