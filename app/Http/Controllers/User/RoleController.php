<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

use App\Http\Requests\RoleRequest;
use App\Models\Permissions;
use Illuminate\Http\Request;

//use App\Models\Role;
use App\Models\User;
use Spatie\Permission\Contracts\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    public function index(){
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $data =Role::all();
        return view('admin.user.role.index',compact('data'));
    }



    public function create(){
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.user.role.form');
    }



    public function store(RoleRequest $request) {
        $input = $request->all();
        try{
            Role::create($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug==0){
            return redirect('userRole')->with('success', 'Role Successfully saved.');
        }else {
            return redirect('userRole')->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }



    public function edit($id){
        $editModeData =Role::FindOrFail($id);
        $permissions=Permissions::all();
        $data =Role::all();
        return view('admin.user.role.form',compact('editModeData','data'));
    }



    public function update(RoleRequest $request,$id) {
          $data = Role::FindOrFail($id);
          $input = $request->all();
          try{
              $data->update($input);
              $bug = 0;
          }
          catch(\Exception $e){
              $bug = $e->getMessage();
          }

          if($bug==0){
              return redirect()->back()->with('success', 'Role Successfully Updated.');
          }else {
              return redirect()->back()->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
          }
    }



    public function destroy($id){

        $count = User::where('role_id','=',$id)->count();

        if ($count>0) {
          return "hasForeignKey";
        }

        if ($id == 1) {
           return "error";
        }
        try{
            $role = Role::FindOrFail($id);
            $role->delete();
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug==0){
            echo "success";
        }elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }


}
