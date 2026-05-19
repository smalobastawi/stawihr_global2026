<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApprovalSettingStoreRequest;
use App\Http\Requests\ApprovalSettingUpdateRequest;
use App\Models\ApprovalSettingApprover;
use App\Models\ApprovalSettings;
use App\Models\Employee;
use App\Models\Module;
use App\Models\User;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApprovalSettingController extends Controller
{

    public function index()
    {
        $approvalSettings = ApprovalSettings::with("module", 'approvers')->get();
        //dd($approvalSettings);
        return view('admin.setting.approvals.indexv', ['approvalSettings' => $approvalSettings]);
    }

    public function create()
    {
        $currentUserID=Auth::user()->id;
        $modules = Module::all();
        $employees = Employee::whereNot('user_id',$currentUserID)->get();

        // dd($departments);
        return view('admin.setting.approvals.createv', ['modules' => $modules, 'employees' => $employees]);

    }

    public function store(ApprovalSettingStoreRequest $request)
    {

        //check if the  approval 
        $approvalSettings = new ApprovalSettings();
        $approvalSettings->approver_numbers = $request->input('number_of_approvers');
        $approvalSettings->module_id = $request->input('module_id');
        $approvers = $request->input('approvers');
        $approvers = array_map('intval', $approvers);
        $approvalSettings->approvers_list = json_encode($approvers);

        $approvalSettings->save();
        $appovalUsers = User::whereIn('id', $approvers)->get();
        foreach ($appovalUsers as $user) {
            $approvalSettingsApprover = new ApprovalSettingApprover();
            $approvalSettingsApprover->user_id = $user->id;
            $approvalSettingsApprover->module_id = $request->input('module_id');
            $approvalSettingsApprover->approval_setting_id = $approvalSettings->id;
            $approvalSettingsApprover->save();
        }

        return redirect()->route('approvalSettings.index')->with('success', 'Approval Setting Saved Successfully');
    }

    public function edit(ApprovalSettings $approval_setting)
    {
        $modules = Module::select('id', 'name')->get();
        $employees = Employee::all();
        $currentApproverIds = $approval_setting->approvers->pluck('user_id')->toArray();

        return view('admin.setting.approvals.editv', [
            'approvalSetting' => $approval_setting,
            'modules' => $modules,
            'employees' => $employees,
            'currentApproverIds' => $currentApproverIds,
        ]);
    }

    public function update(ApprovalSettingUpdateRequest $request, ApprovalSettings $approval_setting)
    {

        $approval_setting->approver_numbers = $request->input('number_of_approvers');
        $approval_setting->module_id = $request->input('module_id');
        $approvers = $request->input('approvers');
        $approvers = array_map('intval', $approvers);
        $approval_setting->approvers_list = json_encode($approvers);

        $approval_setting->save();
        $appovalUsers = User::whereIn('id', $approvers)->get();
        $approval_setting->approvers()->delete();
        foreach ($appovalUsers as $user) {
            $approvalSettingsApprover = new ApprovalSettingApprover();
            $approvalSettingsApprover->user_id = $user->id;
            $approvalSettingsApprover->module_id = $request->input('module_id');
            $approvalSettingsApprover->approval_setting_id = $approval_setting->id;
            $approvalSettingsApprover->save();
        }

        return redirect()->route('approvalSettings.index')->with('success', 'Approval Setting Saved Successfully');

    }
    public function index1()
    {

        $results = ApprovalSettings::orderBy('id', 'DESC')->get();
        $departments = Module::pluck('name', 'id')->toArray();
        foreach ($results as $result) {
            $approversIds = json_decode($result->approvers_list, true);

            if (is_array($approversIds)) {
                $employees = Employee::whereIn('employee_id', $approversIds)->get();

                $approversNames = $employees->map(function ($employee) {
                    return $employee->first_name . ' ' . $employee->last_name;
                })->toArray();

                $result->approvers_list = implode(', ', $approversNames);
            } else {
                $result->approvers_list = '';
            }

            $result->model_type = $departments[$result->model_type] ?? 'Unknown Department';

        }

        return view('admin.setting.approvals.index', ['results' => $results]);
    }
    /**
     * Show the form for creating a new resource.
     
     */
    public function create1()
    {

        $departments = Module::select('id', 'name')->get();
        $employees = Employee::select('employee_id', 'first_name', 'last_name')->get();

        // dd($departments);
        return view('admin.setting.approvals.create', ['departments' => $departments, 'employees' => $employees]);
    }
    public function store1(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                'number_of_approvers' => [
                    'required',
                    'integer',
                    function ($attribute, $value, $fail) use ($request) {
                        $approversCount = is_array($request->input('approvers')) ? count($request->input('approvers')) : 0;
                        if ((int) $value !== $approversCount) {
                            $fail('The number of approvers must match the approvers selected.');
                        }
                    },
                ],
                'model_type' => [
                    'required',
                    'string',
                ],
                'approvers' => [
                    'required',
                    'array',
                ],
            ], [
                'number_of_approvers.required' => 'The number of approvers is required.',
                'number_of_approvers.integer' => 'The number of approvers must be a valid number.',
                'model_type.required' => 'The model type is required.',
                'approvers.required' => 'Please select at least one approver.',
                'approvers.*.exists' => 'One or more selected approvers are invalid. Please select valid approvers.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $approvers = $request->input('approvers');
            $approvers = array_map('intval', $approvers);

            // $formattedApprovers = array_map(function ($approver) {
            //     return json_decode($approver, true);
            // }, $approvers);

            //now save into the model
            $approvalSettings = new ApprovalSettings();
            $approvalSettings->approver_numbers = $request->input('number_of_approvers');
            $approvalSettings->model_type = $request->input('model_type');
            $approvalSettings->approvers_list = json_encode($approvers);
            $approvalSettings->save();
            return redirect()->route('approvalSettings.index')->with('success', 'Approval settings successfully saved.');

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id 
     */
    public function edit1($id)
    {
        $approvalSetting = ApprovalSettings::findOrFail($id);
        $departments = Module::select('id', 'name')->get();
        $employees = Employee::select('employee_id', 'first_name', 'last_name')->get();

        return view('admin.setting.approvals.edit', [
            'approvalSetting' => $approvalSetting,
            'departments' => $departments,
            'employees' => $employees,
        ]);
    }

    public function update1(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'model_type' => 'required|exists:modules,id',
            'number_of_approvers' => 'required|integer|min:1',
            'approvers' => 'required|array|min:1',
            'approvers.*' => 'exists:employee,employee_id',
        ]);

        $validator->after(function ($validator) use ($request) {
            if (count($request->input('approvers', [])) !== (int) $request->input('number_of_approvers')) {
                $validator->errors()->add(
                    'approvers',
                    'The number of approvers must match the count of selected approvers.'
                );
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $approvalSetting = ApprovalSettings::findOrFail($id);
        $approvers = $request->input('approvers');
        $approvers = array_map('intval', $approvers);

        $approvalSetting->model_type = $request->input('model_type');
        $approvalSetting->approver_numbers = $request->input('number_of_approvers');
        $approvalSetting->approvers_list = json_encode($approvers);

        $approvalSetting->save();

        return redirect()->route('approvalSettings.index')
            ->with('success', __('Approval settings updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $approvalSetting = ApprovalSettings::findOrFail($id);
            $approvalSetting->delete();

            echo "success";
        } catch (\Exception $e) {

            echo 'error';

        }
    }

}
