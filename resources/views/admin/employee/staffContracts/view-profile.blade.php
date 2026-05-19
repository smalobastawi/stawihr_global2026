@extends('admin.master')
@section('content')
@section('title')
    @lang('employee.profile')
@endsection
<style>
    .panel-custom {
        background-color: #41b3f9;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
        padding: 10px 15px;
        color: white;
    }

    .item {
        padding: 13px 21px;
    }
</style>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#profile">Staff Profile</a></li>
            <li><a data-toggle="tab" href="#payout_profile">Payroll Profile</a></li>

        </ul>

    </div>

    <div class="tab-content">
        <div id="payout_profile" class="tab-pane fade in">
            @if ($employeeInfo->payoutChannel)
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <td>S/No</td>
                            <td>Name</td>
                            <td>Channel Type</td>
                            <td>Account Number</td>
                            <td>Location</td>
                            <td>Swift Code</td>
                            <th>@lang('common.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1
                            </td>
                            <td>{{ $employeeInfo->payoutChannel->payoutChannel->name }}</td>
                            <td>{{ $employeeInfo->payoutChannel->payoutChannel->type_of_channel }}</td>
                            <td>{{ $employeeInfo->payoutChannel->payoutChannel->main_account_number }}</td>
                            <td>{{ $employeeInfo->payoutChannel->payoutChannel->location }}</td>
                            <td>{{ $employeeInfo->payoutChannel->payoutChannel->swift_code }}</td>
                            <td style="width: 100px;">
                                <a href="{!! route('payoutChannel.deleteFromStaff', $employeeInfo->payoutChannel->id) !!}" data-token="{!! csrf_token() !!}"
                                    data-id="{!! $employeeInfo->payoutChannel->id !!}"
                                    class="delete btn btn-danger btn-xs deleteBtn btnColor"><i class="fa fa-trash-o"
                                        aria-hidden="true"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            @endif
            <form action="{{ route('payoutChannel.updateStaff', ['id' => $employeeInfo->employee_id]) }}"
                method="POST">
                @csrf
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="payout_channel">Select/Change Payout Channel:</label>
                        <select name="payout_channel" id="payout_channel" class="form-control">
                            @foreach ($payourChannels as $value)
                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Add Payout Channel</button>
            </form>

        </div>

        <div class="row tab-pane fade in active" id="profile">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i>
                        @lang('employee.profile')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="panel-body">
                                <div class="">
                                    <div class="col-xs-6 col-sm-6 col-md-4">
                                        <div id="resume">
                                            <p>
                                                <strong>{{ $employeeInfo->first_name }}
                                                    {{ $employeeInfo->last_name }}</strong>
                                            </p>
                                            <p><b>@lang('employee.email') :</b> {{ $employeeInfo->email }}</p>
                                            <p>
                                            </p>
                                            <p class="applicant_address"><b>@lang('employee.address')
                                                    : </b> {{ $employeeInfo->address }}</p>
                                            <p><b>@lang('employee.phone') :</b> {{ $employeeInfo->phone }}</p>
                                            <p>

                                            <p>NSSF Deduction Group: @if ($employeeInfo->nssf_rate_type == 1)
                                                    Old Rates
                                                @elseif($employeeInfo->nssf_rate_type == 2)
                                                    Tier 1 and 2
                                                @elseif($employeeInfo->nssf_rate_type == 3)
                                                    Tier 1 only
                                                @else
                                                    No NSSf deduction
                                                @endif
                                            </p>

                                        </div>
                                    </div>
                                    <div class="col-md-offset-2 col-xs-6 col-sm-6 col-md-6">
                                        <div class="text-right">

                                            @if ($employeeInfo->status == 0)
                                                <a href="{!! route('employee.enable', $employeeInfo->employee_id) !!}" data-token="{!! csrf_token() !!}"
                                                    data-id="{!! $employeeInfo->employee_id !!}"
                                                    class="enable btn-xs deleteBtn btnColor">
                                                    <button class="btn btn-default">Enable</button>
                                                    <i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                            @else
                                                <a href="{!! route('employee.disable', $employeeInfo->employee_id) !!}" data-token="{!! csrf_token() !!}"
                                                    data-id="{!! $employeeInfo->employee_id !!}"
                                                    class="disable btn-xs deleteBtn btnColor">
                                                    <button class="btn btn-warning">Disable</button>
                                                    <i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                            @endif
                                            <a href="{!! route('employee.edit', $employeeInfo->employee_id) !!}">
                                                <button class="btn btn-primary">Edit User</button>
                                            </a>
                                            <a href="{!! route('employee.delete', $employeeInfo->employee_id) !!}" data-token="{!! csrf_token() !!}"
                                                data-id="{!! $employeeInfo->employee_id !!}"
                                                class="delete btn-xs deleteBtn btnColor">
                                                <button class="btn btn-danger">Delete</button>
                                                <i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                        </div>
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

                                    <!-------------personal info --------->

                                    <div class="personal_info">
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="panel-custom">
                                                    <h3 class="panel-title"><i class="fa fa-info-circle"></i>
                                                        @lang('employee.personal_information')
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
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
                                                    <div class="col-xs-2 col-sm-2 col-md-3">
                                                        {{ $employeeInfo->identity_type ? \App\Lib\Enumerations\IdentityType::toArray()[$employeeInfo->identity_type] : 'National ID' }}
                                                    </div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->national_id }}</div>
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
                                                    <div class="col-xs-2 col-sm-2 col-md-3">@lang('employee.date_of_joining')</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ dateConvertDBtoForm($employeeInfo->date_of_joining) }}
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">@lang('employee.date_of_birth')</div>
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
                                                    <div class="col-xs-2 col-sm-2 col-md-3">@lang('employee.marital_status')</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;{{ $employeeInfo->marital_status }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- work information -->
                                    <div class="work_info">
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="panel-custom">
                                                    <h3 class="panel-title"><i class="fa fa-info-circle"></i> Work
                                                        Information
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="personal_info">
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">Contract Details</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        Start:&nbsp;&nbsp;&nbsp;&nbsp;@isset($employeeInfo->date_of_joining)
                                                            {{ $employeeInfo->date_of_joining }}
                                                        @endisset
                                                        End: @isset($employeeInfo->date_of_leaving)
                                                            {{ $employeeInfo->date_of_leaving }}
                                                        @else
                                                            N/A
                                                        @endisset
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">Department/Group</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;@isset($employeeInfo->department->department_name)
                                                            {{ $employeeInfo->department->department_name }}
                                                        @endisset
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">Work Shift</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;@isset($employeeInfo->workShift->shift_name)
                                                            {{ $employeeInfo->workShift->shift_name }}
                                                            (
                                                            {{ date('H:i', strtotime($employeeInfo->workShift->start_time)) . '-' . date('H:i', strtotime($employeeInfo->workShift->end_time)) }}
                                                            )
                                                        @endisset
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">Location:</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;@isset($employeeInfo->workLocation->location_name)
                                                            {{ $employeeInfo->workLocation->location_name }}
                                                        @endisset
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">Designation</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">
                                                        :&nbsp;&nbsp;&nbsp;&nbsp;@isset($employeeInfo->designation->designation_name)
                                                            {{ $employeeInfo->designation->designation_name }}
                                                        @endisset
                                                    </div>
                                                </div>
                                                <div class="item">
                                                    <div class="col-xs-2 col-sm-2 col-md-3">Job Group</div>
                                                    <div class="col-xs-10 col-sm-10 col-md-9">

                                                        :&nbsp;@isset($employeeInfo->jobGroup->name)
                                                            {{ $employeeInfo->jobGroup->name }}
                                                        @endisset
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                    <!-- end of work information -->

                                    <div class="row">
                                        <div class="col-md-4"></div>
                                        <div class="col-md-4">
                                            <hr>

                                        </div>
                                        <div class="col-md-4"></div>
                                    </div>
                                    <div class="education_qualification">
                                        <section class="content">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="panel-custom">
                                                        <h3 class="panel-title"><i class="fa fa-laptop"></i>Employee
                                                            Documents
                                                        </h3>
                                                    </div>
                                                    <div class="box">
                                                        <div class="box-body">
                                                            <table id="example1"
                                                                class="table table-bordered table-hover">
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
                                                                                <td>{{ $documents->document_name }}
                                                                                </td>
                                                                                <td>{{ $documents->document_type }}
                                                                                </td>
                                                                                <td>{{ date('Y-m-d', strtotime($documents->date_uploaded)) }}
                                                                                </td>
                                                                                <td>
                                                                                    <a
                                                                                        href="{{ url('uploads/employeeDocs') . '/' . $documents->document_link }}">
                                                                                        View </a>
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

                                    <!-- Leave and leave rollovers start here -->
                                    <div class="education_qualification">
                                        <section class="content">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="panel-custom">
                                                        <h3 class="panel-title"><i class="fa fa-bars"></i> Leaves and
                                                            Leave
                                                            Rolled Over</h3>
                                                    </div>
                                                    <div class="box">
                                                        <div class="box-body">
                                                            <table id="example1"
                                                                class="table table-bordered table-hover">
                                                                <thead class="education_lable">
                                                                    <tr>
                                                                        <th>Leave type</th>
                                                                        <th>Allocated days</th>
                                                                        <th>Rollover Requested</th>
                                                                        <th>Total Leave Balance</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="education_lable">
                                                                    @if (count($leaveTypes) > 0)
                                                                    @else
                                                                        <tr class="text-center">
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
                                    <!-- END OF LEAVES AND ROLLOVERS. -->

                                    <!----------------------
                                        'ACADEMIC QUALIFICATION:
                                        ------------------------>
                                    <div class="education_qualification">
                                        <section class="content">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="panel-custom">
                                                        <h3 class="panel-title"><i class="fa fa-graduation-cap"></i>
                                                            @lang('employee.educational_qualification')
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

                                    <div class="education_qualification">
                                        <section class="content">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div class="panel-custom">
                                                        <h3 class="panel-title"><i class="fa fa-laptop"></i>
                                                            @lang('employee.professional_experience')
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
                                                                                <td>{{ $experience->skill }}</td>
                                                                                <td>{{ $experience->responsibility }}
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

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
