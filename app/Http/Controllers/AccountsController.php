<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers;

use App\Mail\Leave\StaffLeaveApplicationMail;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Mail\Employee\PasswordResetMail;
use App\Mail\Employee\PasswordResetSuccess;
use DB;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Test comment
class AccountsController extends Controller
{
    public function validatePasswordRequest(Request $request)
    {

        //$user = Employee::where('email', $request->email)->first();
        $user = User::where('email', $request->email)->first();
        if ($user == null) {
            return redirect()->back()->withErrors(['email' => trans('User does not exist')]);
        }

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => Str::random(60), //change 60 to any length you want
            'created_at' => Carbon::now()
        ]);
        $tokenData = DB::table('password_resets')
            ->where('email', $request->email)->first();

        $token = $tokenData->token;
        $email = $request->email; // or $email = $tokenData->email;
        $username = User::where('id', $user->id)->pluck('user_name')->first();

        $mailContent = ([
            'username' => $username,
            'url' => url('password/reset/' . $token)
        ]);

        try {
            \Mail::to($email)->send(new PasswordResetMail($mailContent));
            return redirect()->back()->with('success', trans('A reset link has been sent to your email.'));

        } catch (\Exception $e) {
            \Log::info($e->getMessage() . 'Password reset email failed');
            return redirect()->back()->withErrors(['error' => trans('A Network Error occurred. Please try again.')]);
        }
    }

    public function resetPassword(Request $request)
    {
        //Validate input
     
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:user,email',
            'password' => 'required|confirmed',
            'token' => 'required']);

        //check if payload is valid before moving on
        if ($validator->fails()) {
            return redirect()->back()->withErrors(['email' => 'Please complete the form']);
        }

        $password = $request->password;// Validate the token
        $tokenData = DB::table('password_resets')->where('token', $request->token)->first();
        if (!$tokenData) return redirect()->back()->withErrors(['email' => 'Invalid token']);
       // $userId = Employee::where('email', $tokenData->email)->pluck('id')->first();
        $username = User::where('email', $tokenData->email)->pluck('user_name')->first();
        $user = User::where('email', $tokenData->email)->first();
        if (!$user) return redirect()->back()->withErrors(['email' => 'Email not found']);//Hash and update the new password
        $user->password = \Hash::make($password);
        $user->update(); //or $user->save();

        DB::table('password_resets')->where('email', $tokenData->email)
            ->delete();

        //Send Email Reset Success Email
        $mailContent = ([
            'username' => $username,
        ]);
        try {
            \Mail::to($tokenData->email)->send(new PasswordResetSuccess($mailContent));
            return redirect(url('/login'))->with('success', trans('Password reset success. Use your new password to login.'));
            //return redirect()->back()

        } catch (\Exception $e) {
            \Log::info($e->getMessage() . 'Password reset success email failed');
            return redirect()->back()->withErrors(['error' => trans('A Network Error occurred. Please try again.')]);
        }
    }

    public function resetForm($token)
    {
        $tokenData = DB::table('password_resets')->where('token', $token)->first();
        $content = $tokenData;

        if (!$tokenData) return redirect()->to('/'); 
        return view('admin.user.user.password_reset', compact('content'));
    }

    public function sendPasswordReset( $id)
    {

        //$user = Employee::where('email', $request->email)->first();
        $user = User::where('id', $id)->first();
        if ($user == null) {
            return redirect()->back()->withErrors(['email' => trans('User does not exist')]);
        }

        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => Str::random(60), //change 60 to any length you want
            'created_at' => Carbon::now()
        ]);
        $tokenData = DB::table('password_resets')
            ->where('email', $user->email)->first();

        $token = $tokenData->token;
        $email = $user->email; // or $email = $tokenData->email;
        $username = User::where('id', $user->id)->pluck('user_name')->first();

        $mailContent = ([
            'username' => $username,
            'url' => url('password/reset/' . $token)
        ]);

        try {
            \Mail::to($email)->send(new PasswordResetMail($mailContent));
            echo "success";

        } catch (\Exception $e) {
            \Log::info($e->getMessage() . 'Password reset email failed');
            echo 'error';
        }
    }
}
