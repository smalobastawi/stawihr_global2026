@extends('admin.master')
@section('title', 'Update Profile')
@section('content')
    <style>
        .panel-custom {
            background-color: #F1F1F1;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
            padding: 10px 15px;
        }

        .item {
            padding: 13px 21px;
        }
    </style>

    <div class="container-fluid">
        @if (employeeInfo())
            <div class="row bg-title">
                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                    <ol class="breadcrumb">
                        <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                                @lang('dashboard.dashboard')</a></li>
                        <li>@yield('title')</li>

                    </ol>
                </div>
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                    <a href="{{ route('home.profile') }}"
                        class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                        <i class="fa fa-list-ul" aria-hidden="true"></i> My Profile
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-info">
                        <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i> @yield('title')</div>
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
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-hidden="true">×</button>
                                        <i
                                            class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                    </div>
                                @endif
                                @if (session()->has('error'))
                                    <div class="alert alert-danger alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-hidden="true">×</button>
                                        <i
                                            class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                    </div>
                                @endif
                                <form action="{{ route('ess.employee.update.profile', ) }}" method="POST" enctype="multipart/form-data" id="employeeForm">
@csrf
@method('PUT')

                                <div class="form-body">
                                    <h3 class="box-title">
                                        @lang('employee.personal_information')
                                    </h3>
                                    <hr>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInput">@lang('employee.first_name')</label>
                                                <input class="form-control required first_name" id="first_name"
                                                    placeholder="@lang('employee.first_name')" name="first_name" type="text"
                                                    value="{{ $employeeInfo->first_name }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInput">Middle name</label>
                                                <input class="form-control middle_name" id="middle_name"
                                                    placeholder="middle name" name="middle_name" type="text"
                                                    value="{{ $employeeInfo->middle_name }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInput">@lang('employee.last_name')</label>
                                                <input class="form-control last_name" id="last_name"
                                                    placeholder="@lang('employee.last_name')" name="last_name" type="text"
                                                    value="{{ $employeeInfo->last_name }}">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="exampleInput">@lang('employee.phone')<span
                                                    class="validateRq">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                                <input class="form-control number phone" id="phone"
                                                    placeholder="@lang('employee.phone')" name="phone" type="number"
                                                    value="{{ $employeeInfo->phone }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInput">@lang('employee.religion')</label>
                                                <input class="form-control religion" id="religion"
                                                    placeholder="@lang('employee.religion')" name="religion" type="text"
                                                    value="{{ $employeeInfo->religion }}">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInput">@lang('employee.marital_status')</label>
                                                <select name="marital_status" class="form-control status required select2">
                                                    <option value=""> @lang('common.please_select') </option>
                                                    <option value="Unmarried"
                                                        @if ('Unmarried' == $employeeInfo->marital_status) {{ 'selected' }} @endif>
                                                        @lang('employee.unmarried')</option>
                                                    <option value="Married"
                                                        @if ('Married' == $employeeInfo->marital_status) {{ 'selected' }} @endif>
                                                        Married</option>
                                                    <option value="Single"
                                                        @if ('Single' == $employeeInfo->marital_status) {{ 'selected' }} @endif>
                                                        Single</option>
                                                    <option value="Divorced"
                                                        @if ('Divorced' == $employeeInfo->marital_status) {{ 'selected' }} @endif>
                                                        Divorced</option>
                                                    <option value="Separated"
                                                        @if ('Separated' == $employeeInfo->marital_status) {{ 'selected' }} @endif>
                                                        Separated</option>
                                                    <option value="Separated"
                                                        @if ('Widowed' == $employeeInfo->marital_status) {{ 'selected' }} @endif>
                                                        Widowed</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="exampleInput">@lang('employee.photo')</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="	fa fa-picture-o"></i></span>
                                                <input class="form-control photo" id="photo"
                                                    accept="image/png, image/jpeg, image/gif,image/jpg" name="photo"
                                                    type="file">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInput">Nationality<span
                                                        class="validateRq">*</span></label>
                                                <select class="nationalitySelect form-control select2" name="nationality"
                                                    required>
                                                    <option selected="selected" disabled="disabled" value="">
                                                        Select Nationality
                                                    </option>
                                                    @foreach (\Nationality::toArray() as $key => $value)
                                                        <option value="{{ $value }}"
                                                            @isset($employeeInfo->nationality)
                                                                    @if ($value == $employeeInfo->nationality)
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
                                                <label for="exampleInput">@lang('employee.gender')<span
                                                        class="validateRq">*</span></label>
                                                <select class="genderSelect form-control select2" name="gender">
                                                    <option selected="selected" disabled="disabled">Select Gender</option>
                                                    @foreach (\Gender::toArray() as $key => $value)
                                                        <option value="{{ $value }}"
                                                            @isset($employeeInfo->gender)
																			@if ($value == $employeeInfo->gender)
																				selected="selected"
																			@endif
																		@endisset>
                                                            {{ $value }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="exampleInput">@lang('employee.date_of_birth')<span
                                                    class="validateRq">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input class="form-control date_of_birth dateField" id="date_of_birth"
                                                    placeholder="DD/MM/YYYY" name="date_of_birth" type="text"
                                                    value="{{ dateConvertDBtoForm($employeeInfo->date_of_birth) }}">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInput">Ethnicity<span
                                                        class="validateRq">*</span></label>
                                                <select class="ethnicitySelect form-control select2" name="ethnicity">
                                                    <option selected="selected" value="Other">
                                                        Select Ethnicity
                                                    </option>
                                                    @foreach (\App\Models\Ethnicity::getEthnicitiesArray() as $key => $value)
                                                        <option value="{{ $value }}"
                                                            @isset($employeeInfo->ethnicity)
                                                                    @if ($value == $employeeInfo->ethnicity)
                                                                        selected="selected"
                                                                    @endif
                                                                @endisset>
                                                            {{ $value }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="exampleInput">Residency Status<span
                                                        class="validateRq">*</span></label>
                                                <select class="residencySelect form-control select2"
                                                    name="residential_status" required>
                                                    <option selected="selected" disabled="disabled">Select Residency
                                                        Status
                                                    </option>
                                                    @foreach (\ResidencyStatus::toArray() as $key => $value)
                                                        <option value="{{ $key }}"
                                                            @isset($employeeInfo->residential_status)
                                                                    @if ($key == $employeeInfo->residential_status)
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
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="emergency_name">@lang('employee.emergency_name')</label>
                                                <input class="form-control" id="emergency_name"
                                                    placeholder="@lang('employee.emergency_name')" name="emergency_name" type="text"
                                                    value="{{ $employeeInfo->emergency_name }}">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="emergency_phone">@lang('employee.emergency_phone')</label>
                                                <input class="form-control" id="emergency_phone"
                                                    placeholder="@lang('employee.emergency_phone')" name="emergency_phone"
                                                    type="text" value="{{ $employeeInfo->emergency_phone }}">
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
                                                            @isset($employeeInfo->emergency_relationship)
                                                                @if ($key == $employeeInfo->emergency_relationship)
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
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="exampleInput">@lang('employee.address')</label>
                                                <textarea class="form-control address" id="address" placeholder="@lang('employee.address')" cols="30"
                                                    rows="2" name="address">{{ $employeeInfo->address }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- end of employee documents -->
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <button type="submit" class="btn btn-info btn_style">
                                            <i class="fa fa-pencil"></i> @lang('common.update')
                                        </button>
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    @endif

    </div><!--/.container-fluid -->
@endsection

