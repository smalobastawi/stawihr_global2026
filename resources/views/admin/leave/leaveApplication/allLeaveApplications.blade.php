@extends('admin.master')

@section('title', 'All leave Applications')

@section('content')

    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="#"><i class="fa fa-home"></i>
                            @lang('dashboard.dashboard')</a></li>
                    <li>@yield('title')</li>
                </ol>

            </div>
            <div>
                <a href="{{ route('leaveManagement.manualUploadView') }}"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i
                        class="fa fa-plus-circle" aria-hidden="true"></i>Upload from Excel</a>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
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

                            <div id="searchBox">
                                <form id="filter_form" method="GET"
                                    action="{{ route('allLeaveApplications.allLeaveApplications') }}">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <label class="control-label" for="financial_year_id">Financial Year</label>
                                            <select name="financial_year_id" class="form-control select2"
                                                id="financial_year_id">
                                                <option value="">Financial Year</option>
                                                @foreach ($financialYears as $financialYear)
                                                    <option value="{{ $financialYear->id }}"
                                                        {{ request('financial_year_id') == $financialYear->id ? 'selected' : '' }}>
                                                        {{ $financialYear->name ?? $financialYear->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-2">
                                            <label class="control-label" for="from_date">From Date<span
                                                    class="validateRq">*</span>:</label>
                                            <input type="text" class="form-control dateField" required
                                                value="{{ request('from_date', $start_date) }}"
                                                placeholder="@lang('common.date')" name="from_date" id='from_date'>
                                        </div>
                                        <div class="col-sm-2">
                                            <label class="control-label" for="to_date">To Date<span
                                                    class="validateRq">*</span>:</label>
                                            <input type="text" class="form-control dateField" required
                                                value="{{ request('to_date', $end_date) }}" placeholder="@lang('common.date')"
                                                name="to_date" id="to_date">
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label for="location_id">@lang('employee.location')</label>
                                                <select name="location_id" class="form-control location_id select2"
                                                    id="location_id">
                                                    <option value="">All Locations</option>
                                                    @foreach ($locations as $location)
                                                        <option value="{{ $location->location_id }}"
                                                            {{ request('location_id') == $location->location_id ? 'selected' : '' }}>
                                                            {{ $location->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label for="department_id">@lang('employee.department')</label>
                                                <select name="department_id" class="form-control department_id select2"
                                                    id="department_id">
                                                    <option value="">--- @lang('employee.select_department')---</option>
                                                    @foreach ($departments as $department)
                                                        <option value="{{ $department->department_id }}"
                                                            {{ request('department_id') == $department->department_id ? 'selected' : '' }}>
                                                            {{ $department->department_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label for="leave_type_id">Leave Type</label>
                                                <select name="leave_type_id" class="form-control select2"
                                                    id="leave_type_id">
                                                    <option value="">-- All Leave Types --</option>
                                                    @foreach ($leaveTypes as $leaveType)
                                                        <option value="{{ $leaveType->leave_type_id }}"
                                                            {{ request('leave_type_id') == $leaveType->leave_type_id ? 'selected' : '' }}>
                                                            {{ $leaveType->leave_type_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label for="final_status">Status</label>
                                                <select name="final_status" class="form-control select2" id="final_status">
                                                    <option value="">-- All Statuses --</option>
                                                    <option value="{{ \App\Lib\Enumerations\LeaveStatus::PENDING }}"
                                                        {{ request('final_status') == \App\Lib\Enumerations\LeaveStatus::PENDING ? 'selected' : '' }}>
                                                        Pending
                                                    </option>
                                                    <option value="{{ \App\Lib\Enumerations\LeaveStatus::APPROVE }}"
                                                        {{ request('final_status') == \App\Lib\Enumerations\LeaveStatus::APPROVE ? 'selected' : '' }}>
                                                        Approved
                                                    </option>
                                                    <option value="{{ \App\Lib\Enumerations\LeaveStatus::REJECT }}"
                                                        {{ request('final_status') == \App\Lib\Enumerations\LeaveStatus::REJECT ? 'selected' : '' }}>
                                                        Rejected
                                                    </option>
                                                    <option value="{{ \App\Lib\Enumerations\LeaveStatus::RECALL }}"
                                                        {{ request('final_status') == \App\Lib\Enumerations\LeaveStatus::RECALL ? 'selected' : '' }}>
                                                        Recalled
                                                    </option>
                                                    <option
                                                        value="{{ \App\Lib\Enumerations\LeaveStatus::RECALL_APPROVED }}"
                                                        {{ request('final_status') == \App\Lib\Enumerations\LeaveStatus::RECALL_APPROVED ? 'selected' : '' }}>
                                                        Recall Approved
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <label for="filter">&nbsp;</label>
                                            <input type="submit" id="filter" style="width: 100px;"
                                                class="btn btn-info form-control" value="@lang('common.filter')">
                                        </div>
                                        <div class="col-sm-2">
                                            <label for="reset">&nbsp;</label>
                                            <a href="{{ route('allLeaveApplications.allLeaveApplications') }}"
                                                class="btn btn-warning form-control" style="width: 100px;">Reset</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <hr>
                            <div class="table-responsive1" id='table_reponse' style="width: 100%; overflow-x: scroll;">
                                @include('admin.leave.leaveApplication.all_applications_table', [
                                    'results' => $results,
                                ])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('page_scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#filter_form select').select2();
        });
    </script>
@endsection
