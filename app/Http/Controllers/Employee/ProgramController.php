<?php

namespace App\Http\Controllers\Employee;

use App\Http\Requests\StoreProgramRequest;
use App\Http\Requests\UpdateProgramRequest;
use App\Models\Program;
use App\Http\Controllers\Controller;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $programs = Program::all();
        return view('admin.employee.program.index')->with(['programs' => $programs]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $programs = Program::all(); // For main_program dropdown
        return view('admin.employee.program.form')->with(['programList' => $programs]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProgramRequest $request)
    {
        // Create a new program using validated data
        Program::create([
            'name' => $request->name,
            'code' => $request->code,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'main_program' => $request->main_program,
            'status' => $request->status,
            'created_by'=>\Auth::id()
        ]);

        return redirect()->route('employee.program.index')->with('success', 'Program created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program)
    {
        return view('admin.employee.program.show')->with(['program' => $program]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program $program)
    {
        $programs = Program::where('id', '!=', $program->id)->get(); // Exclude current program from dropdown
        return view('admin.employee.program.form')->with([
            'editModeData' => $program,
            'programList' => $programs,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProgramRequest $request, Program $program)
    {
        // Update program with validated data
        $program->update([
            'name' => $request->name,
            'code' => $request->code,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'main_program' => $request->main_program,
            'status' => $request->status,
            'updated_by'=>\Auth::id()
        ]);

        return redirect()->route('employee.program.index')->with('success', 'Program updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program)
    {
        $program->delete();
        echo 'success';
    }
}
