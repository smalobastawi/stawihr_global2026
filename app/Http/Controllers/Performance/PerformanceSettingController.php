<?php

namespace App\Http\Controllers\Performance;

use App\Http\Controllers\Controller;
use App\Models\Performance\PerformanceSetting;
use App\Models\User;
use Illuminate\Http\Request;

class PerformanceSettingController extends Controller
{
    public function index()
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $setting = PerformanceSetting::current();

        return view('admin.performance.setting.index', [
            'setting' => $setting,
            'approaches' => PerformanceSetting::approaches(),
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function update(Request $request)
    {
        $input = $request->validate([
            'appraisal_approach' => 'required|in:hr_defined,staff_defined',
            'policy_notes' => 'nullable|string',
        ]);

        $setting = PerformanceSetting::current();
        $setting->update($input);

        return redirect()->route('performance.setting.index')
            ->with('success', 'Performance appraisal approach updated successfully.');
    }
}
