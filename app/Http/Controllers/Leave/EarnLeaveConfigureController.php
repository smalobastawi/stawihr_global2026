<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;

use App\Models\EarnLeaveRule;

use App\Models\User;
use Illuminate\Http\Request;


class EarnLeaveConfigureController extends Controller
{

   public function index(){
       $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

       $data = EarnLeaveRule::first();
       return view('admin.leave.setup.earnLeaveConfigure',['data' => $data, 'signed_in_user_role'=>$signed_in_user_role]);
   }



   public function updateEarnLeaveConfigure(Request $request){
       $input   = $request->all();
       \Log::info($input);
       $data = EarnLeaveRule::findOrFail($request->earn_leave_rule_id);

       try{
           $data->update($input);
           $bug = 0;
       }
       catch(\Exception $e){
           $bug = $e->getMessage();
           \Log::info($e->getMessage());
       }

       if($bug==0){
           return "success";
       }else {
           return "error";
       }
   }



}
