@extends('admin.master')
@section('content')
@section('title')
    @lang('employee.profile')
@endsection
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
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor">
                        <a href="{{ url('dashboard') }}">
                            <i class="fa fa-home"></i> @lang('dashboard.dashboard')
                        </a>
                    </li>
                    <li>@yield('title')</li>
                </ol>
            </div>
            <ul class="nav nav-tabs">
                <li class="active">
                    <a data-toggle="tab" href="#profile">
                        My Profile
                    </a>
                </li>
                <li>
                    <a data-toggle="tab" href="#payout_profile">
                        My Payroll Profile
                    </a>
                </li>
                <li>
                    <a data-toggle="tab" href="#contract_details">
                        My Contract Details
                    </a>
                </li>
                <li>
                    <a data-toggle="tab" href="#employee_documents">
                        My Documents
                    </a>
                </li>
            </ul>
        </div>
        <!--/.row -->

        <div class="tab-content">
            <div class="tab-pane fade in active" id="profile">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <i class="mdi mdi-table fa-fw"></i> @lang('employee.profile')
                            </div>
                            <div class="panel-wrapper collapse in" aria-expanded="true">
                                <div class="panel-body">
                                    <div class="col-xs-6 col-sm-6 col-md-4">
                                        <div id="resume">
                                            <p>
                                                <strong>{{ !$employeeInfo->first_name }}
                                                    {{ !$employeeInfo->last_name }}</strong>
                                            </p>
                                            <p><b>@lang('employee.email') :</b> {{ $employeeInfo->email }}</p>
                                            <p>
                                            </p>
                                            <p class="applicant_address"><b>@lang('employee.address')
                                                    : </b> {{ $employeeInfo->address }}</p>
                                            <p>
                                                <b>@lang('employee.phone') :</b> {{ $employeeInfo->phone }}
                                            </p>
                                            <p style="text-transform: capitalize">
                                                <b>Supervisor:</b>
                                                @if ($supervisor_info != '')
                                                    {{ $supervisor_info->first_name }} {{ $supervisor_info->last_name }}
                                                @else
                                                    .. N/A
                                                @endif
                                            </p>
                                            @php
                                                $roles = Auth::user()->getRoleNames();
                                            @endphp
                                            <p><b>System Roles Assigned:</b>
                                                @foreach ($roles as $role)
                                                    <span class="badge badge-info">{{ $role }}</span>
                                                @endforeach
                                            </p>
                                        </div>
                                        <!--/#resume -->
                                    </div>
                                    <!--/.col-xs-6 col-sm-6 col-md-4 -->

                                    <div class="col-md-offset-2 col-xs-6 col-sm-6 col-md-6">
                                        <div class="applicant_pic text-right">
                                            <?php
                                        if($employeeInfo->photo != ''){
                                        ?>
                                            <img style="width: 124px;height:135px" src="{!! asset('uploads/employeePhoto/' . $employeeInfo->photo) !!}">
                                            <?php  }else{ ?>
                                            <img style="width: 124px;height:135px" src="{!! asset('admin_assets/img/default.png') !!}">
                                            <?php } ?>
                                        </div>
                                        <br>
                                    </div>
                                    <!--/.col-xs-6 col-sm-6 col-md-4 -->

                                    <!-- Leave and leave rollovers start here -->
                                    <div class="education_qualification">
                                        <section class="content">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="panel-custom">
                                                        <h3 class="panel-title">
                                                            <i class="fa fa-bars"></i>
                                                            Leaves and Leave Rolled Over--
                                                            Leave Group(
                                                            {{ $employeeInfo->leaveGroup->name ?? ' No Leave Group Defined' }})
                                                            <a href="{{ route('ess.leave.index') }}"
                                                                class="btn btn-info" style="float: right; color:#fff;">
                                                                Apply For Leave
                                                            </a>
                                                        </h3>
                                                    </div>
                                                    <div class="box">
                                                        <div class="box-body">
                                                            <table id="example1"
                                                                class="table table-bordered table-hover">
                                                                <thead class="education_lable">
                                                                    <tr>
                                                                        <th>Leave type</th>
                                                                        <th>Entitled Days</th>
                                                                        <th>Earned days</th>
                                                                        <th>Rolled Over Days</th>
                                                                        <th>Days used</th>
                                                                        <th>Total Leave Balance</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="education_lable">
                                                                    @if (count($leaveTypes) > 0)
                                                                        @foreach ($leaveTyesData as $leaveType)
                                                                            <tr>
                                                                                <td>{{ $leaveType['name'] }}</td>
                                                                                <td>{{ number_format($leaveType['days_entitled'] ?? 0, 1) }}
                                                                                </td>
                                                                                <td>{{ number_format($leaveType['totalDays'], 1) }} </td>
                                                                                <td>{{ number_format($leaveType['roll_over_days'] ?? 0, 1) }}
                                                                                </td>
                                                                                <td>{{ number_format($leaveType['days_used'], 1) }}</td>
                                                                                <td>{{ number_format($leaveType['totalBlance'], 1) }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr class="text-center">
                                                                            <td colspan="4">No leave types found

                                                                                @if (!$employeeInfo->leaveGroup)
                                                                                    Employee Has No Leave Group Defined
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <!--/.box -->
                                                </div>
                                            </div>
                                        </section>
                                    </div>
                                    <!--/.education_qualification -->

                                    <!----------------------
                                    'ACADEMIC QUALIFICATION:------------------------>
                                    <div class="education_qualification">
                                        <section class="content">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="panel-custom">
                                                        <h3 class="panel-title"><i class="fa fa-graduation-cap"></i>
                                                            @lang('employee.educational_qualification')
                                                            @can('ess.employee.qualification.store')
                                                                <a href="javascript:void(0)"
                                                                    data-id="{{ $employeeInfo->employee_id }}"
                                                                    class="btn btn-info addEducationQualificationBtn"
                                                                    style="float: right; color:#fff;">
                                                                    Add Qualification
                                                                </a>
                                                            @endcan

                                                        </h3>
                                                    </div>
                                                    <div class="box">
                                                        <div class="box-body">
                                                            <table id="example1"
                                                                class="table table-bordered table-hover">
                                                                <thead class="education_lable">
                                                                    <tr>
                                                                        <th>@lang('employee.institute')</th>
                                                                        <th>@lang('employee.degree')</th>
                                                                        <th>@lang('employee.board')
                                                                            / @lang('employee.university')</th>
                                                                        <th>@lang('employee.result')</th>
                                                                        <th>@lang('employee.gpa')
                                                                            / @lang('employee.cgpa')</th>
                                                                        <th>@lang('employee.passing_year')</th>
                                                                        <th>@lang('employee.certificate')</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="education_lable">
                                                                    @if (count($employeeEducation) > 0)
                                                                        @foreach ($employeeEducation as $education)
                                                                            <tr>
                                                                                <td>{{ $education->institute }}</td>
                                                                                <td>{{ $education->degree }}</td>
                                                                                <td>{{ $education->board_university }}
                                                                                </td>
                                                                                <td>{{ $education->result }}</td>
                                                                                <td>{{ $education->cgpa }}</td>
                                                                                <td>{{ $education->passing_year }}</td>
                                                                                <td>
                                                                                    @if ($education->certificate)
                                                                                        <a href="{{ asset('storage/' . $education->certificate) }}"
                                                                                            target="_blank">
                                                                                            View Certificate
                                                                                        </a>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr class="text-center">
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                        <br>
                                    </div>
                                    <!--/.education_qualification -->

                                    <div class="education_qualification">
                                        <section class="content">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="panel-custom">
                                                        <h3 class="panel-title"><i class="fa fa-laptop"></i>
                                                            @lang('employee.professional_experience')
                                                            @can('ess.employee.experience.store')
                                                                <a href="javascript:void(0)"
                                                                    data-id="{{ $employeeInfo->employee_id }}"
                                                                    class="btn btn-info addEmployeeExperienceBtn"
                                                                    style="float: right; color:#fff;">
                                                                    Add Professional Experience
                                                                </a>
                                                            @endcan
                                                        </h3>
                                                    </div>
                                                    <div class="box">
                                                        <div class="box-body">
                                                            <table id="example1"
                                                                class="table table-bordered table-hover">
                                                                <thead class="education_lable">
                                                                    <tr>
                                                                        <th>@lang('employee.organization_name')</th>
                                                                        <th>@lang('employee.designation')</th>
                                                                        <th>@lang('employee.duration')</th>
                                                                        <th>@lang('employee.skill')</th>
                                                                        <th>@lang('employee.responsibility')</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="education_lable">
                                                                    @if (count($employeeExperience) > 0)
                                                                        @foreach ($employeeExperience as $experience)
                                                                            <tr>
                                                                                <td>{{ $experience->organization_name }}
                                                                                </td>
                                                                                <td>{{ $experience->designation }}</td>
                                                                                <td>{{ $experience->from_date }}
                                                                                    To {{ $experience->to_date }}</td>
                                                                                <td>
                                                                                    {!! $experience->skill !!}</td>
                                                                                <td>
                                                                                    {!! $experience->responsibility !!}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr>
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                            <td>--</td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                        <br>
                                    </div>
                                    <!--/.education_qualification -->

                                    <!-------------personal info --------->
                                    <div class="personal_info">
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="panel-custom">
                                                    <h3 class="panel-title">
                                                        <i class="fa fa-info-circle"></i>
                                                        @lang('employee.personal_information')
                                                        <a href="{{ route('ess.employee.edit.profile') }}"
                                                            class="btn btn-info" style="float: right; color:#fff;">
                                                            Update Personal Info
                                                        </a>
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                        <!--/.row -->

                                        <div class="row">
                                            <div class="personal_info">
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">@lang('employee.name')</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->first_name }}
                                                        {{ $employeeInfo->last_name }}</div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">@lang('employee.email')</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->email }}</div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">@lang('employee.address')</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->address }}</div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">@lang('employee.phone')</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->phone }}</div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">
                                                        @lang('employee.date_of_joining')</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ dateConvertDBtoForm($employeeInfo->date_of_joining) }}
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">@lang('employee.date_of_birth')
                                                    </div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ dateConvertDBtoForm($employeeInfo->date_of_birth) }}
                                                    </div>
                                                </div>

                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">@lang('employee.gender')</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->gender }}</div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">@lang('employee.religion')</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->religion }}</div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">@lang('employee.marital_status')
                                                    </div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->marital_status }}
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">@lang('employee.emergency_name')
                                                    </div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->emergency_name }}
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">@lang('employee.emergency_phone')
                                                    </div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->emergency_phone }}
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">
                                                        @lang('employee.emergency_relationship')
                                                    </div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ \EmergencyContactRelationship::getName($employeeInfo->emergency_relationship) }}
                                                    </div>
                                                </div>
                                                <br>

                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">
                                                        Location
                                                    </div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;
                                                        @if (isset($employeeInfo->workLocation) && !empty($employeeInfo->workLocation))
                                                            {{ $employeeInfo->workLocation->location_name ?? '' }}
                                                        @endif

                                                    </div>
                                                </div>

                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">
                                                        Region
                                                    </div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;
                                                        @if (isset($employeeInfo->workLocation->region) && !empty($employeeInfo->workLocation->region))
                                                            {{ $employeeInfo->workLocation->region->name }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--/.row -->
                                    </div>
                                    <!--/. personal_info -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="payout_profile" class="tab-pane fade in">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>S/No</th>
                            <th>Name</th>
                            <th>Channel Type</th>
                            <th>Account Number</th>
                            <th>Location</th>
                            <th>Location code</th>
                            <th>Swift Code</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($employeeInfo->employeePayoutChannel)
                            <tr>
                                <td>1</td>
                                <td>{{ $employeeInfo->employeePayoutChannel->payoutChannel->name }}</td>
                                <td>{{ $employeeInfo->employeePayoutChannel->payoutChannel->type_of_channel }}</td>
                                <td>{{ $employeeInfo->employeePayoutChannel->account_number }}</td>
                                <td>{{ $employeeInfo->employeePayoutChannel->location }}</td>
                                <td>{{ $employeeInfo->employeePayoutChannel->branch_code }}</td>
                                <td>{{ $employeeInfo->employeePayoutChannel->swift_code }}</td>
                            </tr>
                        @else
                            <tr>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

            </div>

            <div id="contract_details" class="tab-pane fade in">
                @if ($employeeInfo->contractDetails)
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>S/No</th>
                                <th>Hire Date</th>
                                <th>Contract Type</th>
                                <th>Probation Start</th>
                                <th>Probation End</th>
                                <th>Contract Start</th>
                                <th>Contract End</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employeeInfo->contractDetails as $contractDetails)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $contractDetails->hire_date }}</td>
                                    <td>{{ $contractDetails->hire_date }}</td>
                                    <td>{{ $contractDetails->probation_start_date }}</td>
                                    <td>{{ $contractDetails->probation_end_date }}</td>

                                    <td>{{ $contractDetails->start_date }}</td>
                                    <td>{{ $contractDetails->end_date }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

            </div>

            <div id="employee_documents" class="tab-pane fade in">
                <section class="content">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="panel-custom">
                                <h3 class="panel-title">
                                    <i class="fa fa-laptop"></i>My Documents
                                </h3>
                            </div>
                            <div class="box">
                                <div class="box-body">
                                    <br><br>
                                    <a href="javascript:void(0)" id="addDocumentsBtn">
                                        <div class="btn btn-success">
                                            Add Document
                                        </div>
                                    </a>
                                    <br><br>
                                    <table id="example1" class="table table-bordered table-hover">
                                        <thead class="education_lable">
                                            <tr>
                                                <th>S.No</th>
                                                <th>Document Name</th>
                                                <th>Document Type</th>
                                                <th>Date uploaded</th>
                                                <th>View/Download</th>
                                            </tr>
                                        </thead>
                                        <tbody class="education_lable">
                                            @if (count($employeeInfo->employeeDocuments) > 0)
                                                @foreach ($employeeInfo->employeeDocuments as $key => $documents)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $documents->document_name }}</td>
                                                        <td>{{ $documents->document_type }}</td>
                                                        <td>{{ date('Y-m-d', strtotime($documents->date_uploaded)) }}
                                                        </td>
                                                        <td>
                                                            <a href="{{ url('uploads/employeeDocs') . '/' . $documents->document_link }}"
                                                                target="_blank">
                                                                View
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td>--</td>
                                                    <td>--</td>
                                                    <td>--</td>
                                                    <td>--</td>
                                                    <td>--</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
        <!--/.tab-content -->
    @else
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor">
                        <a href="{{ url('dashboard') }}">
                            <i class="fa fa-home"></i> @lang('dashboard.dashboard')
                        </a>
                    </li>
                    <li>@yield('title')</li>
                </ol>
            </div>
            <ul class="nav nav-tabs">
                <li class="active">
                    <a data-toggle="tab" href="#adminProfile">
                        My Profile
                    </a>
                </li>
            </ul>
        </div>
        <!--/.row -->

        <div class="tab-content">
            <div class="tab-pane fade in active" id="adminProfile">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <i class="mdi mdi-table fa-fw"></i> @lang('employee.profile')
                            </div>

                            <div class="panel-wrapper collapse in" aria-expanded="true">
                                <div class="panel-body">

                                    <div class="col-xs-6 col-sm-6 col-md-4">
                                        <div id="resume">
                                            <p>
                                                <b> Name: </b> <strong> {{ Auth::user()->first_name }}
                                                    {{ Auth::user()->last_name }}</strong>
                                            </p>
                                            <p>
                                                <b>@lang('employee.email') :</b> {{ Auth::user()->email }}
                                            </p>
                                            <p>
                                                <b>@lang('employee.name') :</b> {{ Auth::user()->user_name }}
                                            </p>
                                            {{-- User Roles listing here --}}
                                            @php
                                                $roles = Auth::user()->getRoleNames();
                                            @endphp
                                            <p>System Roles Assigned: @foreach ($roles as $role)
                                                    <span class="badge badge-info">{{ $role }}</span>
                                                @endforeach
                                            </p>
                                            {{-- <p class="applicant_address"><b>@lang('employee.address')
                                                : </b> {{ $employeeInfo->address }}</p>
                                            <p>
                                                <b>@lang('employee.phone') :</b> {{ $employeeInfo->phone }}
                                            </p>
                                            <p style="text-transform: capitalize">
                                                Supervisor:
                                                @if ($supervisor_info != '')
                                                {{ $supervisor_info->first_name }} {{ $supervisor_info->last_name }}
                                                @else
                                                .. N/A
                                                @endif
                                            </p>
                                            <p>
                                                NSSF Deduction Group: @if ($employeeInfo->nssf_rate_type == 1)
                                                Old Rates
                                                @elseif($employeeInfo->nssf_rate_type == 2)
                                                Tier 1 and 2
                                                @elseif($employeeInfo->nssf_rate_type == 3)
                                                Tier 1 only
                                                @else
                                                No NSSf deduction
                                                @endif
                                            </p> --}}
                                        </div>
                                        <!--/#resume -->
                                    </div>
                                    <!--/.col-xs-6 col-sm-6 col-md-4 -->

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/.tab-content -->

    @endif
</div>
<!--/.container-fluid -->

<div class="modal fade" id="addQualificationModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="addQualificationForm">
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="addQualificationInstitute">
                                    Institute
                                </label>
                                <input type="text" name="institute" class="form-control"
                                    id="addQualificationInstitute" placeholder="Enter institute">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="addQualificationBoardUniversity">
                                    Exam Body
                                </label>
                                <input type="text" name="board_university" class="form-control"
                                    id="addQualificationBoardUniversity" placeholder="Enter exam body">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="addQualificationDegree">
                                    Certificate Title
                                </label>
                                <input type="text" name="degree" class="form-control"
                                    id="addQualificationDegree" placeholder="Enter degree">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="addQualificationResults">
                                    Awards
                                </label>
                                <input type="text" name="result" class="form-control"
                                    id="addQualificationResults" placeholder="Enter Awards">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="cgpa">
                                    GPA
                                </label>
                                <input type="text" name="cgpa" class="form-control" id="cgpa"
                                    placeholder="GPA">
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="passing_year">
                                    Passing Year
                                </label>
                                <input type="text" name="passing_year" class="form-control" id="passing_year"
                                    placeholder="Passing Year">
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="certificate">
                                    Attach Certificates
                                </label>
                                <input type="file" name="certificate" class="form-control" id="certificate">
                            </div>
                        </div>


                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="addEmployeeExperienceModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="addEmployeeExperienceForm">
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="addEmployeeExperienceOrganizationName">
                                    Organization Name
                                </label>
                                <input type="text" name="organization_name" class="form-control"
                                    id="addEmployeeExperienceOrganizationName" placeholder="Enter Organization Name">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="addEmployeeExperienceDesignation">
                                    Designation
                                </label>
                                <input type="text" name="designation" class="form-control"
                                    id="addEmployeeExperienceDesignation" placeholder="Enter Designation">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="addEmployeeExperienceFromDate">
                                    From Date
                                </label>
                                <input type="text" name="from_date" class="form-control from_date"
                                    id="addEmployeeExperienceFromDate" placeholder="From Date" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="addEmployeeExperienceToDate">
                                    To Date
                                </label>
                                <input type="text" name="to_date" class="form-control to_date"
                                    id="addEmployeeExperienceToDate" placeholder="To Date" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <div class="form-group">
                                <label for="addEmployeeExperienceSkills">
                                    Skills
                                </label>
                                <textarea name="skill" id="addEmployeeExperienceSkills" class="form-control" placeholder="Skills"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <div class="form-group">
                                <label for="addEmployeeExperienceResponsibility">
                                    Responsibilities
                                </label>
                                <textarea name="responsibility" id="addEmployeeExperienceResponsibility" class="form-control"
                                    placeholder="Responsibilities"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="addDocumentsModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="addDocumentsForm">
                <div class="modal-body">
                    @csrf
                    <div id="documentsFields">
                        <div class="row">
                            <input name="employeeDocuments_cid[]" type="hidden">
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="addDocumentsName">
                                        Document Name <span class="validateRq">*</span>
                                    </label>
                                    <input type="text" name="document_name[]" class="form-control"
                                        id="addDocumentsName" placeholder="Enter Document Name" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Type<span class="validateRq">*</span></label>
                                    <select name="document_type[]" class="form-control type" cols="30"
                                        rows="2" required>
                                        <option value="personal">Personal</option>
                                        <option value="official">Official</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="exampleInput">Upload file<span class="validateRq">*</span></label>
                                    <input type="file" name="document_file[]" class="form-control responsibility"
                                        placeholder="document_name" cols="30" rows="2" required
                                        accept="application/pdf">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group" style="padding-left: 1rem !important;">
                                <button type="button" class="btn btn-primary" id="addMoreEmployeeDocuments">
                                    <i class="fa fa-plus"></i> Add More Documentss
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

@endsection


@section('page_scripts')
<script>
    $(document).ready(function() {
        @if (employeeInfo())
            // Summernote
            $('#addEmployeeExperienceSkills').summernote();
            $('#addEmployeeExperienceResponsibility').summernote();
            $(document).on('click', '.addEducationQualificationBtn', function(e) {
                e.preventDefault();
                $('#addQualificationModal').modal('show');
            });

            $('#addQualificationForm').submit(function(e) {
                e.preventDefault();
                let form = $(this);
                let formData = new FormData(form[0]);
                let employee_id = '{{ $employeeInfo->employee_id }}';
                let path = '{{ route('ess.employee.qualification.store', ':employee') }}';
                path = path.replace(':employee', employee_id);
                $.ajax({
                    type: "POST",
                    url: path,
                    data: formData,
                    dataType: "json",
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        form.find('button[type=submit]').html(
                            '<i class="fa fa-spinner fa-spin"></i>'
                        );
                        form.find('button[type=submit]').attr('disabled', true);
                    },
                    complete: function() {
                        form.find('button[type=submit]').html(
                            'Submit'
                        );
                        form.find('button[type=submit]').attr('disabled', false);
                    },
                    success: function(data) {
                        if (data['status']) {
                            // toastr.success(data['message']);
                            $('#addQualificationModal').modal('hide');
                            $('#addQualificationForm').trigger("reset");
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                    },
                    error: function(data) {
                        var errors = data.responseJSON;
                        var errorsHtml = '<ul>';
                        $.each(errors['errors'], function(key, value) {
                            errorsHtml += '<li>' + value + '</li>';
                        });
                        errorsHtml += '</ul>';
                        toastr.error(errorsHtml);
                    }
                });
            });

            $(document).on("focus", ".from_date", function() {
                $(this).datepicker({
                    format: 'yyyy-mm-dd',
                    todayHighlight: true,
                    clearBtn: true,
                    // startDate: new Date(),
                }).on('changeDate', function(e) {
                    $(this).datepicker('hide');
                });
            });

            $(document).on("focus", ".to_date", function() {
                $(this).datepicker({
                    format: 'yyyy-mm-dd',
                    todayHighlight: true,
                    clearBtn: true,
                    // startDate: new Date(),
                }).on('changeDate', function(e) {
                    $(this).datepicker('hide');
                });
            });

            $(document).on('click', '.addEmployeeExperienceBtn', function(e) {
                e.preventDefault();
                $('#addEmployeeExperienceModal').modal('show');
            });

            $('#addEmployeeExperienceForm').submit(function(e) {
                e.preventDefault();
                let form = $(this);
                // Update the hidden textarea fields with Summernote content
                $('textarea[name="skill"]').val($('#addEmployeeExperienceSkills').summernote('code'));
                $('textarea[name="responsibility"]').val($('#addEmployeeExperienceResponsibility')
                    .summernote('code'));
                let formData = new FormData(form[0]);
                let employee_id = '{{ $employeeInfo->employee_id }}';
                let path = '{{ route('ess.employee.experience.store', ':employee') }}';
                path = path.replace(':employee', employee_id);
                $.ajax({
                    type: "POST",
                    url: path,
                    data: formData,
                    dataType: "json",
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        form.find('button[type=submit]').html(
                            '<i class="fa fa-spinner fa-spin"></i>'
                        );
                        form.find('button[type=submit]').attr('disabled', true);
                    },
                    complete: function() {
                        form.find('button[type=submit]').html(
                            'Submit'
                        );
                        form.find('button[type=submit]').attr('disabled', false);
                    },
                    success: function(data) {
                        if (data['status']) {
                            // toastr.success(data['message']);
                            $('#addEmployeeExperienceModal').modal('hide');
                            $('#addEmployeeExperienceForm').trigger("reset");
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                    },
                    error: function(data) {
                        var errors = data.responseJSON;
                        var errorsHtml = '<ul>';
                        $.each(errors['errors'], function(key, value) {
                            errorsHtml += '<li>' + value + '</li>';
                        });
                        errorsHtml += '</ul>';
                        toastr.error(errorsHtml);
                    }
                });
            });

            $(document).on('click', '.updatePersonalInfoBtn', function(e) {
                e.preventDefault();
                $('#updatePersonalInfoModal').modal('show');
            });

            $(document).on('click', '#addDocumentsBtn', function(e) {
                e.preventDefault();
                $('#addDocumentsModal').modal('show')
            });

            $("#addMoreEmployeeDocuments").click(function() {
                let newAnswerField = `
                <div class="answer-group">
                    <div class="row">
                        <input name="employeeDocuments_cid[]" type="hidden">
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <label for="addDocumentsName">
                                    Document Name <span class="validateRq">*</span>
                                </label>
                                <input type="text" name="document_name[]" class="form-control"
                                    id="addDocumentsName" placeholder="Enter Document Name" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <label for="exampleInput">Type<span class="validateRq">*</span></label>
                                <select name="document_type[]" class="form-control type" cols="30" rows="2" required>
                                    <option value="personal">Personal</option>
                                    <option value="official">Official</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <label for="exampleInput">Upload file<span class="validateRq">*</span></label>
                                <input type="file" name="document_file[]" class="form-control responsibility"
                                    placeholder="document_name" cols="30" rows="2" required accept="application/pdf">
                            </div>
                        </div>
                        <div class="col-12 col-md-3 d-flex align-items-center">
                            <div class="form-group">
                                <label for="">&nbsp;</label>
                                <button type="button" class="btn btn-danger remove-answer">
                                    <i class="fa fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    <br/>
                </div>
               `;
                $("#documentsFields").append(newAnswerField);
            });

            // Remove answer field
            $(document).on("click", ".remove-answer", function() {
                $(this).closest(".answer-group").remove();
            });

            $('#addDocumentsForm').submit(function(e) {
                e.preventDefault();
                let form = $(this);
                let formData = new FormData(form[0]);
                let employee_id = '{{ $employeeInfo->employee_id }}'
                let path = '{{ route('ess.documents.docs.upload', ':employee') }}';
                path = path.replace(':employee', employee_id);
                $.ajax({
                    type: "POST",
                    url: path,
                    data: formData,
                    dataType: "json",
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        form.find('button[type=submit]').html(
                            '<i class="fa fa-spinner fa-spin"></i>'
                        );
                        form.find('button[type=submit]').attr('disabled', true);
                    },
                    complete: function() {
                        form.find('button[type=submit]').html(
                            'Save'
                        );
                        form.find('button[type=submit]').attr('disabled', false);
                    },
                    success: function(data) {
                        if (data['status']) {
                            // toastr.success(data['message']);
                            $('#addDocumentsModal').modal('hide');
                            $('#addDocumentsForm').trigger("reset");
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                    },
                    error: function(data) {
                        var errors = data.responseJSON;
                        var errorsHtml = '<ul>';
                        $.each(errors['errors'], function(key, value) {
                            errorsHtml += '<li>' + value + '</li>';
                        });
                        errorsHtml += '</ul>';
                        toastr.error(errorsHtml);
                    }
                });

            });
        @endif
    });
</script>
@endsection
