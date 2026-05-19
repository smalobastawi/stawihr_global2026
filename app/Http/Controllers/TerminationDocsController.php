<?php

namespace App\Http\Controllers;

use App\Models\TerminationDocs;
use App\Http\Requests\StoreTerminationDocsRequest;
use App\Http\Requests\UpdateTerminationDocsRequest;

class TerminationDocsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreTerminationDocsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTerminationDocsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TerminationDocs  $terminationDocs
     * @return \Illuminate\Http\Response
     */
    public function show(TerminationDocs $terminationDocs)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TerminationDocs  $terminationDocs
     * @return \Illuminate\Http\Response
     */
    public function edit(TerminationDocs $terminationDocs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTerminationDocsRequest  $request
     * @param  \App\Models\TerminationDocs  $terminationDocs
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTerminationDocsRequest $request, TerminationDocs $terminationDocs)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TerminationDocs  $terminationDocs
     * @return \Illuminate\Http\Response
     */
    public function destroy(TerminationDocs $terminationDocs)
    {
        //
    }
}
