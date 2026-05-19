<?php

namespace App\Http\Controllers;

use App\Models\Ethnicity;
use Illuminate\Http\Request;

class EthnicityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ethnicities = Ethnicity::withCount('employees')->get();
        return view('admin.employee.ethnicities.index', compact('ethnicities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.employee.ethnicities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ethnicities',
        ]);

        Ethnicity::create($request->only('name'));

        return redirect()->route('ethnicities.index')->with('success', 'Ethnicity created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ethnicity $ethnicity)
    {
        return view('admin.employee.ethnicities.show', compact('ethnicity'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ethnicity $ethnicity)
    {
        return view('admin.employee.ethnicities.edit', compact('ethnicity'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ethnicity $ethnicity)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ethnicities,name,' . $ethnicity->id,
        ]);

        $ethnicity->update($request->only('name'));

        return redirect()->route('ethnicities.index')->with('success', 'Ethnicity updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ethnicity $ethnicity)
    {
        $ethnicity->delete();

        return redirect()->route('ethnicities.index')->with('success', 'Ethnicity deleted successfully.');
    }
}
