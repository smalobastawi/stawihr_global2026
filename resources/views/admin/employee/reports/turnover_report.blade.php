@extends('admin.master')
@section('content')
@section('title')
    Employee Turnover Report
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-filter"></i> Filters
                </div>
                <div class="panel-body">
                    <form method="GET" action="{{ route('employee.turnoverReport') }}" class="form-horizontal">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Date From</label>
                                    <div class="col-md-8">
                                        <input type="text" name="date_from" class="form-control dateField"
                                            value="{{ request('date_from') }}" placeholder="DD/MM/YYYY" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Date To</label>
                                    <div class="col-md-8">
                                        <input type="text" name="date_to" class="form-control dateField"
                                            value="{{ request('date_to') }}" placeholder="DD/MM/YYYY" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Department</label>
                                    <div class="col-md-8">
                                        <select name="department_id" class="form-control select2">
                                            <option value="">All Departments</option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->department_id }}"
                                                    {{ request('department_id') == $department->department_id ? 'selected' : '' }}>
                                                    {{ $department->department_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Location</label>
                                    <div class="col-md-8">
                                        <select name="location_id" class="form-control select2">
                                            <option value="">All Locations</option>
                                            @foreach ($locations as $location)
                                                <option value="{{ $location->location_id }}"
                                                    {{ request('location_id') == $location->location_id ? 'selected' : '' }}>
                                                    {{ $location->location_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Designation</label>
                                    <div class="col-md-8">
                                        <select name="designation_id" class="form-control select2">
                                            <option value="">All Designations</option>
                                            @foreach ($designations as $designation)
                                                <option value="{{ $designation->designation_id }}"
                                                    {{ request('designation_id') == $designation->designation_id ? 'selected' : '' }}>
                                                    {{ $designation->designation_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-info btn-sm">
                                            <i class="fa fa-filter"></i> Generate Report
                                        </button>
                                        <a href="{{ route('employee.turnoverReport') }}" class="btn btn-default btn-sm">
                                            <i class="fa fa-refresh"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if ($summary)
        <div class="row">
            <div class="col-md-2 col-sm-6">
                <div class="panel panel-primary">
                    <div class="panel-heading text-center">Opening Headcount</div>
                    <div class="panel-body text-center">
                        <h3 class="m-0">{{ $summary['openingHeadcount'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="panel panel-success">
                    <div class="panel-heading text-center">Joiners</div>
                    <div class="panel-body text-center">
                        <h3 class="m-0">{{ $summary['joinersCount'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="panel panel-danger">
                    <div class="panel-heading text-center">Leavers</div>
                    <div class="panel-body text-center">
                        <h3 class="m-0">{{ $summary['leaversCount'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="panel panel-info">
                    <div class="panel-heading text-center">Closing Headcount</div>
                    <div class="panel-body text-center">
                        <h3 class="m-0">{{ $summary['closingHeadcount'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="panel panel-default">
                    <div class="panel-heading text-center">Average Headcount</div>
                    <div class="panel-body text-center">
                        <h3 class="m-0">{{ number_format($summary['averageHeadcount'], 1) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="panel panel-warning">
                    <div class="panel-heading text-center">Turnover Rate</div>
                    <div class="panel-body text-center">
                        <h3 class="m-0">{{ $summary['turnoverRate'] }}%</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Report period: {{ dateConvertDBtoForm($summary['dateFrom']) }} to
                    {{ dateConvertDBtoForm($summary['dateTo']) }}.
                    Turnover rate = (Leavers &divide; Average headcount) &times; 100.
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-table fa-fw"></i> Joiners ({{ $joiners->count() }})
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>@lang('common.serial')</th>
                                            <th>@lang('common.employee_name')</th>
                                            <th>Payroll No.</th>
                                            <th>Department</th>
                                            <th>Location</th>
                                            <th>Designation</th>
                                            <th>Date of Joining</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($joiners as $index => $record)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    @if ($record->employee)
                                                        {{ $record->employee->first_name }}
                                                        {{ $record->employee->last_name }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ $record->employee->payroll_number ?? 'N/A' }}</td>
                                                <td>{{ $record->employee->department->department_name ?? 'N/A' }}</td>
                                                <td>{{ $record->employee->workLocation->location_name ?? 'N/A' }}</td>
                                                <td>{{ $record->employee->designation->designation_name ?? 'N/A' }}</td>
                                                <td>{{ dateConvertDBtoForm($record->date_of_movement) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No joiners found for the selected period.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-table fa-fw"></i> Leavers ({{ $leavers->count() }})
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>@lang('common.serial')</th>
                                            <th>@lang('common.employee_name')</th>
                                            <th>Payroll No.</th>
                                            <th>Department</th>
                                            <th>Location</th>
                                            <th>Designation</th>
                                            <th>Date of Leaving</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($leavers as $index => $record)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    @if ($record->employee)
                                                        {{ $record->employee->first_name }}
                                                        {{ $record->employee->last_name }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ $record->employee->payroll_number ?? 'N/A' }}</td>
                                                <td>{{ $record->employee->department->department_name ?? 'N/A' }}</td>
                                                <td>{{ $record->employee->workLocation->location_name ?? 'N/A' }}</td>
                                                <td>{{ $record->employee->designation->designation_name ?? 'N/A' }}</td>
                                                <td>{{ dateConvertDBtoForm($record->date_of_movement) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No leavers found for the selected period.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    Select a date range and optional filters, then click <strong>Generate Report</strong>.
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
