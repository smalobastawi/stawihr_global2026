@extends('admin.master')

@section('title', getPageTitle() . ' | ' . config('app.name'))

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
                        class="fa fa-list-ul" aria-hidden="true"></i> Employee List
                </a>

                <a href="{{ route('employee.show', $editModeData->employee_id) }}"
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
                    <!--/.panel-heading -->
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
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <i
                                        class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                </div>
                            @endif
                            @if (session()->has('error'))
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <i
                                        class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                </div>
                            @endif
<form action="{{ route('employee.update', $editModeData->employee_id) }}" method="POST" enctype="multipart/form-data" id="employeeForm">
    @method('PUT')
    @csrf
                            <input class="form-control  delete_education_qualifications_cid"
                                id="delete_education_qualifications_cid" name="delete_education_qualifications_cid"
                                type="hidden" value="">
                            <input class="form-control  delete_experiences_cid" id="delete_experiences_cid"
                                name="delete_experiences_cid" type="hidden" value="">
                            <div class="form-body">
                                <h3 class="box-title">@lang('employee.employee_account')</h3>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInput">Roles<span class="validateRq">*</span></label>
                                            <select class="roleSelect" multiple name="roles[]" style="width: 200px"
                                                required>
                                                @foreach ($roleList as $value)
                                                    <option value="{{ $value->id }}"
                                                        {{ in_array($value->id, $userRoles) ? 'selected' : '' }}>
                                                        {{ $value->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div><!--/.col -->

                                    @if (config('app.password_login'))
                                        <div class="col-md-6">
                                            <label for="exampleInput">
                                                @lang('employee.user_name')<span class="validateRq">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="ti-user"></i></div>
                                                <input class="form-control required user_name" required id="user_name"
                                                    placeholder="@lang('employee.user_name')" name="user_name" type="text"
                                                    value="{{ $employeeAccountEditModeData->user_name }}">
                                            </div>
                                        </div><!--/.col -->
                                    @endif
                                </div><!--/.row -->
                                <h3 class="box-title">
                                    @lang('employee.personal_information')
                                </h3>
                                <hr>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.KRA_Pin')</label>
                                            <input class="form-control required KRA_Pin" id="KRA_Pin"
                                                placeholder="@lang('employee.KRA_Pin')" name="KRA_Pin" type="text"
                                                value="{{ $editModeData->KRA_Pin }}">
                                        </div>
                                    </div><!--/.col -->

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.NSSF_no')</label>
                                            <input class="form-control NSSF_no" id="NSSF_no"
                                                placeholder="@lang('employee.NSSF_no')" name="NSSF_no" type="text"
                                                value="{{ $editModeData->NSSF_no }}" required>
                                        </div>
                                    </div><!--/.col -->

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.SHIF_no')</label>
                                            <input class="form-control number SHIF_no" id="SHIF_no"
                                                placeholder="@lang('employee.SHIF_no')" name="shif_number" type="text"
                                                value="{{ $editModeData->shif_number }}" required>
                                        </div>
                                    </div><!--/.col -->

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.payroll_number')</label>
                                            <input class="form-control number payroll_number" id="payroll_number"
                                                placeholder="@lang('employee.payroll_number')" name="payroll_number" type="text"
                                                value="{{ $editModeData->payroll_number }}" readonly>
                                        </div>
                                    </div><!--/.col -->
                                </div><!--/.row -->

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.first_name')</label>
                                            <input class="form-control required first_name" id="first_name"
                                                placeholder="@lang('employee.first_name')" name="first_name" type="text"
                                                value="{{ $editModeData->first_name }}">
                                        </div>
                                    </div><!--/.col -->

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">
                                                @lang('employee.middle_name')
                                            </label>
                                            <input class="form-control middle_name" id="middle_name"
                                                placeholder="middle name" name="middle_name" type="text"
                                                value="{{ $editModeData->middle_name }}">
                                        </div>
                                    </div><!--/.col -->

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.last_name')</label>
                                            <input class="form-control last_name" id="last_name"
                                                placeholder="@lang('employee.last_name')" name="last_name" type="text"
                                                value="{{ $editModeData->last_name }}">
                                        </div>
                                    </div><!--/.col -->

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="identity_type">Identity Type<span
                                                    class="validateRq">*</span></label>
                                            <select name="identity_type" id="identity_type" class="form-control select2"
                                                required>
                                                <option value="">--- Select Identity Type ---</option>
                                                @foreach (\App\Lib\Enumerations\IdentityType::toArray() as $key => $value)
                                                    <option value="{{ $key }}"
                                                        {{ $editModeData->identity_type == $key ? 'selected' : '' }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.finger_print_no')<span
                                                    class="validateRq">*</span></label>
                                            <input class="form-control number national_id" id="national_id" required
                                                placeholder="@lang('employee.finger_print_no')" name="national_id" type="text"
                                                value="{{ $editModeData->national_id }}">
                                        </div>
                                    </div><!--/.col -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">Driving License No</label>
                                            <input class="form-control driving_license_number" id="driving_license_number"
                                                placeholder="Enter Driving License Number" name="driving_license_number"
                                                type="text" value="{{ $editModeData->driving_license_number }}">
                                        </div>
                                    </div><!--/.col -->
                                </div><!--/.row -->

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="company_id">@lang('employee.company')<span class="validateRq">*</span></label>
                                            <select name="company_id" id="company_id" class="form-control select2" required>
                                                <option value="">--- @lang('common.please_select') ---</option>
                                                @foreach ($companyList as $company)
                                                    <option value="{{ $company->id }}"
                                                        {{ $editModeData->company_id == $company->id ? 'selected' : '' }}>
                                                        {{ $company->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div><!--/.col -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('department.department_name')<span
                                                    class="validateRq">*</span></label>
                                            <select name="department_id" class="form-control department_id  select2"
                                                required>
                                                <option value="">--- @lang('common.please_select') ---</option>
                                                @foreach ($departmentList as $value)
                                                    <option value="{{ $value->department_id }}"
                                                        @if ($value->department_id == $editModeData->department_id) {{ 'selected' }} @endif>
                                                        {{ $value->department_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div><!--/.col -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('section.section_name')</label>
                                            <select name="employee_section_id" class="form-control employee_section_id select2">
                                                <option value="">--- @lang('common.please_select') ---</option>
                                                @foreach ($sectionList as $value)
                                                    <option value="{{ $value->id }}"
                                                        @if ($value->id == $editModeData->employee_section_id) {{ 'selected' }} @endif>
                                                        {{ $value->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div><!--/.col -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.supervisor')<span
                                                    class="validateRq">*</span></label>
                                            <select name="supervisor_id"
                                                class="form-control supervisor_id select2 required" required>
                                                <option value="">--- @lang('common.please_select') ---</option>
                                                @foreach ($supervisorList as $value)
                                                    <option value="{{ $value->employee_id }}"
                                                        @if ($value->employee_id == $editModeData->supervisor_id) {{ 'selected' }} @endif>
                                                        {{ $value->first_name }} {{ $value->last_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div><!--/.col -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('designation.designation_name')<span
                                                    class="validateRq">*</span></label>
                                            <select name="designation_id" class="form-control department_id select2">
                                                <option value="">--- @lang('common.please_select') ---</option>
                                                @foreach ($designationList as $value)
                                                    <option value="{{ $value->designation_id }}"
                                                        @if ($value->designation_id == $editModeData->designation_id) {{ 'selected' }} @endif>
                                                        {{ $value->designation_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div><!--/.col -->
                                </div><!--/.row -->

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('location.location_name')</label>
                                            <select name="location_id" class="form-control location_id select2">
                                                <option value="">--- @lang('common.please_select') ---</option>
                                                @foreach ($locationList as $value)
                                                    <option value="{{ $value->location_id }}"
                                                        @if ($value->location_id == $editModeData->location_id) {{ 'selected' }} @endif>
                                                        {{ $value->location_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div><!--/.col -->
                                </div><!--/.row -->

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('work_shift.work_shift_name')<span
                                                    class="validateRq">*</span></label>

                                            <select name="work_shift_id" class="form-control department_id  select2">
                                                <option value="">--- @lang('common.please_select') ---</option>
                                                @foreach ($workShiftList as $value)
                                                    <option value="{{ $value->work_shift_id }}"
                                                        @if ($value->work_shift_id == $editModeData->work_shift_id) {{ 'selected' }} @endif>
                                                        {{ $value->shift_name }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div><!--/.col -->

                                    <div class="col-md-3">
                                        <label for="exampleInput">@lang('employee.email')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                            <input class="form-control email" id="email"
                                                placeholder="@lang('employee.email')" name="email" type="email"
                                                value="{{ $editModeData->email }}">
                                        </div>
                                    </div><!--/.col -->
                                    <div class="col-md-3">
                                        <label for="exampleInput">@lang('employee.personal_email')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                            <input class="form-control email" id="personal_email"
                                                placeholder="@lang('employee.personal_email')" name="personal_email" type="email"
                                                value="{{ $editModeData->personal_email }}">
                                        </div>
                                    </div><!--/.col -->

                                    <div class="col-md-3">
                                        <label for="exampleInput">@lang('employee.phone')<span
                                                class="validateRq">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                            <input class="form-control number phone" id="phone"
                                                placeholder="@lang('employee.phone')" name="phone" type="number"
                                                value="{{ $editModeData->phone }}">
                                        </div>
                                    </div><!--/.col -->
                                </div><!--/.row -->

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.gender')<span
                                                    class="validateRq">*</span></label>
                                            <select class="genderSelect form-control select2" name="gender" required>
                                                <option selected="selected" disabled="disabled">Select Gender</option>
                                                @foreach (\Gender::toArray() as $key => $value)
                                                    <option value="{{ $value }}"
                                                        @isset($editModeData->gender)
																		@if ($value == $editModeData->gender)
																			selected="selected"
																		@endif
																	@endisset>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div><!--/.col -->

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.religion')</label>
                                            <input class="form-control religion" id="religion"
                                                placeholder="@lang('employee.religion')" name="religion" type="text"
                                                value="{{ $editModeData->religion }}">
                                        </div>
                                    </div><!--/.col -->

                                    <div class="col-md-3">
                                        <label for="exampleInput">@lang('employee.date_of_birth')<span
                                                class="validateRq">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input required class="form-control date_of_birth dateField"
                                                id="date_of_birth" placeholder="DD/MM/YYYY" name="date_of_birth"
                                                type="text"
                                                value="{{ dateConvertDBtoForm($editModeData->date_of_birth) }}">
                                        </div>
                                    </div><!--/.col -->

                                    <div class="col-md-3">
                                        <label for="exampleInput">
                                            @lang('employee.date_of_joining')<span class="validateRq">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input class="form-control date_of_joining dateField" id="date_of_joining"
                                                placeholder="DD/MM/YYYY" name="date_of_joining" type="text"
                                                value="{{ dateConvertDBtoForm($editModeData->date_of_joining) }}">
                                        </div>
                                    </div><!--/.col -->

                                </div><!--/.row-->

                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="exampleInput">
                                            @lang('employee.date_of_leaving')
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input class="form-control  date_of_leaving dateField" id="date_of_leaving"
                                                readonly placeholder="@lang('employee.date_of_leaving')" name="date_of_leaving"
                                                type="text"
                                                value="{{ dateConvertDBtoForm($editModeData->date_of_leaving) }}">
                                        </div>
                                    </div><!--/.col -->

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.marital_status')</label>
                                            <select name="marital_status" class="form-control status required select2">
                                                <option value=""> @lang('common.please_select') </option>
                                                <option value="Unmarried"
                                                    @if ('Unmarried' == old('marital_status')) {{ 'selected' }} @endif>
                                                    @lang('employee.unmarried')</option>
                                                <option value="Married"
                                                    @if ('Married' == old('marital_status')) {{ 'selected' }} @endif>
                                                    Married</option>
                                                <option value="Single"
                                                    @if ('Single' == old('marital_status')) {{ 'selected' }} @endif>
                                                    Single</option>
                                                <option value="Divorced"
                                                    @if ('Divorced' == old('marital_status')) {{ 'selected' }} @endif>
                                                    Divorced</option>
                                                <option value="Separated"
                                                    @if ('Separated' == old('marital_status')) {{ 'selected' }} @endif>
                                                    Separated</option>
                                                <option value="Separated"
                                                    @if ('Widowed' == old('marital_status')) {{ 'selected' }} @endif>
                                                    Widowed</option>
                                            </select>
                                        </div>
                                    </div><!--/.col -->

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">Status<span class="validateRq">*</span></label>
                                            <select name="status" class="form-control status select2">
                                                <option value="1"
                                                    @if ('1' == $editModeData->status) {{ 'selected' }} @endif>
                                                    @lang('common.active')</option>
                                                <option value="2"
                                                    @if ('2' == $editModeData->status) {{ 'selected' }} @endif>
                                                    @lang('common.inactive')</option>
                                                <option value="3"
                                                    @if ('3' == $editModeData->status) {{ 'selected' }} @endif>
                                                    @lang('common.terminated')</option>
                                            </select>
                                        </div>
                                    </div><!--/.col -->

                                    <div class="col-md-3">
                                        <label for="exampleInput">@lang('employee.photo')</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="	fa fa-picture-o"></i></span>
                                            <input class="form-control photo" id="photo"
                                                accept="image/png, image/jpeg, image/gif,image/jpg" name="photo"
                                                type="file">
                                        </div>
                                    </div><!--/.col -->

                                </div><!--/.row -->

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="emergency_name">@lang('employee.emergency_name')</label>
                                            <input class="form-control" id="emergency_name"
                                                placeholder="@lang('employee.emergency_name')" name="emergency_name" type="text"
                                                value="{{ $editModeData->emergency_name }}">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="emergency_phone">@lang('employee.emergency_phone')</label>
                                            <input class="form-control" id="emergency_phone"
                                                placeholder="@lang('employee.emergency_phone')" name="emergency_phone" type="text"
                                                value="{{ $editModeData->emergency_phone }}">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="emergency_relationship">@lang('employee.emergency_relationship')</label>
                                            <select class="residencySelect form-control select2"
                                                name="emergency_relationship" required>
                                                <option selected="selected" disabled="disabled">Select Relationship
                                                </option>
                                                @foreach (\EmergencyContactRelationship::toArray() as $key => $value)
                                                    <option value="{{ $key }}"
                                                        @isset($editModeData->emergency_relationship)
															@if ($key == $editModeData->emergency_relationship)
																selected="selected"
															@endif
														@endisset>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">Leave Group</label>
                                            <select name="leave_group_id" class="form-control employeeGroupId select2">
                                                <option value="">--- @lang('common.please_select') ---</option>
                                                @foreach ($leaveGroupList as $value)
                                                    @if ($editModeData->leaveGroup != null)
                                                        <option value="{{ $value->id }}"
                                                            @if ($value->id == $editModeData->leaveGroup->id) {{ 'selected' }} @endif>
                                                            {{ $value->name }}</option>
                                                    @else
                                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div><!--/.col -->

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">Employment Type</label>
                                            <select name="employment_type" class="form-control employmentType select2 required" id="employment_type">
                                                <?php
                                                $employmentTypes = ['Permanent', 'Contract', 'Seasonal', 'Internship', 'Part Time'];
                                                $selectedEmploymentType = isset($editModeData) ? Request::old('employment_type') : session('logged_session_data.employee_id');
                                                foreach ($employmentTypes as $type):
                                                    $isSelected = ($selectedEmploymentType == $type) ? 'selected' : '';
                                                ?>
                                                    <option value="<?= $type ?>" <?= $isSelected ?>><?= $type ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div><!--/.col -->

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exampleInput">Residency Status<span
                                                    class="validateRq">*</span></label>
                                            <select class="residencySelect form-control select2" name="residential_status"
                                                required>
                                                <option selected="selected" disabled="disabled">Select Residency Status
                                                </option>
                                                @foreach (\ResidencyStatus::toArray() as $key => $value)
                                                    <option value="{{ $key }}"
                                                        @isset($editModeData->residential_status)
																		@if ($key == $editModeData->residential_status)
																			selected="selected"
																		@endif
																	@endisset>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div><!--/.col -->

                                </div><!--/.row -->

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInput">NSSF Rate Group</label>
                                            <input type="hidden" name="nssf_rate_type" value="2">
                                            <input class="form-control" type="text" value="Tier 1 & 2" readonly>
                                            <small class="text-muted">Default: Tier 1 & 2 (Not editable)</small>
                                        </div>
                                    </div><!--/.col -->

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exampleInput">Nationality<span class="validateRq">*</span></label>
                                            <select class="nationalitySelect form-control select2" name="nationality"
                                                required>
                                                <option selected="selected" disabled="disabled" value="">Select
                                                    Nationality
                                                </option>
                                                @foreach (\Nationality::toArray() as $key => $value)
                                                    <option value="{{ $value }}"
                                                        @isset($editModeData->nationality)
																		@if ($value == $editModeData->nationality)
																			selected="selected"
																		@endif
																	@endisset>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div><!--/.col -->
                                </div><!--/.row -->

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('employee.address')</label>
                                            <textarea class="form-control address" id="address" placeholder="@lang('employee.address')" cols="30"
                                                rows="2" name="address">{{ $editModeData->address }}</textarea>
                                        </div>
                                    </div>
                                </div>


                                <br>
                                <!-- employee documents here -->
                                <h3 class="box-title">Employee Documents</h3>
                                <hr>

                                <div class="employee_document_append_div">
                                    <div class="row">
                                        <div class="col-md-9"></div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <input id="addEmployeeDocuments" type="button"
                                                    class="form-control btn btn-success appendBtnColor"
                                                    value="Add Documents">
                                            </div>
                                        </div><!--/.col -->

                                    </div><!--/.row -->
                                </div>
                                <!-- end of employee documents -->
                                <h3 class="box-title">
                                    @lang('employee.educational_qualification')
                                </h3>
                                <hr>

                                <div class="education_qualification_append_div">
                                    @if (isset($editModeData) && count($educationQualificationEditModeData) > 0)
                                        @foreach ($educationQualificationEditModeData as $educationQualificationValue)
                                            <div class="education_qualification_row_element">
                                                <input class="educationQualification_cid" id="educationQualification_cid"
                                                    name="educationQualification_cid[]" type="hidden"
                                                    value="{{ $educationQualificationValue->employee_education_qualification_id }}">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="exampleInput">
                                                                @lang('employee.institute')<span class="validateRq">*</span>
                                                            </label>
                                                            <select name="institute[]" class="form-control institute">
                                                                <option value="">--- @lang('common.please_select') ---</option>
                                                                <option value="Board"
                                                                    @if ($educationQualificationValue->institute == 'Board') {{ 'selected' }} @endif>
                                                                    @lang('employee.board')</option>
                                                                <option value="University"
                                                                    @if ($educationQualificationValue->institute == 'University') {{ 'selected' }} @endif>
                                                                    @lang('employee.university')</option>
                                                                <option value="Primary"
                                                                    @if ($educationQualificationValue->institute == 'Primary') {{ 'selected' }} @endif>
                                                                    @lang('employee.primary')</option>
                                                                <option value="Secondary"
                                                                    @if ($educationQualificationValue->institute == 'Secondary') {{ 'selected' }} @endif>
                                                                    @lang('employee.secondary')</option>
                                                                <option value="Tertiary"
                                                                    @if ($educationQualificationValue->institute == 'Tertiary') {{ 'selected' }} @endif>
                                                                    @lang('employee.tertiary')</option>
                                                            </select>
                                                        </div>
                                                    </div><!--/.col -->

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="exampleInput">@lang('employee.name_institution')<span
                                                                    class="validateRq">*</span></label>
                                                            <input type="text" name="board_university[]"
                                                                class="form-control board_university"
                                                                id="board_university" placeholder="@lang('employee.name_institution')"
                                                                value="{{ $educationQualificationValue->board_university }}">
                                                        </div>
                                                    </div><!--/.col -->

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="exampleInput">@lang('employee.degree')<span
                                                                    class="validateRq">*</span></label>
                                                            <input type="text" name="degree[]"
                                                                class="form-control degree required" id="degree"
                                                                placeholder="Example: B.Sc. Engr.(Bachelor of Science in Engineering)"
                                                                value="{{ $educationQualificationValue->degree }}">
                                                        </div>
                                                    </div><!--/.col -->

                                                    <div class="col-md-3">
                                                        <label for="exampleInput">@lang('employee.passing_year')<span
                                                                class="validateRq">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon"><i
                                                                    class="fa fa-calendar-o"></i></span>
                                                            <input type="text" name="passing_year[]"
                                                                class="form-control yearPicker required" id="passing_year"
                                                                placeholder="@lang('employee.passing_year')"
                                                                value="{{ $educationQualificationValue->passing_year }}">
                                                        </div>
                                                    </div><!--/.col -->
                                                </div><!--/.row -->
                                                <div class="row">
                                                    {{-- <div class="col-md-3">
														<div class="form-group">
															<label for="exampleInput">@lang('employee.result')</label>
															<select name="result[]" class="form-control result">
																<option value="">--- @lang('common.please_select') ---</option>
																<option value="First class" @if ($educationQualificationValue->result == 'First class') {{  "selected" }} @endif>First class</option>
																<option value="Second class" @if ($educationQualificationValue->result == 'Second class') {{  "selected" }} @endif>Second class</option>
																<option value="Third class" @if ($educationQualificationValue->result == 'Third class') {{  "selected" }} @endif>Third class</option>
															</select>
														</div>
													</div> --}}
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="exampleInput">@lang('employee.gpa') /
                                                                @lang('employee.cgpa')</label>
                                                            <input type="text" name="cgpa[]"
                                                                class="form-control cgpa" id="cgpa"
                                                                placeholder="Example: 5.00,4.63"
                                                                value="{{ $educationQualificationValue->cgpa }}">
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
                                                </div><!--/.row -->
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <!--/.education_qualification_append_div-->

                                <div class="row">
                                    <div class="col-md-9"></div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input id="addEducationQualification" type="button"
                                                class="form-control btn btn-success appendBtnColor"
                                                value="@lang('employee.add_educational_qualification')">
                                        </div>
                                    </div>
                                </div><!--/.row -->

                                <h3 class="box-title">
                                    @lang('employee.professional_experience')
                                </h3>
                                <hr>

                                <div class="experience_append_div">
                                    @if (isset($editModeData) && count($experienceEditModeData) > 0)
                                        @foreach ($experienceEditModeData as $experienceValue)
                                            <input class="employee_experience_id" id="employee_experience_id"
                                                name="employeeExperience_cid[]" type="hidden"
                                                value="{{ $experienceValue->employee_experience_id }}">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="exampleInput">@lang('employee.organization_name')<span
                                                                class="validateRq">*</span></label>
                                                        <input type="text" name="organization_name[]"
                                                            class="form-control organization_name" id="organization_name"
                                                            placeholder="@lang('employee.organization_name')"
                                                            value="{{ $experienceValue->organization_name }}">
                                                    </div>
                                                </div><!--/.col -->
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="exampleInput">@lang('employee.designation')<span
                                                                class="validateRq">*</span></label>
                                                        <input required type="text" name="designation[]"
                                                            class="form-control designation" id="designation"
                                                            placeholder="@lang('employee.designation')"
                                                            value="{{ dateConvertDBtoForm($experienceValue->designation) }}">
                                                    </div>
                                                </div><!--/.col -->
                                                <div class="col-md-3">
                                                    <label for="exampleInput">@lang('common.from_date')<span
                                                            class="validateRq">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i
                                                                class="fa fa-calendar"></i></span>
                                                        <input type="text" name="from_date[]"
                                                            class="form-control dateField" id="from_date"
                                                            placeholder="@lang('common.from_date')"
                                                            value="{{ dateConvertDBtoForm($experienceValue->from_date) }}">
                                                    </div>
                                                </div><!--/.col -->
                                                <div class="col-md-3">
                                                    <label for="exampleInput">@lang('common.to_date')<span
                                                            class="validateRq">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i
                                                                class="fa fa-calendar"></i></span>
                                                        <input type="text" name="to_date[]"
                                                            class="form-control dateField" id="to_date"
                                                            placeholder="@lang('common.to_date')"
                                                            value="{{ dateConvertDBtoForm($experienceValue->to_date) }}">
                                                    </div>
                                                </div><!---/.col -->
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="exampleInput">@lang('employee.responsibility')<span
                                                                class="validateRq">*</span></label>
                                                        <textarea name="responsibility[]" class="form-control responsibility" placeholder="@lang('employee.responsibility')"
                                                            cols="30" rows="2" required>{{ $experienceValue->responsibility }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="exampleInput">@lang('employee.skill')<span
                                                                class="validateRq">*</span></label>
                                                        <textarea name="skill[]" class="form-control skill" placeholder="@lang('employee.skill')" cols="30" rows="2">{{ $experienceValue->skill }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-3"></div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="button"
                                                            class="form-control btn btn-danger deleteExperience appendBtnColor"
                                                            style="margin-top: 17px" value="@lang('common.delete')">
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                        @endforeach
                                    @endif
                                </div><!--/.experience_append_div -->

                                <div class="row">
                                    <div class="col-md-9"></div>
                                    <div class="col-md-3">
                                        <div class="form-group"><input id="addExperience" type="button"
                                                class="form-control btn btn-success appendBtnColor"
                                                value="@lang('employee.add_professional_experience')"></div>
                                    </div>
                                </div><!--/.row -->

                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-12 ">
                                            <button type="submit" class="btn btn-info btn_style"><i
                                                    class="fa fa-pencil"></i> @lang('common.update')</button>
                                        </div>
                                    </div>
                                </div><!--/.form-actions -->
                            </div><!--/.form-body -->
                            </form> 
                        </div>
                        <!--/.panel-body -->
                    </div>
                </div>
                <!--/.panel panel-info -->
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
                </div><!--/.col -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="exampleInput">@lang('employee.name_institution')<span class="validateRq">*</span></label>
                        <input type="text" name="board_university[]" class="form-control board_university"
                            id="board_university" placeholder="@lang('employee.name_institution')">
                    </div>
                </div><!--/.col -->
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
            </div><!--/.row -->
            <div class="row">
                {{-- <div class="col-md-3">
				<div class="form-group">
					<label for="exampleInput">@lang('employee.result')</label>
					<select name="result[]" class="form-control result">
						<option value="">--- @lang('common.please_select') ---</option>
						<option value="First class">First class</option>
						<option value="Second class">Second class</option>
						<option value="Third class">Third class</option>
					</select>
				</div>
			</div> --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="exampleInput">@lang('employee.gpa') / @lang('employee.grade')</label>
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
        </div><!--/.row_element1 -->

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
                        <textarea name="skill[]" class="form-control skill" placeholder="@lang('employee.skill')" cols="30"
                            rows="2"></textarea>
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
        </div><!--/.row_element2 -->

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
                            placeholder="document_name" cols="30" rows="2" required
                            accept="application/pdf"></input>
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
        $('.workShiftSelect').select2({
            placeholder: 'Search shift',
        });
        $('.department_id').select2({
            placeholder: 'Search department',
        });
        $('.supervisor_id').select2({
            placeholder: 'Search supervisor',
        });
        $('.designation_id').select2({
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
        $('.location_id, .employeeSectionId, .employeeGroupId, .role22, .roleSelect, .employmentType')
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

            var identityTypeMap = {
                'national_id': 'e.g., 12345678 (7-14 digits)',
                'passport': 'e.g., AB1234567 (alphanumeric)',
                'military_id': 'e.g., M12345 (alphanumeric)',
                'driving_licence': 'e.g., DL-12345678 (alphanumeric with hyphens)',
                'alien_id': 'e.g., 123456 (6-9 digits)',
                'diplomatic_id': 'e.g., DPL12345 (alphanumeric)'
            };

            function updateNationalIdPlaceholder() {
                var selectedType = $('#identity_type').val();
                var placeholderText = identityTypeMap[selectedType] || ' @lang('employee.finger_print_no')';
                $('#national_id').attr('placeholder', placeholderText);
            }

            // Initial call on page load
            updateNationalIdPlaceholder();

            // Update on change
            $('#identity_type').on('change', updateNationalIdPlaceholder);
        });
    </script>
@endsection