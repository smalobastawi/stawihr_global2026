<?php

namespace App\Http\Controllers\Pdp;

use App\Http\Controllers\Controller;
use App\Models\Pdp\PdpSetting;
use App\Models\User;
use Illuminate\Http\Request;

class PdpSettingController extends Controller
{
    public function index()
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $setting = PdpSetting::current();

        return view('admin.pdp.setting.index', [
            'setting' => $setting,
            'signed_in_user_role' => $signed_in_user_role,
        ]);
    }

    public function update(Request $request)
    {
        $input = $request->validate([
            'default_review_frequency' => 'required|in:quarterly,bi_annually,annually',
            'allow_employee_self_service' => 'nullable|boolean',
            'require_supervisor_approval' => 'nullable|boolean',
            'require_hr_review' => 'nullable|boolean',
            'policy_notes' => 'nullable|string',
        ]);

        $input['allow_employee_self_service'] = $request->boolean('allow_employee_self_service');
        $input['require_supervisor_approval'] = $request->boolean('require_supervisor_approval');
        $input['require_hr_review'] = $request->boolean('require_hr_review');

        $setting = PdpSetting::current();
        $setting->update($input);

        return redirect()->route('pdp.setting.index')->with('success', 'PDP policy settings updated successfully.');
    }
}
