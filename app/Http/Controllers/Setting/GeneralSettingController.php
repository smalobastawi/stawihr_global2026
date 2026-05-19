<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Setting;

use App\Models\PrintHeadSetting;
use Illuminate\Support\Facades\Validator;

use App\Models\CompanyAddressSetting;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;


class GeneralSettingController extends Controller
{


    public function index()
    {
        $data           = CompanyAddressSetting::first();
        $printHeadData  = PrintHeadSetting::first();
        return view('admin.setting.generalSetting',['data' => $data,'printHeadData'=>$printHeadData]);
    }


    public function store(Request $request)
    {
        $validator=validator::make($request->all(),[
            'address'=>'required|max:2000',
        ]);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }


        $input = $request->all();
        try{
            CompanyAddressSetting::create($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug == 0){
            return redirect()->route('generalSettings.index')->with('success', 'Company Address Successfully saved.');
        }else {
            return  redirect()->route('generalSettings.index')->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }


    public function update(Request $request,$id)
    {
        $validator=validator::make($request->all(),[
            'address'=>'required|max:2000',
        ]);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = CompanyAddressSetting::FindOrFail($id);
        $input = $request->all();
        try{
            $data->update($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug == 0){
            return redirect()->route('generalSettings.index')->with('success', 'Company Address Successfully saved.');
        }else {
            return  redirect()->route('generalSettings.index')->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }


    public function printHeadSettingsStore(Request $request)
    {
        $validator=validator::make($request->all(),[
            'description'=>'required|max:2000',
        ]);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }


        $input = $request->all();
        try{
            PrintHeadSetting::create($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug == 0){
            return redirect()->route('generalSettings.index')->with('success', 'Company Address Successfully saved.');
        }else {
            return  redirect()->route('generalSettings.index')->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }


    public function printHeadSettingsUpdate(Request $request,$id)
    {
        $validator=validator::make($request->all(),[
            'description'=>'required|max:2000',
        ]);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = PrintHeadSetting::FindOrFail($id);
        $input = $request->all();
        try{
            $data->update($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = $e->getMessage();
        }

        if($bug == 0){
            return redirect()->route('generalSettings.index')->with('success', 'Company Address Successfully saved.');
        }else {
            return  redirect()->route('generalSettings.index')->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }



}
