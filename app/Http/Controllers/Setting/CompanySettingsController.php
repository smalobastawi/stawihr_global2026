<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\CompanySettings;
use App\Models\FrontSetting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanySettingsController extends Controller
{
    //

    public function index()
    {

        $setting = CompanySettings::orderBy('id', 'desc')->first();
       

        return view('admin.setting.companySettings', ['setting' => $setting]);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'company_title'        => 'required',
            'company_logo'         => 'nullable|mimes:jpeg,jpg,png,gif,webp',
            'home_page_big_title'  => 'required',
            'short_description'    => 'required',
            'service_title'        => 'required',
            'job_title'            => 'required',
            'about_us_image'       => 'nullable|mimes:jpeg,jpg,png,gif,webp',
            'footer_text'          => 'required',
            'about_us_description' => 'required',
            'contact_website'      => 'required',
            'contact_phone'        => 'required',
            'contact_email'        => 'required',
            'contact_address'      => 'required',

        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        try
        {

            $front =  FrontSetting::find($request->id);

            $front->company_title        = $request->company_title;
            $front->home_page_big_title  = $request->home_page_big_title;
            $front->short_description    = $request->short_description;
            $front->service_title        = $request->service_title;
            $front->job_title            = $request->job_title;
            $front->about_us_description = $request->about_us_description;
            $front->contact_website      = $request->contact_website;
            $front->contact_phone        = $request->contact_phone;
            $front->contact_email        = $request->contact_email;
            $front->contact_address      = $request->contact_address;

            $front->show_job             = $request->show_job;
            $front->show_service         = $request->show_service;
            $front->show_counter         = $request->show_counter;
            $front->show_about           = $request->show_about;
            $front->show_contact         = $request->show_contact;
            $front->footer_text          = $request->footer_text;

            $logo           = $request->file('company_logo');
            $about_us_image = $request->file('about_us_image');

            if ($logo) {
                $name = 'logo' . '.' . $logo->getClientOriginalExtension();
                $logo->move('uploads/front/', $name);
                $front->logo = $name;
            }
            if ($about_us_image) {
                $about_name = 'about_us' . '.' . $about_us_image->getClientOriginalExtension();
                $about_us_image->move('uploads/front/', $about_name);
                $front->about_us_image = $about_name;
            }

            $front->update();

            return redirect()->back()->with('success', 'information updated');

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
