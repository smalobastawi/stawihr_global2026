<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers;


use App\Http\Requests\StorePermissionsRequest;
use App\Http\Requests\UpdatePermissionsRequest;
use App\Repositories\CommonRepository;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roleList = Role::get();
        return view('admin.permissions.rolesIndex', ['roleList' => $roleList]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.permissions.add_role_form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePermissionsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd($request->all());

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $permissions
     * @return \Illuminate\Http\Response
     */
    public function show(Request $permissions)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $permissions
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $permissions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePermissionsRequest  $request
     * @param  \App\Models\Role  $permissions
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePermissionsRequest $request, Role $permissions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $permissions
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $permissions)
    {
        //
    }
}
