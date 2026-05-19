<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\Employee\PasswordResetMail;
use App\Mail\Employee\PasswordResetSuccess;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Request password reset link
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User does not exist'
            ], 422);
        }

        // Delete any existing reset tokens for this user
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Create new reset token
        $token = Str::random(60);
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        // Prepare mail content
        $mailContent = [
            'username' => $user->user_name,
            'url' => url('password/reset/' . $token)
        ];

        try {
            \Mail::to($request->email)->send(new PasswordResetMail($mailContent));
            
            return response()->json([
                'status' => 'success',
                'message' => 'Password reset link has been sent to your email'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Password reset email failed: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send reset email. Please try again.'
            ], 500);
        }
    }

    /**
     * Reset password
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $tokenData = DB::table('password_resets')
            ->where('token', $request->token)
            ->where('email', $request->email)
            ->first();

        if (!$tokenData) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid token or email'
            ], 422);
        }

        // Check token expiration (60 minutes)
        if (Carbon::parse($tokenData->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_resets')->where('email', $request->email)->delete();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Password reset token has expired'
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 422);
        }

        // Update password
        $user->password = \Hash::make($request->password);
        $user->save();

        // Delete the token
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Send success email
        try {
            \Mail::to($user->email)->send(new PasswordResetSuccess([
                'username' => $user->user_name
            ]));

            return response()->json([
                'status' => 'success',
                'message' => 'Password has been reset successfully'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Password reset success email failed: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'success',
                'message' => 'Password has been reset successfully, but failed to send confirmation email'
            ], 200);
        }
    }

    /**
     * Validate reset token
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        $tokenData = DB::table('password_resets')
            ->where('token', $request->token)
            ->where('email', $request->email)
            ->first();

        if (!$tokenData) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid token'
            ], 422);
        }

        if (Carbon::parse($tokenData->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_resets')->where('email', $request->email)->delete();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Token has expired'
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Token is valid'
        ], 200);
    }
}