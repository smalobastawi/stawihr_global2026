<?php

namespace App\Http\Controllers\Performance;

use App\Http\Controllers\Controller;
use App\Models\Performance\PerformanceRatingScale;
use App\Models\User;
use Illuminate\Http\Request;

class RatingScaleController extends Controller
{
    public function index()
    {
        $results = PerformanceRatingScale::orderBy('points', 'desc')->get();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.performance.ratingScale.index', [
            'results' => $results,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function create()
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        return view('admin.performance.ratingScale.form', [
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function store(Request $request)
    {
        $input = $request->validate([
            'points' => 'required|integer|min:1|max:5',
            'rating_label' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'definition' => 'required|string',
            'score_range' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $input['is_active'] = $request->has('is_active') ? 1 : 0;

        try {
            PerformanceRatingScale::create($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('performance.ratingScale.index')->with('success', 'Rating scale saved successfully.');
        } else {
            return redirect()->route('performance.ratingScale.index')->with('error', 'An error occurred: ' . $bug);
        }
    }

    public function edit($id)
    {
        $editModeData = PerformanceRatingScale::findOrFail($id);
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.performance.ratingScale.form', [
            'editModeData' => $editModeData,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function update(Request $request, $id)
    {
        $scale = PerformanceRatingScale::findOrFail($id);

        $input = $request->validate([
            'points' => 'required|integer|min:1|max:5',
            'rating_label' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'definition' => 'required|string',
            'score_range' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $input['is_active'] = $request->has('is_active') ? 1 : 0;

        try {
            $scale->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('performance.ratingScale.index')->with('success', 'Rating scale updated successfully.');
        } else {
            return redirect()->route('performance.ratingScale.index')->with('error', 'An error occurred: ' . $bug);
        }
    }

    public function destroy($id)
    {
        try {
            $scale = PerformanceRatingScale::findOrFail($id);
            $scale->delete();
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
