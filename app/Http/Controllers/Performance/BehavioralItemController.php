<?php

namespace App\Http\Controllers\Performance;

use App\Http\Controllers\Controller;
use App\Models\Performance\PerformanceBehavioralItem;
use App\Models\User;
use Illuminate\Http\Request;

class BehavioralItemController extends Controller
{
    public function index()
    {
        $results = PerformanceBehavioralItem::orderBy('sort_order')->get();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.performance.behavioralItem.index', [
            'results' => $results,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function create()
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        return view('admin.performance.behavioralItem.form', [
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function store(Request $request)
    {
        $input = $request->validate([
            'item_name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0|max:1',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $input['is_active'] = $request->has('is_active') ? 1 : 0;

        try {
            PerformanceBehavioralItem::create($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('performance.behavioralItem.index')->with('success', 'Behavioral item saved successfully.');
        } else {
            return redirect()->route('performance.behavioralItem.index')->with('error', 'An error occurred: ' . $bug);
        }
    }

    public function edit($id)
    {
        $editModeData = PerformanceBehavioralItem::findOrFail($id);
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.performance.behavioralItem.form', [
            'editModeData' => $editModeData,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = PerformanceBehavioralItem::findOrFail($id);

        $input = $request->validate([
            'item_name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0|max:1',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $input['is_active'] = $request->has('is_active') ? 1 : 0;

        try {
            $item->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('performance.behavioralItem.index')->with('success', 'Behavioral item updated successfully.');
        } else {
            return redirect()->route('performance.behavioralItem.index')->with('error', 'An error occurred: ' . $bug);
        }
    }

    public function destroy($id)
    {
        try {
            $item = PerformanceBehavioralItem::findOrFail($id);
            $item->delete();
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            echo "success";
        } else {
            echo 'error';
        }
    }
}
