@extends('admin.master')

@section('title')
    Edit Company
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-md-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i
                                class="fa fa-home"></i>@lang('dashboard.dashboard')</a></li>
                    <li><a href="{{ route('company.index') }}">Companies</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-pencil fa-fw"></i>@yield('title')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <form method="POST" action="{{ route('company.update', $company) }}" class="form-horizontal" enctype="multipart/form-data">
@csrf
@method('PUT')
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-offset-2 col-md-6">
                                        @if ($errors->any())
                                            <div class="alert alert-danger alert-dismissible" role="alert">
                                                <button type="button" class="close" data-dismiss="alert"
                                                    aria-label="Close"><span aria-hidden="true">×</span></button>
                                                @foreach ($errors->all() as $error)
                                                    <strong>{!! $error !!}</strong><br>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if (session()->has('success'))
                                            <div class="alert alert-success alert-dismissable">
                                                <button type="button" class="close" data-dismiss="alert"
                                                    aria-hidden="true">×</button>
                                                <i
                                                    class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="control-label">Company Name<span class="validateRq">*</span></label>
                                        <input type="text" class="form-control" name="name"
                                            placeholder="Enter company name" value="{{ old('name', $company->name) }}"
                                            required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label">Domain<span class="validateRq">*</span></label>
                                        <input type="text" class="form-control" name="domain" placeholder="Enter domain"
                                            value="{{ old('domain', $company->domain) }}" required>
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-md-6">
                                        <label class="control-label">Payroll Country<span class="validateRq">*</span></label>
                                        <select class="form-control" name="payroll_country" required>
                                            <option value="">Select payroll country</option>
                                            @foreach ($payrollCountries as $id => $label)
                                                <option value="{{ $id }}"
                                                    {{ (int) old('payroll_country', $company->payroll_country ?? \App\Lib\Enumerations\PayrollCountry::KENYA) === (int) $id ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Determines which statutory PAYE and deduction rules apply during payroll processing.</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label">Status<span class="validateRq">*</span></label>
                                        <select class="form-control" name="status" required>
                                            <option value="">Select Status</option>
                                            <option value="active"
                                                {{ old('status', $company->status) == 'active' ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="inactive"
                                                {{ old('status', $company->status) == 'inactive' ? 'selected' : '' }}>
                                                Inactive</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-md-6">
                                        <label class="control-label">Company Logo</label>
                                        @if($company->logo)
                                            <div style="margin-bottom: 10px;">
                                                <img src="{{ companyLogoUrl($company) }}" alt="{{ $company->name }} logo"
                                                    style="max-height: 80px; max-width: 200px; object-fit: contain; border: 1px solid #eee; padding: 5px;">
                                            </div>
                                        @endif
                                        <input type="file" class="form-control" name="logo" accept="image/*">
                                        <small class="text-muted">Upload a logo for payslips and reports (PNG, JPG, GIF, WEBP, SVG — max 2MB)</small>
                                    </div>
                                </div>

                                <h4 style="margin-top: 30px; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                                    <i class="mdi mdi-account-card-details fa-fw"></i> Kenya Government Employer Information
                                </h4>

                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-md-6">
                                        <label class="control-label">KRA PIN</label>
                                        <input type="text" class="form-control" name="kra_pin"
                                            placeholder="Enter KRA PIN (e.g., P011111111A)"
                                            value="{{ old('kra_pin', $company->kra_pin) }}">
                                        <small class="text-muted">Kenya Revenue Authority PIN</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label">Registration Number</label>
                                        <input type="text" class="form-control" name="registration_number"
                                            placeholder="Enter Company Registration Number"
                                            value="{{ old('registration_number', $company->registration_number) }}">
                                        <small class="text-muted">Registrar of Companies Registration Number</small>
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-md-6">
                                        <label class="control-label">NSSF Employer Number</label>
                                        <input type="text" class="form-control" name="nssf_employer_number"
                                            placeholder="Enter NSSF Employer Number"
                                            value="{{ old('nssf_employer_number', $company->nssf_employer_number) }}">
                                        <small class="text-muted">National Social Security Fund Employer Number</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label">SHIF Employer Code</label>
                                        <input type="text" class="form-control" name="shif_employer_code"
                                            placeholder="Enter SHIF Employer Code"
                                            value="{{ old('shif_employer_code', $company->shif_employer_code) }}">
                                        <small class="text-muted">Social Health Insurance Fund (formerly NHIF) Employer Code</small>
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-md-6">
                                        <label class="control-label">Employer Number</label>
                                        <input type="text" class="form-control" name="employer_number"
                                            placeholder="Enter Employer Number"
                                            value="{{ old('employer_number', $company->employer_number) }}">
                                        <small class="text-muted">General Employer Reference Number</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label">NITA Registration Number</label>
                                        <input type="text" class="form-control" name="nita_registration_number"
                                            placeholder="Enter NITA Registration Number"
                                            value="{{ old('nita_registration_number', $company->nita_registration_number) }}">
                                        <small class="text-muted">National Industrial Training Authority Registration</small>
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-md-6">
                                        <label class="control-label">eCitizen Identifier</label>
                                        <input type="text" class="form-control" name="ecitizen_identifier"
                                            placeholder="Enter eCitizen Identifier"
                                            value="{{ old('ecitizen_identifier', $company->ecitizen_identifier) }}">
                                        <small class="text-muted">Kenya eCitizen Portal Business Identifier</small>
                                    </div>
                                </div>

                                <h4 style="margin-top: 30px; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                                    <i class="mdi mdi-map-marker fa-fw"></i> Contact &amp; Correspondence
                                </h4>

                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-md-12">
                                        <label class="control-label">Registered Address</label>
                                        <textarea class="form-control" name="address" rows="3"
                                            placeholder="Enter company address">{{ old('address', $company->address) }}</textarea>
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-md-6">
                                        <label class="control-label">Official Phone</label>
                                        <input type="text" class="form-control" name="official_contact_number"
                                            value="{{ old('official_contact_number', $company->official_contact_number) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label">Official Email</label>
                                        <input type="email" class="form-control" name="official_email"
                                            value="{{ old('official_email', $company->official_email) }}">
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-md-6">
                                        <label class="control-label">Contact Person</label>
                                        <input type="text" class="form-control" name="company_contact_name"
                                            value="{{ old('company_contact_name', $company->company_contact_name) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label">Representative Phone</label>
                                        <input type="text" class="form-control" name="representative_phone"
                                            value="{{ old('representative_phone', $company->representative_phone) }}">
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-md-6">
                                        <label class="control-label">Representative Email</label>
                                        <input type="email" class="form-control" name="representative_email"
                                            value="{{ old('representative_email', $company->representative_email) }}">
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 20px;">
                                    <div class="col-md-12">
                                        <label class="control-label">Print Head (Payslips &amp; Reports)</label>
                                        <textarea class="form-control" name="print_head_description" rows="4"
                                            placeholder="HTML or text shown at the top of payslips and reports">{{ old('print_head_description', $company->print_head_description) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-8" style="margin-top:20px;">
                                                <button type="submit" class="btn btn-info btn_style"><i
                                                        class="fa fa-check"></i>@lang('common.save')</button>
                                                <a href="{{ route('company.index') }}"
                                                    class="btn btn-default">@lang('common.cancel')</a>
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
    </div>
@endsection
