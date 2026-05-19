<?php

namespace App\Http\Controllers\Disciplinary;

use App\Models\DisciplinaryCaseAction;
use App\Http\Requests\StoreDiscplinaryCaseActionRequest;
use App\Http\Requests\UpdateDiscplinaryCaseActionRequest;
use App\Http\Controllers\Controller;

class DiscplinaryCaseActionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DisciplinaryCaseAction::with(['case', 'actionedBy'])->get();
        return view('admin.disciplinary.case-actions.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreDiscplinaryCaseActionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDiscplinaryCaseActionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DisciplinaryCaseAction  $discplinaryCaseAction
     * @return \Illuminate\Http\Response
     */
    public function show(DisciplinaryCaseAction $discplinaryCaseAction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DisciplinaryCaseAction  $discplinaryCaseAction
     * @return \Illuminate\Http\Response
     */
    public function edit(DisciplinaryCaseAction $discplinaryCaseAction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDiscplinaryCaseActionRequest  $request
     * @param  \App\Models\DisciplinaryCaseAction  $discplinaryCaseAction
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDiscplinaryCaseActionRequest $request, DisciplinaryCaseAction $discplinaryCaseAction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DisciplinaryCaseAction  $discplinaryCaseAction
     * @return \Illuminate\Http\Response
     */
    public function destroy(DisciplinaryCaseAction $discplinaryCaseAction)
    {
        //
    }
}
