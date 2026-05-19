@extends('admin.master')
@section('content')
@section('title')
    Leave Consumption Report
@endsection

<style>
    .employeeName {
        position: relative;
    }

    #employee_id-error {
        position: absolute;
        top: 66px;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .monthly-table th {
        background-color: #2c3b41;
        color: white;
        text-align: center;
        vertical-align: middle;
    }

    .monthly-table td {
        text-align: center;
        vertical-align: middle;
    }

    .total-row {
        font-weight: bold;
        background-color: #f4f4f4;
    }

    .month-highlight {
        background-color: #d9edf7;
    }

    .summary-card {
        background: linear-gradient(45deg, #2c3b41, #1a2529);
        color: white;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .summary-card .count {
        font-size: 24px;
        font-weight: bold;
    }

    .summary-card .label {
        font-size: 14px;
        opacity: 0.9;
    }

    /* Loading overlay */
    #loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        z-index: 9999;
        display: none;
        justify-content: center;
        align-items: center;
    }

    .loader {
        border: 5px solid #f3f3f3;
        border-radius: 50%;
        border-top: 5px solid #2c3b41;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<!-- Loading Overlay -->
<div id="loading-overlay">
    <div class="loader"></div>
</div>

<script>
    jQuery(function() {
        $("#monthlyLeaveReport").validate();
        // Initialize all select2 
        $('.select2').select2({
            placeholder: "Select option",
            allowClear: true
        });
    });

    // Show loading overlay when form is submitted
    function showLoading() {
        document.getElementById('loading-overlay').style.display = 'flex';
        return true;
    }
</script>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div id="searchBox">
            <form action="{{ route('leaveReport.monthlyLeaveConsumption') }}" method="POST" id="monthlyLeaveReport" class="form-horizontal">
@csrf

            <div class="form-group">
                <div class="col-sm-2">
                    <label for="financial_year_id">@lang('general.financial_year')<span class="validateRq">*</span></label>
                    <select name="financial_year_id" class="form-control financial_year_id select2" required>
                        <option value="">--- Select Financial Year ---</option>
                        @foreach ($financialYears as $financialYear)
                            <option value="{{ $financialYear->id }}" data-start="{{ $financialYear->start_date }}"
                                data-end="{{ $financialYear->end_date }}"
                                {{ request('financial_year_id', $selectedFinancialYear->id ?? '') == $financialYear->id ? 'selected' : '' }}>
                                {{ $financialYear->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="filtering" value="1">

                <div class="col-sm-2">
                    <label for="leave_type_id">@lang('leave.leave_type')</label>
                    <select name="leave_type_id" class="form-control select2">
                        <option value="all">All Leave Types</option>
                        @foreach ($leaveTypes as $leaveType)
                            <option value="{{ $leaveType->leave_type_id }}"
                                {{ request('leave_type_id') == $leaveType->leave_type_id ? 'selected' : '' }}>
                                {{ $leaveType->leave_type_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-2">
                    <label for="location">@lang('employee.location')</label>
                    <select name="location" class="form-control select2">
                        <option value="">All Locations</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->location_id }}"
                                {{ request('location') == $location->location_id ? 'selected' : '' }}>
                                {{ $location->location_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-2">
                    <label for="department">@lang('employee.department')</label>
                    <select name="department" class="form-control select2">
                        <option value="">All Departments</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->department_id }}"
                                {{ request('department') == $department->department_id ? 'selected' : '' }}>
                                {{ $department->department_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-2">
                    <label for="designation">@lang('employee.designation')</label>
                    <select name="designation" class="form-control select2">
                        <option value="">All Designations</option>
                        @foreach ($designations as $designation)
                            <option value="{{ $designation->designation_id }}"
                                {{ request('designation') == $designation->designation_id ? 'selected' : '' }}>
                                {{ $designation->designation_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="employee_name">@lang('employee.employee_name')</label>
                        <input type="text" name="employee_name" class="form-control"
                            value="{{ request('employee_name') }}" placeholder="Search by name...">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-2">
                    <div class="" style="margin-top: 25px;">
                        <label>
                            <input type="checkbox" name="show_zero" value="1"
                                {{ request('show_zero') ? 'checked' : '' }}>
                            Show employees with zero leave
                        </label>
                    </div>
                </div>

                <div class="col-sm-2">
                    <label>&nbsp;</label><br>
                    <button type="submit" id="filter" class="btn btn-info" value="Generate Report">
                        <i class="fa fa-search"></i> @lang('common.generate')
                    </button>
                    <a href="{{ route('leaveReport.monthlyLeaveConsumption') }}" class="btn btn-default" id="reset"
                        onclick="return showLoading()">
                        <i class="fa fa-refresh"></i> @lang('common.reset')
                    </a>
                </div>

                @if (request()->filled('filtering'))
                    <div class="col-sm-6 text-right">
                        <label>&nbsp;</label><br>
                        <a href="{{ route('downloadleaveReport.monthlyLeaveConsumption', request()->all()) }}"
                            class="btn btn-danger" onclick="return showLoading()">
                            <i class="fa fa-file-pdf-o"></i> PDF
                        </a>
                        <a href="{{ route('exportleaveReport.monthlyLeaveConsumption', request()->all()) }}"
                            class="btn btn-success" onclick="return showLoading()">
                            <i class="fa fa-file-excel-o"></i> Excel
                        </a>
                    </div>
                @endif
            </div>
            </form>
        </div>
        <hr>
    </div>

    @if (request()->filled('filtering'))
        <!-- Financial Year Info -->
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-info">
                    <i class="fa fa-calendar"></i>
                    <strong>Financial Year:</strong> {{ $selectedFinancialYear->name ?? 'N/A' }}
                    ({{ date('d/m/Y', strtotime($selectedFinancialYear->start_date)) }} -
                    {{ date('d/m/Y', strtotime($selectedFinancialYear->end_date)) }})
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row">
            <div class="col-sm-12">
                <div class="summary-card">
                    <div class="row">
                        <div class="col-md-2 col-sm-4">
                            <div class="label">Total Employees</div>
                            <div class="count">{{ count($reportData) }}</div>
                        </div>
                        <div class="col-md-2 col-sm-4">
                            <div class="label">Total Leave Days</div>
                            <div class="count">{{ array_sum($monthlyTotals) }}</div>
                        </div>
                        <div class="col-md-2 col-sm-4">
                            <div class="label">Average/Employee</div>
                            <div class="count">
                                {{ count($reportData) > 0 ? round(array_sum($monthlyTotals) / count($reportData), 1) : 0 }}
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="label">Employees with Leave</div>
                            <div class="count">
                                {{ count(array_filter($reportData, function ($item) {return $item['total'] > 0;})) }}
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="label">Financial Year</div>
                            <div class="count">{{ $selectedFinancialYear->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-table fa-fw"></i> @lang('leave.monthly_consumption_report') -
                        {{ $selectedFinancialYear->name ?? '' }}
                        <span class="pull-right">
                            <small><i class="fa fa-info-circle"></i> Only Annual Leave is counted</small>
                        </span>
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if (count($reportData) > 0)
                                <h4 class="text-right">
                                    <span class="label label-info">Total Records: {{ count($reportData) }}</span>
                                </h4>
                            @endif

                            <div class="table-responsive">
                                <table id="" class="table table-bordered table-striped monthly-table">
                                    <thead class="tr_header">
                                        <tr>
                                            <th rowspan="2" style="vertical-align: middle; width:50px;">
                                                @lang('common.serial')</th>
                                            <th rowspan="2" style="vertical-align: middle;">@lang('employee.employee_name')</th>
                                            <th rowspan="2" style="vertical-align: middle;">@lang('employee.payroll_number')</th>
                                            <th rowspan="2" style="vertical-align: middle;">@lang('employee.location')</th>
                                            <th rowspan="2" style="vertical-align: middle;">@lang('employee.department')</th>
                                            <th colspan="12" style="text-align: center;">
                                                {{ date('Y', strtotime($selectedFinancialYear->start_date)) }}-{{ date('Y', strtotime($selectedFinancialYear->end_date)) }}
                                            </th>
                                            <th rowspan="2" style="vertical-align: middle;">@lang('leave.total_days')</th>
                                        </tr>
                                        <tr>
                                            <th>Jan</th>
                                            <th>Feb</th>
                                            <th>Mar</th>
                                            <th>Apr</th>
                                            <th>May</th>
                                            <th>Jun</th>
                                            <th>Jul</th>
                                            <th>Aug</th>
                                            <th>Sep</th>
                                            <th>Oct</th>
                                            <th>Nov</th>
                                            <th>Dec</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($reportData as $index => $data)
                                            <tr class="{{ $data['total'] == 0 ? 'text-muted' : '' }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td style="text-align: left;">{{ $data['employee_name'] }}</td>
                                                <td>{{ $data['payroll_number'] }}</td>
                                                <td>{{ $data['location'] }}</td>
                                                <td>{{ $data['department'] }}</td>
                                                @for ($m = 1; $m <= 12; $m++)
                                                    <td
                                                        class="{{ $data['monthly'][$m] > 0 ? 'month-highlight' : '' }}">
                                                        {{ $data['monthly'][$m] > 0 ? $data['monthly'][$m] : '-' }}
                                                    </td>
                                                @endfor
                                                <td><strong>{{ $data['total'] > 0 ? $data['total'] : '-' }}</strong>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="18" class="text-center">@lang('common.no_data_available')</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot class="total-row">
                                        <tr>
                                            <td colspan="5" style="text-align: right;">
                                                <strong>@lang('common.total')</strong>
                                            </td>
                                            @for ($m = 1; $m <= 12; $m++)
                                                <td><strong>{{ $monthlyTotals[$m] > 0 ? $monthlyTotals[$m] : '-' }}</strong>
                                                </td>
                                            @endfor
                                            <td><strong>{{ array_sum($monthlyTotals) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Legend -->
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <span class="label label-info">■ Month with leave</span>
                                    <span class="label label-default">■ No leave</span>
                                </div>
                                <div class="col-md-6 text-right">
                                    <p class="text-muted">
                                        <i class="fa fa-calendar"></i> Generated on: {{ date('d/m/Y H:i:s') }}
                                    </p>
                                </div>
                            </div>

                        </div><!-- panel-body -->
                    </div><!-- panel-wrapper -->
                </div><!-- panel -->
            </div><!-- col-sm-12 -->
        </div><!-- row -->
    @else
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-table fa-fw"></i> @lang('leave.monthly_consumption_report')
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="alert alert-info text-center" style="margin-bottom: 0;">
                                <i class="fa fa-info-circle"></i> Please select a Financial Year and click "Generate"
                                to view the report.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div><!-- container-fluid -->

@endsection

@section('page_scripts')
<script>
    $(document).ready(function() {
        // Hide loading overlay when page is fully loaded
        $('#loading-overlay').hide();

        @if (request()->filled('filtering') && count($reportData) > 0)
            var selectedFY = '{{ $selectedFinancialYear->name ?? '' }}';
            var exportTitle = 'Monthly Leave Consumption Report - ' + selectedFY;
            var exportFilename = 'Monthly_Leave_Consumption_' + selectedFY.replace(/\s+/g, '_');

            // Destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable('#monthlyTable')) {
                $('#monthlyTable').DataTable().destroy();
            }

            $('#monthlyTable').DataTable({
                "pageLength": 100,
                "ordering": true,
                "order": [
                    [1, 'asc']
                ], // Sort by employee name
                "scrollX": true,
                "scrollCollapse": true,
                "dom": 'Bfrtip',
                "buttons": [{
                        extend: 'excelHtml5',
                        title: exportTitle,
                        filename: exportFilename,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        title: exportTitle,
                        filename: exportFilename,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: exportTitle,
                        filename: exportFilename,
                        orientation: 'landscape',
                        pageSize: 'A3',
                        exportOptions: {
                            columns: ':visible'
                        },
                        customize: function(doc) {
                            doc.styles.tableHeader.alignment = 'center';
                            doc.styles.tableBodyEven.alignment = 'center';
                            doc.styles.tableBodyOdd.alignment = 'center';
                        }
                    },
                    {
                        extend: 'print',
                        title: exportTitle,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    'pageLength'
                ],
                "language": {
                    "lengthMenu": "@lang('common.show') _MENU_ @lang('common.entries')",
                    "zeroRecords": "@lang('common.no_data_available')",
                    "info": "@lang('common.showing') _START_ @lang('common.to') _END_ @lang('common.of') _TOTAL_ @lang('common.entries')",
                    "infoEmpty": "@lang('common.no_entries')",
                    "search": "@lang('common.search')",
                    "paginate": {
                        "first": "@lang('pagination.first')",
                        "last": "@lang('pagination.last')",
                        "next": "@lang('pagination.next')",
                        "previous": "@lang('pagination.prev')"
                    }
                }
            });
        @endif

        // Initialize select2
        $('.select2').select2({
            placeholder: "Select option",
            allowClear: true
        });
    });
</script>
@endsection
