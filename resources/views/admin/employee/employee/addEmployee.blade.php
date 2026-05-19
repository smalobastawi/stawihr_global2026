@extends('admin.master')

@section('title', trans('employee.add_employee'))

@section('content')
    <style>
        .appendBtnColor {
            color: #fff;
            font-weight: 700;
        }
    </style>

    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                            @lang('dashboard.dashboard')</a></li>
                    <li>@yield('title')</li>

                </ol>
            </div>
            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                <a href="{{ route('employee.index') }}"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                        class="fa fa-list-ul" aria-hidden="true"></i> @lang('employee.view_employee')
                </a>
            </div>
        </div>
        <!--/.row -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-clipboard-text fa-fw"></i> @yield('title')
                    </div>

                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                            aria-hidden="true">×</span></button>
                                    @foreach ($errors->all() as $error)
                                        <strong>{!! $error !!}</strong><br>
                                    @endforeach
                                </div>
                            @endif
                            @if (session()->has('success'))
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                    </button>
                                    <i
                                        class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                </div>
                            @endif
                            @if (session()->has('error'))
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                    </button>
                                    <i
                                        class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('employee.store') }}" enctype="multipart/form-data" id="employeeForm">
                            @csrf
                            <div class="form-body">
                                <h3 class="box-title">@lang('employee.employee_account')</h3>
                                <hr>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.role')<span
                                                    class="validateRq">*</span></label>
                                            <select class="roleSelect" multiple name="roles[]" style="width: 200px">
                                                @foreach ($roleList as $value)
                                                    <option value="{{ $value->id }}">
                                                        {{ $value->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!--/.col -->

                                    @if (config('app.password_login'))
                                        <div class="col-md-3">
                                            <label for="exampleInput">@lang('employee.user_name')<span
                                                    class="validateRq">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="ti-user"></i></div>
                                                <input class="form-control required user_name" required id="user_name"
                                                    placeholder="@lang('employee.user_name')" name="user_name" type="text"
                                                    value="{{ old('user_name') }}">
                                            </div>
                                        </div>
                                        <!--/.col -->

                                        <div class="col-md-3">
                                            <label for="password">@lang('employee.password')<span class="validateRq">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="ti-lock"></i></div>
                                                <input class="form-control required password" required id="password"
                                                    placeholder="@lang('employee.password')" name="password" type="password">
                                            </div>
                                        </div>
                                        <!--/.col -->

                                        <div class="col-md-3">
                                            <label for="password_confirmation">@lang('employee.confirm_password')<span
                                                    class="validateRq">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="ti-lock"></i></div>
                                                <input class="form-control required password_confirmation" required
                                                    id="password_confirmation" placeholder="@lang('employee.confirm_password')"
                                                    name="password_confirmation" type="password">
                                            </div>
                                        </div>
                                        <!--/.col -->
                                    @endif
                                </div>
                                <!--/.row -->
                                <h3 class="box-title">@lang('employee.personal_information')</h3>
                                <hr>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.KRA_Pin') <span
                                                    class="validateRq">*</span></label>
                                            <input class="form-control required KRA_Pin" id="KRA_Pin"
                                                placeholder="@lang('employee.KRA_Pin')" name="KRA_Pin" type="text"
                                                value="{{ old('KRA_Pin') }}" required>
                                        </div>
                                    </div>
                                    <!--/.col -->

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.NSSF_no') <span
                                                    class="validateRq">*</span></label>
                                            <input class="form-control NSSF_no" id="NSSF_no"
                                                placeholder="@lang('employee.NSSF_no')" name="NSSF_no" type="text"
                                                value="{{ old('NSSF_no') }}" required>
                                        </div>
                                    </div>
                                    <!--/.col -->

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.SHIF_no') <span class="validateRq">*</span>
                                            </label>
                                            <input class="form-control number SHIF_no" id="SHIF_no"
                                                placeholder="@lang('employee.SHIF_no')" name="shif_number" type="text"
                                                value="{{ old('shif_number') }}" required>
                                        </div>
                                    </div>
                                    <!--/.col -->

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.payroll_number')
                                            </label>
                                            <input class="form-control number payroll_number" id="payroll_number"
                                                placeholder="@lang('employee.payroll_number')" name="payroll_number" type="text"
                                                value="{{ old('payroll_number', $nextPayrollNumber ?? '') }}" readonly>
                                            <small class="text-muted">Auto-generated based on last number</small>
                                        </div>
                                    </div>
                                    <!--/.col -->

                                </div>
                                <!--/.row-->
                                <hr>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.first_name')<span
                                                    class="validateRq">*</span></label>
                                            <input class="form-control required first_name" required id="first_name"
                                                placeholder="@lang('employee.first_name')" name="first_name" type="text"
                                                value="{{ old('first_name') }}">
                                        </div>
                                    </div>
                                    <!--/.col -->

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">
                                                @lang('employee.middle_name')
                                            </label>
                                            <input class="form-control middle_name" id="middle_name"
                                                placeholder="Middle name" name="middle_name" type="text"
                                                value="{{ old('middle_name') }}">
                                        </div>
                                    </div>
                                    <!--/.col -->

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.last_name')</label>
                                            <input class="form-control last_name" id="last_name"
                                                placeholder="@lang('employee.last_name')" name="last_name" type="text"
                                                value="{{ old('last_name') }}">
                                        </div>
                                    </div>
                                    <!--/.col -->

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="identity_type">Identity Type<span
                                                    class="validateRq">*</span></label>
                                            <select name="identity_type" id="identity_type" class="form-control select2"
                                                required>
                                                <option value="">--- Select Identity Type ---</option>
                                                @foreach (\App\Lib\Enumerations\IdentityType::toArray() as $key => $value)
                                                    <option value="{{ $key }}"
                                                        {{ old('identity_type') == $key ? 'selected' : '' }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">ID/Passport No<span
                                                    class="validateRq">*</span></label>
                                            <input class="form-control number national_id" id="national_id"
                                                placeholder="@lang('employee.finger_print_no')" required name="national_id"
                                                type="text" value="{{ old('national_id') }}">
                                        </div>
                                    </div>
                                    <!--/.col -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">Driving License No</label>
                                            <input class="form-control driving_license_number" id="driving_license_number"
                                                placeholder="Enter Driving License Number" name="driving_license_number"
                                                type="text" value="{{ old('driving_license_number') }}">
                                        </div>
                                    </div>
                                    <!--/.col -->
                                </div>
                                <!--/.row -->

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="company_id">@lang('employee.company')<span class="validateRq">*</span></label>
                                            <select name="company_id" id="company_id" class="form-control select2" required>
                                                <option value="">--- @lang('common.please_select') ---</option>
                                                @foreach ($companyList as $company)
                                                    <option value="{{ $company->id }}"
                                                        {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                                        {{ $company->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!--/.col -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('department.department_name')<span
                                                    class="validateRq">*</span></label>
                                            <select name="department_id"
                                                class="departmentSelect form-control department_id  select2" required>
                                                <option value=""> @lang('common.please_select') </option>
                                                @foreach ($departmentList as $value)
                                                    <option value="{{ $value->department_id }}"
                                                        @if ($value->department_id == old('department_id')) {{ 'selected' }} @endif>
                                                        {{ $value->department_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!--/.col -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('section.section_name')</label>
                                            <select name="employee_section_id"
                                                class="sectionSelect form-control employee_section_id select2">
                                                <option value=""> @lang('common.please_select') </option>
                                                @foreach ($sectionList as $value)
                                                    <option value="{{ $value->id }}"
                                                        @if ($value->id == old('employee_section_id')) {{ 'selected' }} @endif>
                                                        {{ $value->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!--/.col -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">
                                                @lang('employee.supervisor')<span class="validateRq">*</span>
                                            </label>
                                            <select name="supervisor_id"
                                                class=" supervisorSelect form-control supervisor_id select2" required>
                                                <option value=""> @lang('common.please_select') </option>
                                                @foreach ($supervisorList as $value)
                                                    <option value="{{ $value->employee_id }}"
                                                        @if ($value->employee_id == old('employee_id')) {{ 'selected' }} @endif>
                                                        {{ $value->first_name }} {{ $value->last_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!--/.col -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('designation.designation_name')<span
                                                    class="validateRq">*</span></label>
                                            <select name="designation_id"
                                                class="designationSelect form-control department_id select2" required>
                                                <option value=""> @lang('common.please_select') </option>
                                                @foreach ($designationList as $value)
                                                    <option value="{{ $value->designation_id }}"
                                                        @if ($value->designation_id == old('designation_id')) {{ 'selected' }} @endif>
                                                        {{ $value->designation_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!--/.col -->
                                </div>
                                <!--/.row -->

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">Location</label>
                                            <select name="location_id" class="form-control location_id select2">
                                                <option value=""> @lang('common.please_select') </option>
                                                @foreach ($locationList as $value)
                                                    <option value="{{ $value->location_id }}"
                                                        @if ($value->location_id == old('location_id')) {{ 'selected' }} @endif>
                                                        {{ $value->location_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!--/.col -->
                                </div>
                                <!--/.row -->

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('work_shift.work_shift_name')<span
                                                    class="validateRq">*</span></label>
                                            <select name="work_shift_id" class="form-control work_shift_id  select2"
                                                required>
                                                <option value="">--- @lang('common.please_select') ---</option>
                                                @foreach ($workShiftList as $value)
                                                    <option value="{{ $value->work_shift_id }}">{{ $value->shift_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!--/.col -->

                                    <div class="col-md-3">
                                        <label for="exampleInput">@lang('employee.email') <span
                                                class="validateRq">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                            <input class="form-control email" id="email"
                                                placeholder="@lang('employee.email')" name="email" type="email"
                                                value="{{ old('email') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="exampleInput">@lang('employee.personal_email') <span
                                                class="validateRq">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                            <input class="form-control email" id="personal_email"
                                                placeholder="@lang('employee.personal_email')" name="personal_email" type="email"
                                                value="{{ old('personal_email') }}" required>
                                        </div>
                                    </div>
                                    <!--/.col -->

                                    <div class="col-md-3">
                                        <label for="exampleInput">@lang('employee.phone') <span
                                                class="validateRq">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                            <input class="form-control number phone" id="phone"
                                                placeholder="@lang('employee.phone')" name="phone" type="number"
                                                value="{{ old('phone') }}" required>
                                        </div>
                                    </div>
                                    <!--/.col -->

                                </div>
                                <!--/.row -->

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.gender')<span
                                                    class="validateRq">*</span></label>
                                            <select required class="genderSelect form-control select2" name="gender">
                                                <option selected="selected" disabled="disabled">Select Gender</option>
                                                @foreach (\Gender::toArray() as $key => $value)
                                                    @if ($value !== 'ALL')
                                                        <option value="{{ $value }}"
                                                            @isset($data->user->gender)
                            @if ($key == $data->user->gender)
                                selected="selected"
                            @endif
                        @endisset>
                                                            {{ $value }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!--/.col -->

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.religion')</label>
                                            <input class="form-control religion" id="religion"
                                                placeholder="@lang('employee.religion')" name="religion" type="text"
                                                value="{{ old('religion') }}">
                                        </div>
                                    </div>
                                    <!--/.col-->

                                    <div class="col-md-3">
                                        <label for="exampleInput">@lang('employee.date_of_birth')<span
                                                class="validateRq">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input class="form-control date_of_birth dateField" required
                                                id="date_of_birth" placeholder="DD/MM/YYYY" name="date_of_birth"
                                                type="text" value="{{ old('date_of_birth') }}">
                                        </div>
                                    </div>
                                    <!--/.col-->

                                    <div class="col-md-3">
                                        <label for="exampleInput">@lang('employee.date_of_joining')<span
                                                class="validateRq">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input class="form-control date_of_joining dateField" required
                                                id="date_of_joining" placeholder="DD/MM/YYYY" name="date_of_joining"
                                                type="text" value="{{ old('date_of_joining') }}">
                                        </div>
                                    </div>
                                    <!--/.col -->

                                </div>
                                <!--/.row -->

                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="exampleInput">@lang('employee.date_of_leaving')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input class="form-control  date_of_leaving dateField" readonly
                                                id="date_of_leaving" placeholder="@lang('employee.date_of_leaving')"
                                                name="date_of_leaving" type="text"
                                                value="{{ old('date_of_leaving') }}">
                                        </div>
                                    </div>
                                    <!--/.col -->

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.marital_status')</label>
                                            <select name="marital_status" class="form-control status required select2">
                                                <option value=""> @lang('common.please_select') </option>
                                                <option value="Unmarried"
                                                    @if ('Unmarried' == old('marital_status')) {{ 'selected' }} @endif>
                                                    @lang('employee.unmarried')
                                                </option>
                                                <option value="Married"
                                                    @if ('Married' == old('marital_status')) {{ 'selected' }} @endif>
                                                    Married
                                                </option>
                                                <option value="Single"
                                                    @if ('Single' == old('marital_status')) {{ 'selected' }} @endif>
                                                    Single
                                                </option>
                                                <option value="Divorced"
                                                    @if ('Divorced' == old('marital_status')) {{ 'selected' }} @endif>
                                                    Divorced
                                                </option>
                                                <option value="Separated"
                                                    @if ('Separated' == old('marital_status')) {{ 'selected' }} @endif>
                                                    Separated</option>
                                                <option value="Separated"
                                                    @if ('Widowed' == old('marital_status')) {{ 'selected' }} @endif>
                                                    Widowed
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('common.status')<span
                                                    class="validateRq">*</span></label>
                                            <select name="status" class="form-control status select2" required>
                                                <option value="1"
                                                    @if ('1' == old('status')) {{ 'selected' }} @endif>
                                                    @lang('common.active')</option>
                                                <option value="2"
                                                    @if ('2' == old('status')) {{ 'selected' }} @endif>
                                                    @lang('common.inactive')</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!--/.col -->


                                    <div class="col-md-3">
                                        <label for="exampleInput">@lang('employee.photo')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="	fa fa-picture-o"></i></span>
                                            <input class="form-control photo" id="photo"
                                                accept="image/png, image/jpeg, image/gif,image/jpg" name="photo"
                                                type="file">
                                        </div>
                                    </div>
                                    <!--/.col -->

                                </div>
                                <!--/.row -->

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="emergency_name">@lang('employee.emergency_name')</label>
                                            <input class="form-control" id="emergency_name"
                                                placeholder="@lang('employee.emergency_name')" name="emergency_name" type="text">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="emergency_phone">@lang('employee.emergency_phone')</label>
                                            <input class="form-control" id="emergency_phone"
                                                placeholder="@lang('employee.emergency_phone')" name="emergency_phone" type="text">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="emergency_relationship">@lang('employee.emergency_relationship')</label>
                                            <select required class="residencySelect form-control select2"
                                                name="emergency_relationship">
                                                <option selected="selected" disabled="disabled">Select Relationship
                                                </option>
                                                @foreach (\EmergencyContactRelationship::toArray() as $key => $value)
                                                    <option value="{{ $key }}">
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!--/.row -->

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">
                                                Leave Group<span class="validateRq">*</span>
                                            </label>
                                            <select name="leave_group_id" class="form-control employeeGroupId select2"
                                                required>
                                                <option value=""> @lang('common.please_select') </option>
                                                @foreach ($leaveGroupList as $value)
                                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!--/.col -->

                                 <div class="col-md-3">
    <div class="form-group">
        <label for="employmentType">Employment Type</label>
        <select name="employment_type" id="employmentType" class="form-control employment_type select2 required">
            <option value="Permanent">Permanent</option>
            <option value="Contract">Contract</option>
            <option value="Seasonal">Seasonal</option>
            <option value="Internship">Internship</option>
            <option value="Part Time">Part Time</option>
        </select>
    </div>
</div>
                                    <!--/.col -->

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">
                                                Residency Status <span class="validateRq">*</span></label>
                                            <select class="residencySelect form-control select2" name="residential_status"
                                                required>
                                                <option selected="selected" value="" disabled="disabled">Select
                                                    Residency Status
                                                </option>
                                                @foreach (\ResidencyStatus::toArray() as $key => $value)
                                                    <option value="{{ $key }}"
                                                        @isset($data->residential_status)
                                                @if ($key == $data->residential_status)
                                                selected="selected"
                                                @endif
                                                @endisset>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!--/.col -->
                                </div>
                                <!--/.row -->

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInput">NSSF Rate Group</label>
                                            <input type="hidden" name="nssf_rate_type" value="2">
                                            <input class="form-control" type="text" value="Tier 1 & 2" readonly>
                                            <small class="text-muted">Default: Tier 1 & 2 (Not editable)</small>

                                            </select>
                                        </div>
                                    </div>
                                    <!--/.col -->

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInput">
                                                @lang('employee.nationality')<span class="validateRq">*</span>
                                            </label>
                                            <select class="nationalitySelect form-control select2" name="nationality"
                                                required>
                                                <option selected="selected" value="" disabled="disabled">Select
                                                    Nationality
                                                </option>
                                                @foreach (\Nationality::toArray() as $key => $value)
                                                    <option value="{{ $value }}"
                                                        @isset($data->nationality)
                                                @if ($key == $data->nationality)
                                                selected="selected"
                                                @endif
                                                @endisset>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!--/.col -->
                                </div>
                                <!--/.row -->

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.address')</label>
                                            <textarea class="form-control address" id="address" placeholder="@lang('employee.address')" cols="30"
                                                rows="2" name="address">{{ old('address') }}</textarea>
                                        </div>
                                    </div>
                                    <!--/.col -->
                                </div>
                                <!--/.row -->
                                <br>
                                <!-- employee documents here -->
                                <h3 class="box-title">Employee Documents</h3>
                                <hr>
                                <div class="employee_document_append_div"></div>
                                <div class="row">
                                    <div class="col-md-9"></div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input id="addEmployeeDocuments" type="button"
                                                class="form-control btn btn-success appendBtnColor" value="Add Documents">
                                        </div>
                                    </div>
                                </div>
                                <!-- end of employee documents -->
                                <h3 class="box-title">@lang('employee.educational_qualification')</h3>
                                <hr>
                                <div class="education_qualification_append_div"></div>
                                <div class="row">
                                    <div class="col-md-9"></div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input id="addEducationQualification" type="button"
                                                class="form-control btn btn-success appendBtnColor"
                                                value="@lang('employee.add_educational_qualification')">
                                        </div>
                                    </div>
                                </div>
                                <h3 class="box-title">@lang('employee.professional_experience')</h3>
                                <hr>
                                <div class="experience_append_div"></div>
                                <div class="row">
                                    <div class="col-md-9"></div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input id="addExperience" type="button"
                                                class="form-control btn btn-success appendBtnColor"
                                                value="@lang('employee.add_professional_experience')">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-12 ">
                                            <button type="submit" class="btn btn-info btn_style"><i
                                                    class="fa fa-check"></i>
                                                @lang('common.save')</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!--/.form-body-->
                            </form>


                        </div>
                        <!--/.panel-body -->
                    </div>
                    <!--/.panel-wrapper -->


                </div>
                <!--/.panel -->
            </div>
            <!--/.col -->
        </div>
        <!--/.row -->
        <div class="row_element1" style="display: none;">
            <input name="educationQualification_cid[]" type="hidden">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="exampleInput">@lang('employee.institute')<span class="validateRq">*</span></label>
                        <select name="institute[]" class="form-control institute">
                            <option value="">--- @lang('common.please_select') ---</option>
                            <option value="Board">@lang('employee.board')</option>
                            <option value="University">@lang('employee.university')</option>
                            <option value="Secondary">@lang('employee.secondary')</option>
                            <option value="Primary">@lang('employee.primary')</option>
                            <option value="Tertiary">@lang('employee.tertiary')</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="exampleInput">
                            @lang('employee.name_institution')<span class="validateRq">*</span>
                        </label>
                        <input type="text" name="board_university[]" class="form-control board_university"
                            id="board_university" placeholder="@lang('employee.name_institution')">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="exampleInput">@lang('employee.degree')<span class="validateRq">*</span></label>
                        <input type="text" name="degree[]" class="form-control degree required" id="degree"
                            placeholder="Example: B.Sc. Engr.(Bachelor of Science in Engineering)">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="exampleInput">@lang('employee.passing_year')<span class="validateRq">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar-o"></i></span>
                        <input type="text" name="passing_year[]" class="form-control yearPicker required"
                            id="passing_year" placeholder="@lang('employee.passing_year')">
                    </div>
                </div>
            </div>
            <div class="row">
                {{-- <div class="col-md-3">
                <div class="form-group">
                    <label for="exampleInput">@lang('employee.result')</label>
                    <select name="result[]" class="form-control result">
                        <option value=""> @lang('common.please_select') </option>
                        <option value="First class">First class</option>
                        <option value="Second class">Second class</option>
                        <option value="Third class">Third class</option>
                    </select>
                </div>
            </div> --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="exampleInput">@lang('employee.gpa') / @lang('employee.cgpa')</label>
                        <input type="text" name="cgpa[]" class="form-control cgpa" id="cgpa"
                            placeholder="Example: 5.00,4.63">
                    </div>
                </div>
                <div class="col-md-3"></div>
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="button"
                            class="form-control btn btn-danger deleteEducationQualification appendBtnColor"
                            style="margin-top: 17px" value="@lang('common.delete')">
                    </div>
                </div>
            </div>
            <hr>
        </div>

        <div class="row_element2" style="display: none;">
            <input name="employeeExperience_cid[]" type="hidden">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="exampleInput">@lang('employee.organization_name')<span class="validateRq">*</span></label>
                        <input type="text" name="organization_name[]" class="form-control organization_name"
                            id="organization_name" placeholder="@lang('employee.organization_name')">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="exampleInput">@lang('employee.designation')<span class="validateRq">*</span></label>
                        <input type="text" name="designation[]" class="form-control designation" id="designation"
                            placeholder="@lang('employee.designation')">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="exampleInput">@lang('common.from_date')<span class="validateRq">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" name="from_date[]" class="form-control dateField" id="from_date"
                            placeholder="@lang('common.from_date')">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="exampleInput">@lang('common.to_date')<span class="validateRq">*</span></label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" name="to_date[]" class="form-control dateField" id="to_date"
                            placeholder="@lang('common.to_date')">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="exampleInput">@lang('employee.responsibility')<span class="validateRq">*</span></label>
                        <textarea name="responsibility[]" class="form-control responsibility" placeholder="@lang('employee.responsibility')"
                            cols="30" rows="2"></textarea>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="exampleInput">@lang('employee.skill')<span class="validateRq">*</span></label>
                        <textarea name="skill[]" class="form-control skill" placeholder="@lang('employee.skill')" cols="30" rows="2"></textarea>
                    </div>
                </div>
                <div class="col-md-3"></div>
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="button" class="form-control btn btn-danger deleteExperience appendBtnColor"
                            style="margin-top: 17px" value="@lang('common.delete')">
                    </div>
                </div>
            </div>
            <hr>
        </div>

        <!-- employee documents start here -->
        <div class="employee_docs_row_element1" style="display: none;">
            <input name="employeeDocuments_cid[]" type="hidden">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="exampleInput">Document Name<span class="validateRq">*</span></label>
                        <input type="text" name="document_name[]" class="form-control responsibility"
                            placeholder="e.g KRA PIN" cols="30" rows="2" required></input>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="exampleInput">Type<span class="validateRq">*</span></label>
                        <select name="document_type[]" class="form-control type" cols="30" rows="2" required>
                            <option value="personal">Personal</option>
                            <option value="official">Official</option>
                        </select>
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="form-group">
                        <label for="exampleInput">Upload file<span class="validateRq">*</span></label>
                        <input type="file" name="document_file[]" class="form-control responsibility"
                            placeholder="document_name" cols="30" rows="2" required accept="application/pdf">
                    </div>
                </div>
                <div class="col-md-3"></div>
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="button" class="form-control btn btn-danger deleteEmployeeDocuments appendBtnColor"
                            style="margin-top: 17px" value="@lang('common.delete')">
                    </div>
                </div>
            </div>
            <hr>
        </div>

        <!-- end of employee documents -->

    </div>
    <!--/.container-fluid -->
@endsection

@section('page_scripts')
    <script>
        $(document).ready(function() {
            $('#addEducationQualification').click(function() {
                $('.education_qualification_append_div').append(
                    '<div class="education_qualification_row_element">' + $('.row_element1').html() +
                    '</div>');
            });

            $('#addExperience').click(function() {
                $('.experience_append_div').append('<div class="experience_row_element">' + $(
                    '.row_element2').html() + '</div>');
            });

            $(document).on("click", ".deleteEducationQualification", function() {
                $(this).parents('.education_qualification_row_element').remove();
                var deletedID = $(this).parents('.education_qualification_row_element').find(
                    '.educationQualification_cid').val();
                if (deletedID) {
                    var prevDelId = $('#delete_education_qualifications_cid').val();
                    if (prevDelId) {
                        $('#delete_education_qualifications_cid').val(prevDelId + ',' + deletedID);
                    } else {
                        $('#delete_education_qualifications_cid').val(deletedID);
                    }
                }
            });

            $(document).on("click", ".deleteExperience", function() {
                $(this).parents('.experience_row_element').remove();
                var deletedID = $(this).parents('.experience_row_element').find('.employee_experience_id')
                    .val();
                if (deletedID) {
                    var prevDelId = $('#delete_experiences_cid').val();
                    if (prevDelId) {
                        $('#delete_experiences_cid').val(prevDelId + ',' + deletedID);
                    } else {
                        $('#delete_experiences_cid').val(deletedID);
                    }
                }
            });

            $('#addEmployeeDocuments').click(function() {
                $('.employee_document_append_div').append('<div class="employee_documents_row_element">' +
                    $('.employee_docs_row_element1').html() + '</div>');
            });

            $(document).on("click", ".deleteEmployeeDocuments", function() {
                $(this).parents('.employee_documents_row_element').remove();
                var deletedID = $(this).parents('.employee_documents_row_element').find(
                    '.employeeDocuments_cid').val();
                if (deletedID) {
                    var prevDelId = $('#delete_employee_documents_cid').val();
                    if (prevDelId) {
                        $('#delete_employee_documents_cid').val(prevDelId + ',' + deletedID);
                    } else {
                        $('#delete_employee_documents_cid').val(deletedID);
                    }
                }
            });
        });
    </script>
    <script>
        $('.work_shift_id').select2({
            placeholder: 'Search shift',
        });
        $('.departmentSelect').select2({
            placeholder: 'Search department',
        });
        $('.sectionSelect').select2({
            placeholder: 'Search section',
        });
        $('.supervisorSelect').select2({
            placeholder: 'Search supervisor',
        });
        $('.designationSelect').select2({
            placeholder: 'Search designation',
        });
        $('.nationalitySelect').select2({
            placeholder: 'Search designation',
        });
        $('.residencySelect').select2({
            placeholder: 'Search designation',
        });
        $('.genderSelect').select2({
            placeholder: 'Search designation',
        });
        $('.location_id, .employeeSectionId, .employeeGroupId, .employment_type, .roleSelect, .employmentType')
            .select2({
                placeholder: 'click to Search',
            });
    </script>
    <script>
        $(document).ready(function() {
            // Function to convert KRA_Pin to uppercase on blur
            $('#KRA_Pin').on('blur', function() {
                $(this).val($(this).val().toUpperCase());
            });
        });
    </script>
@endsection
