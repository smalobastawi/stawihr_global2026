<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\AwardNoticeAndTraining;

use App\Http\Controllers\Controller;

use App\Http\Requests\NoticeRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notice;
use App\Models\Department;
use App\Models\Location;
use App\Models\Region;
use App\Models\Employee;
use App\Notifications\NoticeNotification;
use App\Mail\NoticeMail;
use App\Http\Services\SmsService;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class NoticeController extends Controller
{


    public function index()
    {
        $results = Notice::with(['departments', 'regions', 'locations'])
            ->orderBy('notice_id', 'DESC')
            ->get();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        return view('admin.notice.index', ['results' => $results, 'signed_in_user_role' => $signed_in_user_role]);
    }



    public function create()
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $departments = Department::all();
        $locations = Location::all();
        $regions = Region::all();
        $employees = Employee::where('status', 1)->get();
        return view('admin.notice.form', [
            'signed_in_user_role' => $signed_in_user_role,
            'departments' => $departments,
            'locations' => $locations,
            'regions' => $regions,
            'employees' => $employees
        ]);
    }



    public function store(NoticeRequest $request)
    {

        $file     = $request->file('attach_file');
        $input  = $request->all();
        $input['created_by'] = Auth::user()->id;
        $input['updated_by'] = Auth::user()->id;
        $input['publish_date'] = dateConvertFormtoDB($request->publish_date);

        if ($file) {
            $fileName = md5(Str::random(30) . time() . '_' . $request->file('attach_file')) . '.' . $request->file('attach_file')->getClientOriginalExtension();
            $request->file('attach_file')->move('uploads/notice/', $fileName);
            $input['attach_file'] = $fileName;
        }
        $bug = 0;
        try {
            $notice = Notice::create($input);
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            // Attach relationships
            if ($request->has('departments')) {
                $notice->departments()->sync($request->input('departments'));
            }
            if ($request->has('regions')) {
                $notice->regions()->sync($request->input('regions'));
            }
            if ($request->has('locations')) {
                $notice->locations()->sync($request->input('locations'));
            }
            if ($request->has('employees')) {
                $notice->employees()->sync($request->input('employees'));
            }

            // Send notifications if checkbox is checked
            if ($request->has('send_notification') && $request->send_notification == '1') {
                // Get targeted employees
                $targetedEmployees = $notice->getTargetedEmployees();

                // Get users from targeted employees
                $users = $targetedEmployees->pluck('user')->filter()->unique();

                // Send database/broadcast notifications
                Notification::send($users, new NoticeNotification($notice));

                // Send email notifications
                foreach ($users as $user) {
                    if ($user->email) {
                        Mail::to($user->email)->send(new NoticeMail($notice));
                    }
                }
            }

            // Send SMS notifications if checkbox is checked
            if ($request->has('send_sms') && $request->send_sms == '1') {
                // Get targeted employees
                $targetedEmployees = $notice->getTargetedEmployees();
                $smsService = app(SmsService::class);

                foreach ($targetedEmployees as $employee) {
                    if ($employee->phone) {
                        $message = "Notice: {$notice->title}. Published: {$notice->publish_date}";
                        $smsService->sendSMS([
                            'mobile' => $employee->phone,
                            'message' => $message
                        ]);
                    }
                }
            }

            return redirect('notice')->with('success', 'Notice Successfully saved.');
        } else {
            return redirect('notice')->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ' . $bug);
        }
    }



    public function show($id)
    {

        $editModeData = Notice::with(['createdBy', 'departments', 'regions', 'locations'])->where('notice_id', $id)->first();

        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        return view('admin.notice.details', ['signed_in_user_role' => $signed_in_user_role], compact('editModeData'));
    }



    public function edit($id)
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $editModeData = Notice::with(['departments', 'regions', 'locations', 'employees'])->findOrFail($id);
        $departments = Department::all();
        $locations = Location::all();
        $regions = Region::all();
        $employees = Employee::where('status', 1)->get();
        return view('admin.notice.form', [
            'signed_in_user_role' => $signed_in_user_role,
            'editModeData' => $editModeData,
            'departments' => $departments,
            'locations' => $locations,
            'regions' => $regions,
            'employees' => $employees
        ]);
    }



    public function update(NoticeRequest $request, $id)
    {

        $file = $request->file('attach_file');
        $data = Notice::FindOrFail($id);
        $input = $request->all();
        $input['created_by'] = Auth::user()->id;
        $input['updated_by'] = Auth::user()->id;
        $input['publish_date'] = dateConvertFormtoDB($request->publish_date);


        if ($file) {
            $fileName = md5(Str::random(30) . time() . '_' . $request->file('attach_file')) . '.' . $request->file('attach_file')->getClientOriginalExtension();
            $request->file('attach_file')->move('uploads/notice/', $fileName);
            if (file_exists('uploads/notice/' . $data->attach_file) and !empty($data->attach_file)) {
                unlink('uploads/notice/' . $data->attach_file);
            }
            $input['attach_file'] = $fileName;
        }

        try {
            $data->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            // Sync relationships
            if ($request->has('departments')) {
                $data->departments()->sync($request->input('departments'));
            } else {
                $data->departments()->sync([]);
            }
            if ($request->has('regions')) {
                $data->regions()->sync($request->input('regions'));
            } else {
                $data->regions()->sync([]);
            }
            if ($request->has('locations')) {
                $data->locations()->sync($request->input('locations'));
            } else {
                $data->locations()->sync([]);
            }
            if ($request->has('employees')) {
                $data->employees()->sync($request->input('employees'));
            } else {
                $data->employees()->sync([]);
            }

            return redirect()->back()->with('success', 'Notice Successfully Updated.');
        } else {
            return redirect()->back()->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }



    public function destroy($id)
    {
        try {
            $data = Notice::FindOrFail($id);
            if (!is_null($data->attach_file)) {
                if (file_exists('uploads/notice/' . $data->attach_file) and !empty($data->attach_file)) {
                    unlink('uploads/notice/' . $data->attach_file);
                }
            }
            $data->delete();
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
