<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Employee;

use App\Http\Requests\LocationRequest;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;

use App\Models\Location;
use App\Models\Employee;
use App\Models\Region;

class LocationController extends Controller
{

    public function index()
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        $results = Location::withCount('employees')->get();

        return view('admin.employee.location.index', ['results' => $results, 'signed_in_user_role' => $signed_in_user_role]);
    }

    public function create()
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $regions = Region::pluck('name', 'id');
        return view('admin.employee.location.form', [
            'signed_in_user_role' => $signed_in_user_role,
            'regions' => $regions
        ]);
    }

    public function store(LocationRequest $request)
    {
        $input = $request->all();
        try {
            Location::create($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('location.index')->with('success', 'Location successfully saved.');
        } else {
            return redirect()->route('location.index')->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }



    public function edit($id)
    {
        $regions = Region::pluck('name', 'id');
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        $editModeData = Location::findOrFail($id);
        return view('admin.employee.location.form', [
            'editModeData' => $editModeData,
            'signed_in_user_role' => $signed_in_user_role,
            'regions' => $regions
        ]);
    }



    public function update(LocationRequest $request, $id)
    {
        $location = Location::findOrFail($id);
        $input = $request->all();
        try {
            $location->update($input);
            $location->employees()->update(['region_id' => $location->region_id]);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('location.index')->with('success', 'Location successfully updated ');
        } else {
            return redirect()->route('location.index')->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }



    public function destroy($id)
    {

        $count = Employee::where('location_id', '=', $id)->count();

        if ($count > 0) {

            return  'hasForeignKey';
        }

        try {
            $location = Location::findOrFail($id);
            $location->delete();
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            echo "success";
        } elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }
}
