<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\User;

use Socialite;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Location;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use App\Models\VerificationCode;
use App\Http\Services\SmsService;
use App\Mail\Auth\OtpVerification;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Lib\Enumerations\UserStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\PendingPasswordChange;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{

    public function index()
    {
        if (Auth::check()) {
            // Skip password change check for Azure logins
            if (Auth::user()->password_changed_at == null && session('login_method') !== 'azure') {
                return redirect(url('/changePassword'));
            }
            return redirect()->intended(url('/dashboard'));
        }

        return view('admin.login');
    }
    public function verify()
    {
        return view('admin.verify');
    }

    public function Auth(LoginRequest $request)
    {
        // Attempt authentication
        $credentials = ['user_name' => $request->user_name, 'password' => $request->user_password];
        $user = User::where('user_name', $request->user_name)->first();

        if ($user && Hash::check($request->user_password, $user->password)) {
            if ($user->status != UserStatus::$ACTIVE) {
                return redirect(url('/login'))->with('error', 'Your account is inactive. Contact admin.');
            }
            if ($user->password_changed_at) {
                $passwordChangedAt = \Carbon\Carbon::parse($user->password_changed_at);
                // Convert to Carbon instance
                $expiryDays = env('PASSWORD_EXPIRY_DAYS', 90); // Default to 90 days if not set

                if (is_numeric($expiryDays) && $passwordChangedAt->lt(now()->subDays((int) $expiryDays))) {
                    Auth::login($user);
                    $user->password_changed_at = null;
                    $user->save();
                }
            }
            // Check if 2FA is enabled
            if (env('2FA_LOGIN')) {
                // Generate OTP
                $otpCode = rand(1000, 9999);
                session(['2fa_otp' => $otpCode, '2fa_user_id' => $user->id]);

                // Save OTP in database
                $user->verification_code = $otpCode;
                $user->verification_code_expiry_date = now()->addMinutes(10);
                $user->save();

                $send_otp_sms = new SmsService();
                $mobile_number = $user->msisdn ?: optional(Employee::where('user_id', $user->id)->first())->phone;

                if (str_starts_with($mobile_number, "07") || str_starts_with($mobile_number, "01")) {
                    $mobile_number = "254" . substr($mobile_number, 1);
                }

                if (isset($mobile_number)) {
                    // Send OTP via SMS
                    $verification_code_sending = $send_otp_sms->sendSMS(['mobile' => $mobile_number, 'message' => "Your verification code is: $otpCode",]);

                    $sms_sent = ($verification_code_sending && isset($verification_code_sending['responses'][0]['response-code'])
                        && $verification_code_sending['responses'][0]['response-code'] == 200);
                } else {
                    return redirect(url('/login'))->with('error', 'User must have mobile number for OTP to be sent.');
                }

                $email_content = "Your OTP verification code is: $otpCode";

                // Send OTP via Email
                try {

                    Mail::to($user->email)->send(new OtpVerification($email_content));
                    $email_sent = true;
                } catch (\Exception $e) {

                    return redirect(url('/login'))->with('error', 'Failed to send OTP email: ' . $e->getMessage());
                    $email_sent = false;
                }


                if ($sms_sent || $email_sent) {

                    return redirect(url('/verify'))->with('success', 'OTP sent. Please verify.');
                } else {
                    return redirect(url('/login'))->with('error', 'An error occurred. Please try again!');
                }
            } else {
                // Log in user directly if 2FA is disabled
                Auth::login($user);

                $employee = Employee::where('user_id', $user->id)->first();

                $user_data = [
                    "id" => $user->id,
                    "user_name" => $user->user_name,
                    "role_id" => $user->role_id,
                    "employee_id" => $employee ? $employee->employee_id : null,
                    "email" => $employee ? $employee->email : null,
                    "password_changed_at" => $user->password_changed_at,
                ];
                session()->put('logged_session_data', $user_data);
                refreshEnabledModules();
                //check and update Biometric Login for the user
                $biometicStatusToday = helper_getBiometricAttendance();

                return redirect()->intended(url('/dashboard'));
            }
        }
        // Authentication failed
        return redirect(url('/login'))->with('error', 'Invalid username or password.');
    }



    public function sendOtpToUser($user)
    {

        if (!$user) {
            $user = User::where('user_name', request()->user_name)->first();
        }
        // Generate OTP
        $otpCode = rand(1000, 9999);
        session(['2fa_otp' => $otpCode, '2fa_user_id' => $user->id]);


        $expiration_time = now()->addMinutes(10);
        // Save OTP in database
        $user->verification_code = $otpCode;
        $user->verification_code_expiry_date = $expiration_time;
        $user->save();

        // Send OTP via SMS
        $send_otp_sms = new SmsService();
        $mobile_number = $user->msisdn;


        if (str_starts_with($mobile_number, "07") || str_starts_with($mobile_number, "01")) {
            $mobile_number = "254" . substr($mobile_number, 1);
        } elseif (str_starts_with($mobile_number, "7") || str_starts_with($mobile_number, "1")) {
            $mobile_number = "254" . $mobile_number;
        }

        $mobile_number = $user->msisdn ?: optional(Employee::where('user_id', $user->id)->first())->phone;

        if (isset($mobile_number)) {
            // Send OTP via SMS
            $verification_code_sending = $send_otp_sms->sendSMS(['mobile' => $mobile_number, 'message' => "Your verification code is: $otpCode",]);

            $sms_sent = ($verification_code_sending && isset($verification_code_sending['responses'][0]['response-code'])
                && $verification_code_sending['responses'][0]['response-code'] == 200);
        } else {
            return response()->json(['message' => 'User must have mobile number for OTP to be sent.'], 400);
        }

        // Send OTP via Email
        $email_content = "Your OTP verification code is: $otpCode and will expire at $expiration_time";

        try {
            Mail::to($user->email)->send(new OtpVerification($email_content));
            $email_sent = true;
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send OTP email: ' . $e->getMessage()], 500);
            $email_sent = true;
        }


        // Return success response if SMS or email was sent successfully
        if ($sms_sent || $email_sent) {
            return response()->json(['message' => 'OTP sent successfully!', 'success' => true]);
        }

        return response()->json(['message' => 'Failed to send OTP. Please try again!'], 500);
    }


    public function sendPasswordChangeOtp(Request $request)
    {
        $user = Auth::user();

        // Check if 2FA_PASSWORD_CHANGE is enabled from .env level
        if (env('2FA_PASSWORD_CHANGE')) {
            return $this->sendOtpToUser($user);
        }

        return response()->json(['message' => 'OTP service not available'], 500);
    }

    public function resendOtp(Request $request)
    {
        $userId = session('2fa_user_id');
        $user = User::where('id', session('2fa_user_id'))->first();
        return $this->sendOtpToUser($user);
    }


    public function verifyOTP(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|digits:4'
        ]);

        $enteredOTP = $request->verification_code;
        $storedOTP = session('2fa_otp');
        $userId = session('2fa_user_id');

        if (!$userId || !$storedOTP) {
            return redirect(url('/login'))->with('error', 'Session expired. Please log in again.');
        }

        $verificationCode = User::where('id', $userId)->where('verification_code', $enteredOTP)
            ->where('verification_code_expiry_date', '>=', now()->subMinutes(10))
            ->first();


        if (!$verificationCode) {
            return redirect()->back()->with('error', 'Invalid or expired OTP.');
        }


        if ($enteredOTP == $storedOTP) {
            // Fetch user
            $user = User::find($userId);
            Auth::login($user);

            // Store session data
            $employee = Employee::where('user_id', $user->id)->first();
            $user_data = [
                "id" => $user->id,
                "user_name" => $user->user_name,
                "role_id" => $user->role_id,
                "employee_id" => $employee ? $employee->employee_id : null,
                "email" => $employee ? $employee->email : null,
                "password_changed_at" => $user->password_changed_at,
            ];
            session()->put('logged_session_data', $user_data);
            refreshEnabledModules();

            // Clear OTP session
            session()->forget(['2fa_otp', '2fa_user_id']);
            //here check if the otp is from  a change password request
            $verificationCode->update(['verification_code' => null, 'expired_at' => null]);

            // Redirect to appropriate page

            return redirect(url('/dashboard'))->with('success', 'Login successful.');
        } else {

            return redirect(url('/verify'))->with('error', 'Invalid OTP. Please try again.');
        }
    }



    public function logout()
    {
        if (Auth::check()) {
            $user = User::findOrFail(Auth::user()->id);
            // Clear Google tokens from database
            $user->update([
                'google_access_token' => null,
                'google_refresh_token' => null,
                'google_token_expires_at' => null,
            ]);
        }
        Auth::logout();
        Session::flush();

        return redirect()->route('login')->with('success', 'Logout successful ..!');
    }

    public function redirectToAzure()
    {
        return Socialite::driver('microsoft')->redirect();
    }

    public function handleAzureCallback()
    {
        try {
            $azureUser = Socialite::driver('microsoft')->user();
            $azureEmail = $azureUser->getEmail();
            $user = User::where('email', $azureEmail)->first();

            if (!$user) {
                return redirect('/login')->with('error', 'Microsoft Account (' . $azureEmail . ') not recognized. Contact Admin.');
            }

            Auth::login($user);

            $userStatus = Auth::user()->status;

            if ($userStatus != UserStatus::$ACTIVE) {
                Auth::logout();
                return redirect(url('/login'))->withInput()->with('error', 'Your account is not active. Please contact admin.');
            }

            $employee = Employee::where('user_id', Auth::user()->id)->first();

            $user_data = [
                "id" => Auth::user()->id,
                "user_name" => Auth::user()->user_name,
                "role_id" => Auth::user()->role_id,
                "employee_id" => $employee ? $employee->employee_id : null,
                "email" => $employee ? $employee->email : null,
                "password_changed_at" => Auth::user()->password_changed_at,
                "login_method" => 'azure' // Add this flag
            ];

            if (!$user->password_changed_at) {
                $user->password_changed_at = now();
                $user->save();
            }
            session()->put('logged_session_data', $user_data);
            refreshEnabledModules();

            // Check biometric status
            $biometicStatusToday = helper_getBiometricAttendance();

            // DIRECTLY redirect to dashboard, bypassing the index() check
            return redirect(url('/dashboard'))->with('success', 'Azure login successful.');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Azure login failed: ' . $e->getMessage());
        }
    }
}