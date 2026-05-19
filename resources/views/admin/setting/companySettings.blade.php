@extends('admin.master')


    @section('title')
       Company Settings
    @endsection
@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-md-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>@lang('dashboard.dashboard')</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <form method="POST" action="{{ route('company.setting.post') }}" class="form-horizontal" enctype="multipart/form-data">
@csrf
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-offset-2 col-md-6">
                                        @if($errors->any())
                                            <div class="alert alert-danger alert-dismissible" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                @foreach($errors->all() as $error)
                                                    <strong>{!! $error !!}</strong><br>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if(session()->has('success'))
                                            <div class="alert alert-success alert-dismissable">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                            </div>
                                        @endif
                                        @if(session()->has('error'))
                                            <div class="alert alert-danger alert-dismissable">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                                <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="row">

                                    <div class="col-md-4 mt-1" style="margin-top:10px;">
                                        <label class="control-label">Legal Name<span class="validateRq">*</span></label>
                                        <input type="text" class="form-control" name="legal_Name" value="{!! $setting ? $setting->legal_Name : '' !!}" placeholder="{{ __('front.company_name') }}" required>
                                    </div>
                                    <div class="col-md-4 mt-1" style="margin-top:10px;">
                                        <label class="control-label">Official Phone<span class="validateRq">*</span></label>
                                        <input type="text" class="form-control" name="official_contact_number" value="{!! $setting ? $setting->official_contact_number : '' !!}" placeholder="{{ __('front.company_name') }}" required>
                                    </div>
                                    

								</div>
                                <div class="row">

                                    <div class="col-md-4 mt-1" style="margin-top:10px;">
                                        <label class="control-label">Legal Address<span class="validateRq">*</span></label>

                                        <input type="text" class="form-control" name="legal_Address"  value="{{ $setting->legal_Address ?? '' }}">
                                    </div>

                                    <div class="col-md-3 mt-1" style="margin-top:10px;">
                                        <label class="control-label">Official Email<span class="validateRq">*</span></label>
                                        <input type="text" class="form-control" name="official_email"  value="{{ $setting->official_email ?? '' }}">
                                    </div>
                                    <div class="col-md-3 mt-1" style="margin-top:10px;">
                                        <label class="control-label">Contact Person<span class="validateRq">*</span></label>
                                        <input type="text" class="form-control" name="company_contact_name"  value="{{ $setting->company_contact_name ?? '' }}">
                                    </div>
                                    <div class="col-md-3 mt-1" style="margin-top:10px;">
                                        <label class="control-label">Contact Phone<span class="validateRq">*</span></label>
                                        <input type="text" class="form-control" name="representative_phone"  value="{{ $setting->representative_phone ?? '' }}">
                                    </div>
                                    <div class="col-md-3 mt-1" style="margin-top:10px;">
                                        <label class="control-label">Contact Email<span class="validateRq">*</span></label>
                                        <input type="text" class="form-control" name="representative_email"  value="{{ $setting->representative_email ?? '' }}">
                                    </div>
                                                   
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mt-1" style="margin-top:10px;">
                                    <label class="control-label">KRA PIN<span class="validateRq">*</span></label>
                                    <input type="text" class="form-control" name="KRA_PIN"  value="{{ $setting->KRA_PIN ?? '' }}">
                                </div>
                                <div class="col-md-3 mt-1" style="margin-top:10px;">
                                    <label class="control-label">Employer Number<span class="validateRq">*</span></label>
                                    <input type="text" class="form-control" name="employer_number"  value="{{ $setting->employer_number ?? '' }}">
                                </div>
                                <div class="col-md-3 mt-1" style="margin-top:10px;">
                                    <label class="control-label">NSSF Employer Number<span class="validateRq">*</span></label>
                                    <input type="text" class="form-control" name="NSSF_employer_number"  value="{{ $setting->NSSF_employer_number ?? '' }}">
                                </div>
                               
                            </div>
                            <div class="row">
                                <div class="col-md-3 mt-1" style="margin-top:10px;">
                                    <label class="control-label">Financial Year Start<span class="validateRq">*</span></label>
                                    <input type="date" class="form-control" name="financial_year_start"  value="{{ $setting->financial_year_start ?? '' }}">
                                </div>
                                <div class="col-md-3 mt-1" style="margin-top:10px;">
                                    <label class="control-label">SHIF_employer_code<span class="validateRq">*</span></label>
                                    <input type="text" class="form-control" name="NHIF_employer_code"  value="{{ $setting->NHIF_employer_code ?? '' }}">
                                </div>
                                <div class="col-md-3 mt-1" style="margin-top:10px;">
                                    <label class="control-label">Payroll Period Start<span class="validateRq">*</span></label>
                                    <input type="date" class="form-control" name="pyroll_period_start"  value="{{ $setting->pyroll_period_start ?? '' }}">
                                </div>
                                
                            </div>
                        
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-8" style="margin-top:20px;">
                                                <button type="submit" class="btn btn-info btn_style"><i class="fa fa-check"></i>@lang('common.save')</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
