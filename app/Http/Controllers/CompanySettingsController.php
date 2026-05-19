<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers;

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

        $settings = CompanySettings::orderBy('id', 'desc')->first();
       
        return view('admin.setting.companySettings', ['setting' => $settings]);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'legal_Name'        => 'required',
            'legal_Address'      => 'required',
            'official_contact_number'        => 'required',
            'official_email'        => 'required',
            'company_contact_name'      => 'required',

        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }
        $companySetttings =  CompanySettings::where('legal_Name', $request->legal_Name)->first();
        if($companySetttings)
        {
            $companySetttings->update([
                'legal_Name' => $request->input('legal_Name'),
                'legal_Address' => $request->input('legal_Address'),
                'official_contact_number' => $request->input('official_contact_number'),
                'official_email' => $request->input('official_email'),
                'company_contact_name' => $request->input('company_contact_name'),
                'representative_phone' => $request->input('representative_phone'),
                'representative_email' => $request->input('representative_email'),
                'KRA_PIN' => $request->input('KRA_PIN'),
                'employer_number' => $request->input('employer_number'),
                'NSSF_employer_number' => $request->input('NSSF_employer_number'),
                'NHIF_employer_code' => $request->input('NHIF_employer_code'),
                'financial_year_start' => $request->input('financial_year_start'),
                'updated_at' => now(),
            ]);

        }
        else{
            $company = CompanySettings::create([
                'legal_Name' => $request->input('legal_Name'),
                'legal_Address' => $request->input('legal_Address'),
                'official_contact_number' => $request->input('official_contact_number'),
                'official_email' => $request->input('official_email'),
                'company_contact_name' => $request->input('company_contact_name'),
                'representative_phone' => $request->input('representative_phone'),
                'representative_email' => $request->input('representative_email'),
                'KRA_PIN' => $request->input('KRA_PIN'),
                'employer_number' => $request->input('employer_number'),
                'NSSF_employer_number' => $request->input('NSSF_employer_number'),
                'NHIF_employer_code' => $request->input('NHIF_employer_code'),
                'financial_year_start' => $request->input('financial_year_start'),
                'created_at' => now(),
                
            ]);
        }

        try
        {

           
          

            return redirect()->back()->with('success', 'information updated');

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function store2(Request $request)
    {
        $request->validate([
            'company_title' => 'required|string|max:255',
            'home_page_big_title' => 'required|string',
            'short_description' => 'required|string',
            'service_title' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'about_us_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'about_us_description' => 'required|string',
            'contact_website' => 'nullable|url|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_address' => 'nullable|string',
            'show_job' => 'required|boolean',
            'show_service' => 'required|boolean',
            'show_about' => 'required|boolean',
            'show_contact' => 'required|boolean',
            'show_counter' => 'required|boolean',
            'footer_text' => 'nullable|string|max:255',
        ]);
    
        $setting = FrontSetting::first(); // Assuming we are updating the first record, if needed you can modify this.
    
        // Handle file uploads
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = time().'.'.$logo->getClientOriginalExtension();
            $logo->move(public_path('uploads/front/'), $logoName);
            $setting->logo = $logoName;
        }
    
        if ($request->hasFile('about_us_image')) {
            $aboutUsImage = $request->file('about_us_image');
            $aboutUsImageName = time().'.'.$aboutUsImage->getClientOriginalExtension();
            $aboutUsImage->move(public_path('uploads/front/'), $aboutUsImageName);
            $setting->about_us_image = $aboutUsImageName;
        }
    
        // Update the remaining fields
        $setting->company_title = $request->company_title;
        $setting->home_page_big_title = $request->home_page_big_title;
        $setting->short_description = $request->short_description;
        $setting->service_title = $request->service_title;
        $setting->job_title = $request->job_title;
        $setting->about_us_description = $request->about_us_description;
        $setting->contact_website = $request->contact_website;
        $setting->contact_phone = $request->contact_phone;
        $setting->contact_email = $request->contact_email;
        $setting->contact_address = $request->contact_address;
        $setting->show_job = $request->show_job;
        $setting->show_service = $request->show_service;
        $setting->show_about = $request->show_about;
        $setting->show_contact = $request->show_contact;
        $setting->show_counter = $request->show_counter;
        $setting->footer_text = $request->footer_text;
    
        // Save the settings
        $setting->save();
    
        return redirect()->route('company.setting')->with('success', 'Settings updated successfully');
    }

}
