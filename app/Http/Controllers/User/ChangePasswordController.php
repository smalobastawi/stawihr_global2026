<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\User;

use App\Http\Requests\ChangePasswordRequest;

use App\Http\Controllers\Controller;
use App\Models\PendingPasswordChange;
use App\Models\VerificationCode;
use App\Mail\Auth\OtpVerification;
use Illuminate\Support\Facades\Mail;

use App\Http\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

use App\Models\User;


class ChangePasswordController extends Controller
{

    public function index() {

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        return view('admin.user.user.changePassword', [ 'signed_in_user_role'=>$signed_in_user_role]);
    }

    public function update(ChangePasswordRequest $request,$id){
        if(env('2FA_PASSWORD_CHANGE')){
            $request->validate([
                'verification_code' => 'required|digits:4'
            ]);
        }
      

        $input['password'] = Hash::make($request['password']);
        $input['password_changed_at'] = date('y-m-d H:i s');
        if(Auth::attempt(['id'=>Auth::user()->id,'password'=>$request->oldPassword])){

            //change password
            if(env('2FA_PASSWORD_CHANGE')){

                $enteredOTP = $request->verification_code;
                
        
                $storedOTP = User::where('id',Auth::user()->id) ->where('verification_code_expiry_date', '>=', now()->subMinutes(10))
                ->value('verification_code');
        
        
                if (!$storedOTP) {
                    return redirect()->back()->with('error', 'Invalid or expired OTP.');
                }
                if ($enteredOTP == $storedOTP) {

                    User::where('id', Auth::user()->id)->update(['password'=>$input['password'],'password_changed_at'=>$input['password_changed_at'],'verification_code'=>null,'verification_code_expiry_date'=>null]);
                    return redirect()->to('/dashboard')->with('success', 'Password successfully updated.');

                } else{
                    return redirect()->back()->with('error', 'Invalid or expired OTP.');
                } 
                
            }else{

                        User::where('id', Auth::user()->id)->update($input);
                    return redirect()->to('/dashboard')->with('success', 'Password successfully updated.');

                }
            
        }else{
            return redirect()->back()->with('error', 'Old Password does not match.');
        }
    }

}
    



