<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    //
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {


        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Check if the user exists in the database
            $user = User::where('email', $googleUser->getEmail())->first();
            if (!$user) {
                return redirect()->route('login')->with('error', 'Your account is not registered. Please contact the administrator.');
            }

            $user->update([
                'google_id' => $googleUser->getId(),
                'token' => $googleUser->token,
                'refresh_token' => $googleUser->refreshToken ?? null,
                'expires_in' => $googleUser->expiresIn,
            ]);
            // Log in the existing user
            Auth::login($user);

            $employee = Employee::where('user_id', $user->id)->first();
            if (!$employee) {
                $user_data = [
                    "id" => $user->id,
                    "user_name" => $user->user_name,
                    "role_id" => $user->role_id,
                    "password_changed_at" => $user->password_change_at,
                ];
            } else {

                $user_data = [
                    "id" => $user->id,
                    "user_name" => $user->user_name,
                    "role_id" => $user->role_id,
                    "employee_id" => $employee->employee_id,
                    "email" => $employee->email,
                    "password_changed_at" => $user->password_change_at,
                ];
            }

            session()->put('logged_session_data', $user_data);
            refreshEnabledModules();

            if (($user->password_changed_at == null)) {
                $user->update([
                    'password_changed_at' => now()
                ]);
                $biometicStatusToday = helper_getBiometricAttendance();  //Update Biometrric attendance status
                return redirect()->intended(route('home.dashboard'));
            } else {

                $biometicStatusToday = helper_getBiometricAttendance();  //Update Biometrric attendance status
                return redirect()->intended(route('home.dashboard'));
            }
        } catch (Exception $e) {
            Log::error('Google Login Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('login')->with('error', 'Google login failed!' . $e->getMessage());
        }
    }


    public function handleGoogleCallbackMobile(Request $request)
    {

        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Check if the user exists in the database
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is not registered. Please contact the administrator.'
                ], 401);
            }

            // Update user with Google data
            $user->update([
                'google_id' => $googleUser->getId(),
                'token' => $googleUser->token,
                'refresh_token' => $googleUser->refreshToken ?? null,
                'expires_in' => $googleUser->expiresIn,
            ]);

            // Get employee data if exists
            $employee = Employee::where('user_id', $user->id)->first();

            $user_data = [
                "id" => $user->id,
                "user_name" => $user->user_name,
                "role_id" => $user->role_id,
                "password_changed_at" => $user->password_change_at,
            ];

            if ($employee) {
                $user_data['employee_id'] = $employee->employee_id;
                $user_data['email'] = $employee->email;
            }

            // Update password changed at if null
            if ($user->password_changed_at == null) {
                $user->update(['password_changed_at' => now()]);
            }

            // Create API token for mobile
            $token = $user->createToken('mobile-google-auth')->plainTextToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => $user_data
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Google login failed!' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
