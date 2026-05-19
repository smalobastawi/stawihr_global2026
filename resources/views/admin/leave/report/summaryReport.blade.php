@extends('admin.master')

@section('title', trans('leave.leave_summary_report'))

@section('content')
    <style>
        .employeeName {
            position: relative;
        }

        #employee_id-error {
            position: absolute;
            top: 66px;
            left: 0;
            width: 100%he;
            width: 100%;
            height: 100%;
        }
    </style>
    <script>
        jQuery(function() {
            $("#leaveReport").validate();
        });
    </script>
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                            @lang('dashboard.dashboard')</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>

        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i>@yield('title')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="row">
                                <div id="searchBox">
                                    <form action="{{ route('summaryReport.summaryReport.download') }}" method="POST" id="leaveReport">
@csrf

                                    <div class="col-md-1"></div>
                                    <div class="col-md-3">
                                        <div class="form-group employeeName">
                                            <label class="control-label" for="email">@lang('common.employee_name')<span
                                                    class="validateRq">*</span></label>
                                            <select class="form-control employee_id select2 required" required
                                                name="employee_id">
                                                <option value="">---- @lang('common.please_select') ----</option>
                                                @foreach ($employeeList as $value)
                                                    <option value="{{ $value->employee_id }}"
                                                        @if ($value->employee_id == $employee_id) {{ 'selected' }} @endif>
                                                        {{ $value->payroll_number }} - {{ $value->fullName() }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label" for="email">@lang('common.from_date')<span
                                                class="validateRq">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control dateField required" readonly
                                                placeholder="@lang('common.from_date')" name="from_date"
                                                value="@if (isset($from_date)) {{ $from_date }}@else {{ dateConvertDBtoForm(date('Y-01-01')) }} @endif">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label" for="email">@lang('common.to_date')<span
                                                class="validateRq">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control dateField required" readonly
                                                placeholder="@lang('common.to_date')" name="to_date"
                                                value="@if (isset($to_date)) {{ $to_date }}@else {{ dateConvertDBtoForm(date('Y-m-d')) }} @endif">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="submit" id="filter" style="margin-top: 25px; width: 100px;"
                                                class="btn btn-info " value="@lang('common.filter')">
                                        </div>
                                    </div>
                                    </form>
                                </div>
                            </div><!--/.row -->
                            <hr>
                            @if (count($results) > 0)
                                <h4 class="text-right">
                                    <a class="btn btn-success" style="color: #fff"
                                        href="{{ route('leave.summaryReport.download') }}?employee_id={{ $employee_id }}&from_date={{ $from_date }}&to_date={{ $to_date }}"><i
                                            class="fa fa-download fa-lg" aria-hidden="true"></i> @lang('common.download') PDF</a>
                                </h4>
                            @endif
                            @if (!empty($results))
                                <div class="table-responsive">
                                    <table id="" class="table table-bordered table-hover">
                                        <thead class="tr_header">
                                            <tr>
                                                <th style="width:50px;">S.No</th>
                                                <th>Leave Type</th>
                                                <th>Annual Entitlement</th>
                                                <th>Earned Days (FY)</th>
                                                <th>Rolled Over Days</th>
                                                <th>Adjustment Days</th>
                                                <th>Total Available</th>
                                                <th>Leave Consumed ({{ date('d/m/Y', strtotime($from_date)) }} -
                                                    {{ date('d/m/Y', strtotime($to_date)) }})</th>
                                                <th>Current Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($results) > 0)
                                                {{ $sl = null }}
                                                @foreach ($results as $value)
                                                    <tr>
                                                        <td>{{ ++$sl }}</td>
                                                        <td>{{ $value['leave_type_name'] }}</td>
                                                        <td>{{ $value['num_of_day'] }}</td>
                                                        <td>{{ $value['earned_days'] }}</td>
                                                        <td>{{ $value['rolled_over_leaves'] }}</td>
                                                        <td>
                                                            @if ($value['adjustment_days'] != 0)
                                                                <span
                                                                    class="label label-{{ $value['adjustment_days'] > 0 ? 'success' : 'danger' }}"
                                                                    title="Additions: +{{ $value['adjustment_details']['additions'] }}, Deductions: -{{ $value['adjustment_details']['deductions'] }}">
                                                                    {{ $value['adjustment_days'] > 0 ? '+' : '' }}{{ $value['adjustment_days'] }}
                                                                </span>
                                                                <small
                                                                    class="text-muted">({{ $value['adjustment_details']['count'] }}
                                                                    adj.)</small>
                                                            @else
                                                                0
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <strong>{{ $value['total_days_available'] }}</strong>
                                                            <small class="text-muted">
                                                                ({{ $value['earned_days'] }} +
                                                                {{ $value['rolled_over_leaves'] }}
                                                                @if ($value['adjustment_days'] != 0)
                                                                    {{ $value['adjustment_days'] > 0 ? '+' : '' }}{{ $value['adjustment_days'] }}
                                                                @endif)
                                                            </small>
                                                        </td>
                                                        <td>{{ $value['leave_consume'] }}</td>
                                                        <td>
                                                            <strong
                                                                class="text-{{ $value['current_balance'] > 0 ? 'success' : ($value['current_balance'] < 0 ? 'danger' : 'warning') }}">
                                                                {{ $value['current_balance'] }}
                                                            </strong>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                <!-- Summary Row -->
                                                <tr class="active">
                                                    <td colspan="8" class="text-right"><strong>Totals:</strong></td>
                                                    <td>
                                                        @php
                                                            $totalBalance = collect($results)->sum('current_balance');
                                                        @endphp
                                                        <strong>{{ $totalBalance }}</strong>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td colspan="9" class="text-center">
                                                        @lang('common.no_data_available') !
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Adjustment Summary Card (Optional) -->
                                @php
                                    $hasAdjustments =
                                        collect($results)
                                            ->filter(function ($item) {
                                                return $item['adjustment_days'] != 0;
                                            })
                                            ->count() > 0;
                                @endphp

                                @if ($hasAdjustments)
                                    <div class="panel panel-info mt-3">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">Leave Adjustment Summary</h4>
                                        </div>
                                        <div class="panel-body">
                                            <table class="table table-condensed table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Leave Type</th>
                                                        <th>Additions</th>
                                                        <th>Deductions</th>
                                                        <th>Net Adjustment</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($results as $value)
                                                        @if ($value['adjustment_days'] != 0)
                                                            <tr>
                                                                <td>{{ $value['leave_type_name'] }}</td>
                                                                <td class="text-success">
                                                                    +{{ $value['adjustment_details']['additions'] }}</td>
                                                                <td class="text-danger">
                                                                    -{{ $value['adjustment_details']['deductions'] }}</td>
                                                                <td
                                                                    class="text-{{ $value['adjustment_details']['net'] > 0 ? 'success' : ($value['adjustment_details']['net'] < 0 ? 'danger' : 'warning') }}">
                                                                    {{ $value['adjustment_details']['net'] > 0 ? '+' : '' }}{{ $value['adjustment_details']['net'] }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
