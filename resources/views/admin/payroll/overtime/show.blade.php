@extends('admin.master')
@section('content')
@section('title')
    Overtime Record Details
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('payroll.overtime.index') }}">Overtime Records</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group text-right">
                                      <a href="{{ route('payroll.overtime.index') }}" class="btn btn-info" style="color: white">
                                        <i class="fa fa-cloud"></i>Bulk Upload
                                    </a>
                                    <a href="{{ route('payroll.overtime.index') }}" class="btn btn-info" style="color: white">
                                        <i class="fa fa-list"></i>Go to list
                                    </a>
                                    <a href="{{ route('payroll.overtime.edit', $overtime->id) }}" class="btn btn-warning">
                                        <i class="fa fa-edit"></i> @lang('common.edit')
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Employee ID</th>
                                            <td>{{ $overtime->employee->payroll_number ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('employee.employee_name')</th>
                                            <td>{{ $overtime->employee->first_name ?? '' }} {{ $overtime->employee->last_name ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Month/Year</th>
                                            <td>{{ $overtime->month_year }}</td>
                                        </tr>
                                 
                                        <tr>
                                            <th>Weekend Hours Total</th>
                                            <td>{{ $overtime->weekend_hours_totals ?? '0.00' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Weekend Days Total</th>
                                            <td>{{ $overtime->weekend_days_totals ?? '0' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Public Holiday Hours Total</th>
                                            <td>{{ $overtime->public_holiday_hours_totals ?? '0.00' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Public Holiday Days Total</th>
                                            <td>{{ $overtime->public_holiday_days_totals ?? '0' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Weekday Hours Total</th>
                                            <td>{{ $overtime->weekday_hours_total ?? '0.00' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Weekday Days Total</th>
                                            <td>{{ $overtime->weekday_days_total ?? '0' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Payroll Period ID</th>
                                            <td>{{ $overtime->payroll_period_id ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Payroll Month</th>
                                            <td>{{ $overtime->month_year ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>@lang('common.status')</th>
                                            <td>
                                                @if($overtime->status == 1)
                                                    <span class="label label-success">@lang('common.active')</span>
                                                @else
                                                    <span class="label label-danger">@lang('common.inactive')</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Created By</th>
                                            <td>{{ $overtime->creator->first_name ?? '' }} {{ $overtime->creator->last_name ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Updated By</th>
                                            <td>{{ $overtime->updater->first_name ?? '' }} {{ $overtime->updater->last_name ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Created At</th>
                                            <td>{{ $overtime->created_at ? $overtime->created_at->format('d-m-Y H:i:s') : '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Updated At</th>
                                            <td>{{ $overtime->updated_at ? $overtime->updated_at->format('d-m-Y H:i:s') : '' }}</td>
                                        </tr>
                                    </table>
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
