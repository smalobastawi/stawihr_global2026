<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Http\Services\SmsService;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SystemTestNotification;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::getSettings();
        $users = User::where('status', 1)->get();
        return view('admin.setting.system_settings.index', compact('settings', 'users'));
    }

    public function update(Request $request)
    {
        $settings = SystemSetting::getSettings();

        $settings->update([
            'email_notifications_enabled' => $request->has('email_notifications_enabled'),
            'sms_notifications_enabled' => $request->has('sms_notifications_enabled'),
            'inapp_notifications_enabled' => $request->has('inapp_notifications_enabled'),
        ]);

        return redirect()->route('systemSettings.index')->with('success', 'System settings updated successfully.');
    }

    public function testEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            Mail::send('emails.mailExample', ['name' => 'System Test'], function ($message) use ($request) {
                $message->to($request->test_email);
                $message->subject('System Settings - Test Email');
            });

            return redirect()->back()->with('success', 'Test email sent successfully to ' . $request->test_email);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }

    public function testSms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_phone' => 'required|string|min:9|max:15',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $smsService = new SmsService();
            $response = $smsService->sendSMS([
                'mobile' => $request->test_phone,
                'message' => 'This is a test SMS from the HR system.',
            ]);

            if ($response && isset($response['responses'][0]['response-code']) && $response['responses'][0]['response-code'] == 200) {
                return redirect()->back()->with('success', 'Test SMS sent successfully to ' . $request->test_phone);
            }

            return redirect()->back()->with('error', 'SMS API returned an error: ' . json_encode($response));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send test SMS: ' . $e->getMessage());
        }
    }

    public function testInApp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_user_id' => 'required|exists:user,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::findOrFail($request->test_user_id);
            $user->notify(new SystemTestNotification());

            return redirect()->back()->with('success', 'Test in-app notification sent successfully to ' . $user->name);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send test in-app notification: ' . $e->getMessage());
        }
    }
}
