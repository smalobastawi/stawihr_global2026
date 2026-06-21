<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\User;
use Validator;
use App\Http\Controllers\Controller;
use App\Http\Controllers\User\LoginController;
use App\Lib\Enumerations\UserStatus;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Check login options
     */
    public function checkloginOptions(Request $request)
    {
        $passwordLogin = config('app.password_login');
        $googleLogin = filled(config('services.google.client_id'));
        $azureLogin = filled(config('services.microsoft.client_id'));

        return response()->json([
            'passwordLogin' => (bool) $passwordLogin,
            'googleLogin' => $googleLogin,
            'azureLogin' => $azureLogin,
            'azureClientId' => $azureLogin ? config('services.microsoft.client_id') : null,
            'azureTenantId' => $azureLogin ? config('services.microsoft.tenant') : null,
            'organizationName' => config('app.name'),
            'applicationTitle' => config('app.name'),
        ]);
    }

    /**
     * Password Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt(['user_name' => $request->username, 'password' => $request->password])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = $request->user();

        // Generate token
        $expirationDate = Carbon::now()->addMinutes(config('sanctum.expiration'));
        $tokenResult = $user->createToken('API Token', ['*'], $expirationDate);
        $token = $tokenResult->plainTextToken;

        // Get roles and permissions
        $roles = $user->roles()->pluck('name');
        $permissions = $user->getAllPermissions()->pluck('name');

        // Get profile picture
        $defaultPhoto = asset('admin_assets/img/default.png');
        $profilePicUrl = optional($user->employeeDetails)->photo
            ? asset('Uploads/employeePhoto/' . $user->employeeDetails->photo)
            : $defaultPhoto;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'accessToken' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_name' => $user->user_name,
                'profile_pic_url' => $profilePicUrl,
            ],
            'roles' => $roles,
            'permissions' => $permissions,
            'expires_at' => $expirationDate->toDateTimeString()
        ]);
    }

    /**
     * Google Login - Only authenticate existing users
     */
    public function loginWithGoogle(Request $request)
    {
        // Validate request
        $request->validate([
            'email' => 'required|email',
            'google_id' => 'required|string',
            'name' => 'required|string',
            'photo' => 'nullable|string',
            'platform' => 'nullable|string|in:web,mobile',
        ]);

        // Check if user exists by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No account found with this email address. Please contact your administrator.'
            ], 404);
        }

        // Get current google_ids as an array
        $googleIds = $user->google_ids ?? [];

        // If google_ids is null/empty and old google_id exists, add it
        if (empty($googleIds) && !empty($user->google_id)) {
            $googleIds = [$user->google_id];
        }

        // Check if this Google ID is already associated
        if (!in_array($request->google_id, $googleIds)) {
            // Add new Google ID to the array
            $googleIds[] = $request->google_id;

            // Update the user with new google_ids
            $user->update([
                'google_ids' => $googleIds,
                // Optional: Keep the original google_id field for backward compatibility
                'google_id' => empty($user->google_id) ? $request->google_id : $user->google_id
            ]);
        }

        // Verify google_id exists in the array (security check)
        if (!empty($googleIds) && !in_array($request->google_id, $googleIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Google account mismatch. Please contact your administrator.'
            ], 403);
        }

        // Generate token
        $expirationDate = Carbon::now()->addMinutes(config('sanctum.expiration'));
        $tokenResult = $user->createToken('API Token', ['*'], $expirationDate);
        $token = $tokenResult->plainTextToken;

        // Get roles and permissions
        $roles = $user->roles()->pluck('name');
        $permissions = $user->getAllPermissions()->pluck('name');

        // Get profile picture (prioritize Google photo if available)
        $defaultPhoto = asset('admin_assets/img/default.png');
        $profilePicUrl = optional($user->employeeDetails)->photo
            ? asset('Uploads/employeePhoto/' . $user->employeeDetails->photo)
            : ($request->photo ?? $defaultPhoto);

        // Optional: Log platform info for debugging
        if ($request->filled('platform')) {
            \Log::info('Google login', [
                'user_id' => $user->id,
                'email' => $user->email,
                'platform' => $request->platform,
                'google_id' => $request->google_id,
                'all_google_ids' => $user->google_ids
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged in with Google',
            'accessToken' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_name' => $user->user_name,
                'profile_pic_url' => $profilePicUrl,
            ],
            'roles' => $roles,
            'permissions' => $permissions,
            'expires_at' => $expirationDate->toDateTimeString()
        ]);
    }

    /**
     * Azure Login - Only authenticate existing users
     */
    public function loginWithAzure(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'azure_id' => 'required|string',
            'name' => 'required|string',
            'platform' => 'nullable|string|in:web,mobile',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Microsoft Account (' . $request->email . ') not recognized. Contact Admin.'
            ], 404);
        }

        if ($user->status != UserStatus::$ACTIVE) {
            return response()->json([
                'success' => false,
                'message' => 'Your account is not active. Please contact admin.'
            ], 403);
        }

        $expirationDate = Carbon::now()->addMinutes(config('sanctum.expiration'));
        $tokenResult = $user->createToken('API Token', ['*'], $expirationDate);
        $token = $tokenResult->plainTextToken;

        $roles = $user->roles()->pluck('name');
        $permissions = $user->getAllPermissions()->pluck('name');

        $defaultPhoto = asset('admin_assets/img/default.png');
        $profilePicUrl = optional($user->employeeDetails)->photo
            ? asset('Uploads/employeePhoto/' . $user->employeeDetails->photo)
            : $defaultPhoto;

        if ($request->filled('platform')) {
            \Log::info('Azure login', [
                'user_id' => $user->id,
                'email' => $user->email,
                'platform' => $request->platform,
                'azure_id' => $request->azure_id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged in with Azure',
            'accessToken' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_name' => $user->user_name,
                'profile_pic_url' => $profilePicUrl,
            ],
            'roles' => $roles,
            'permissions' => $permissions,
            'expires_at' => $expirationDate->toDateTimeString()
        ]);
    }

    /**
     * Register new user
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:user',
            'password' => 'required|string',
            'c_password' => 'required|same:password'
        ]);

        $user = new User([
            'name'  => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        if ($user->save()) {
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Successfully created user!',
                'accessToken' => $token,
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'Provide proper details'
            ], 400);
        }
    }

    /**
     * Get the authenticated User
     */
    public function user(Request $request)
    {
        $user = $request->user();

        // Get roles and permissions
        $roles = $user->roles()->pluck('name');
        $permissions = $user->getAllPermissions()->pluck('name');

        // Default profile photo
        $defaultPhoto = asset('admin_assets/img/default.png');

        // Get employee photo if user has employee details
        $profilePicUrl = optional($user->employeeDetails)->photo
            ? asset('Uploads/employeePhoto/' . $user->employeeDetails->photo)
            : $defaultPhoto;

        // Convert user object to an array and append profile_pic_url
        $userData = $user->toArray();
        $userData['profile_pic_url'] = $profilePicUrl;
        $userData['roles'] = $roles;
        $userData['permissions'] = $permissions;

        return response()->json([
            'success' => true,
            'user' => $userData
        ]);
    }

    /**
     * Logout user (Revoke the token)
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Whether password change requires OTP verification.
     */
    public function passwordChangeOptions(Request $request)
    {
        return response()->json([
            'success' => true,
            'requires_otp' => (bool) env('2FA_PASSWORD_CHANGE', false),
        ]);
    }

    /**
     * Send OTP for password change (mobile/API, Sanctum authenticated).
     */
    public function sendPasswordChangeOtp(Request $request)
    {
        if (!env('2FA_PASSWORD_CHANGE')) {
            return response()->json([
                'success' => false,
                'message' => 'OTP service not available',
            ], 400);
        }

        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        return app(LoginController::class)->sendOtpToUser($user);
    }

    /**
     * Change password for the authenticated user.
     */
    public function changePassword(Request $request)
    {
        $rules = [
            'oldPassword' => 'required|string',
            'password' => [
                'required',
                'confirmed',
                'min:6',
                'regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            ],
        ];

        if (env('2FA_PASSWORD_CHANGE')) {
            $rules['verification_code'] = 'required|digits:4';
        }

        $request->validate($rules);

        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if (!Hash::check($request->oldPassword, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Old password does not match.',
            ], 422);
        }

        if (env('2FA_PASSWORD_CHANGE')) {
            $storedOtp = User::where('id', $user->id)
                ->where('verification_code_expiry_date', '>=', now()->subMinutes(10))
                ->value('verification_code');

            if (!$storedOtp || (string) $request->verification_code !== (string) $storedOtp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP.',
                ], 422);
            }
        }

        $user->update([
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
            'verification_code' => null,
            'verification_code_expiry_date' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password successfully updated.',
        ]);
    }

    /**
     * Verify email and generate token
     */
    public function verifyEmailAndGenerateToken(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No user found with this email address'
            ], 404);
        }

        $expirationDate = Carbon::now()->addMinutes(config('sanctum.expiration'));
        $tokenResult = $user->createToken('API Token', ['*'], $expirationDate);
        $token = $tokenResult->plainTextToken;

        $roles = $user->roles()->pluck('name');
        $permissions = $user->getAllPermissions()->pluck('name');

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully',
            'accessToken' => $token,
            'token_type' => 'Bearer',
            'user_id' => $user->id,
            'roles' => $roles,
            'permissions' => $permissions,
            'expires_at' => $expirationDate->toDateTimeString()
        ]);
    }
}
