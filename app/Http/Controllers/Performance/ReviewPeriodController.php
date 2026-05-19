<?php

namespace App\Http\Controllers\Performance;

use App\Http\Controllers\Controller;
use App\Models\Performance\ReviewPeriod;
use App\Models\User;
use Illuminate\Http\Request;

class ReviewPeriodController extends Controller
{
    public function index()
    {
        $results = ReviewPeriod::orderBy('sort_order', 'asc')->orderBy('start_date', 'desc')->get();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.performance.review_period.index', [
            'results' => $results,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function create()
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        return view('admin.performance.review_period.form', [
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'period_name' => 'required|string|max:100|unique:review_periods,period_name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $validated['is_active'] = $request->has('is_active');

        try {
            ReviewPeriod::create($validated);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('performance.reviewPeriod.index')->with('success', 'Review period created successfully.');
        } else {
            return redirect()->route('performance.reviewPeriod.index')->with('error', 'Error creating review period: ' . $bug);
        }
    }

    public function edit($id)
    {
        $reviewPeriod = ReviewPeriod::findOrFail($id);
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.performance.review_period.form', [
            'editModeData' => $reviewPeriod,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function update(Request $request, $id)
    {
        $reviewPeriod = ReviewPeriod::findOrFail($id);

        $validated = $request->validate([
            'period_name' => 'required|string|max:100|unique:review_periods,period_name,' . $id . ',period_id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $validated['is_active'] = $request->has('is_active');

        try {
            $reviewPeriod->update($validated);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('performance.reviewPeriod.index')->with('success', 'Review period updated successfully.');
        } else {
            return redirect()->route('performance.reviewPeriod.index')->with('error', 'Error updating review period: ' . $bug);
        }
    }

    public function destroy($id)
    {
        try {
            $reviewPeriod = ReviewPeriod::findOrFail($id);
            $reviewPeriod->delete();
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('performance.reviewPeriod.index')->with('success', 'Review period deleted successfully.');
        } else {
            return redirect()->route('performance.reviewPeriod.index')->with('error', 'Error deleting review period: ' . $bug);
        }
    }
}
