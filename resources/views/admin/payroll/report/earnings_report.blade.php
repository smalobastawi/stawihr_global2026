@extends('admin.master')

@section('title')
    Earnings Report
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i
                                class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
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
                            <!-- Filter Form -->
                            <div class="filter-section">
                                <form id="earningsReportFilter" method="GET"
                                    action="{{ route('payroll.reports.earnings') }}">
                                    <div class="row filter-row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="payroll_period_id">Payroll Period</label>
                                                <select name="payroll_period_id" id="payroll_period_id" class="form-control select2" required>
                                                    <option value="">Select Payroll Period</option>
                                                    @foreach($payrollPeriods as $period)
                                                        <option value="{{ $period->id }}" {{ $selectedPeriodId == $period->id ? 'selected' : '' }}>
                                                            {{ $period->name }} ({{ $period->start_date->format('d M Y') }} - {{ $period->end_date->format('d M Y') }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Department</label>
                                                <select name="department_id" id="department_id_filter"
                                                    class="form-control select2">
                                                    <option value="">All Departments</option>
                                                    @foreach($departments as $department)
                                                        <option value="{{ $department->department_id }}" {{ request('department_id') == $department->department_id ? 'selected' : '' }}>
                                                            {{ $department->department_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Earning Type</label>
                                                <select name="earning_id" id="earning_id_filter"
                                                    class="form-control select2">
                                                    <option value="">All Earning Types</option>
                                                    @foreach($earningTypes as $earningType)
                                                        <option value="{{ $earningType->id }}" {{ request('earning_id') == $earningType->id ? 'selected' : '' }}>
                                                            {{ $earningType->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row filter-row">
                                        <div class="col-md-3">
                                            <div class="form-group" style="margin-top: 25px;">
                                                <button type="submit" class="btn btn-success btn-block">
                                                    <i class="fa fa-search"></i> Filter
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" style="margin-top: 25px;">
                                                <a href="{{ route('payroll.reports.earnings') }}" class="btn btn-warning btn-block">
                                                    <i class="fa fa-refresh"></i> Clear Filter
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" style="margin-top: 25px;">
                                                <button type="button" id="downloadExcelButton" class="btn btn-info btn-block" style="color: #fff">
                                                    <i class="fa fa-download fa-lg" aria-hidden="true"></i> Download Excel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="table-responsive">
                                <table id="earningsReportTable" class="table table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Employee Name</th>
                                            <th>Department</th>
                                            <th>Pay Period</th>
                                            <th>Earning Type</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($results) > 0)
                                            @foreach($results as $result)
                                                <tr>
                                                    <td>{{ $result->payrollRecord->employeePayroll->employee->fullName() }}</td>
                                                    <td>{{ $result->payrollRecord->employeePayroll->employee->department->department_name }}</td>
                                                    <td>{{ $result->payrollRecord->payrollPeriod->name }}</td>
                                                    <td>{{ $result->name }}</td>
                                                    <td>{{ number_format($result->amount, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" class="text-center">No data available.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" style="text-align:right">Total:</th>
                                            <th>{{ number_format($results->sum('amount'), 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script>
        $(document).ready(function() {
            // Initialize select2
            $('.select2').select2();

            // Initialize DataTables
            if ($('#earningsReportTable tbody tr').length > 1 || ($('#earningsReportTable tbody tr').length === 1 && $('#earningsReportTable tbody tr td').length > 1)) {
                $('#earningsReportTable').DataTable({
                    "paging": true,
                    "ordering": true,
                    "info": true,
                    "searching": true
                });
            }

            // Handle Download Excel button click
            $('#downloadExcelButton').on('click', function(e) {
                e.preventDefault();
                var form = $('#earningsReportFilter');
                var originalAction = form.attr('action');
                var originalMethod = form.attr('method');
                form.attr('action', '{{ route("payroll.reports.earnings.export") }}');
                form.attr('method', 'POST');
                form.append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
                form.append('<input type="hidden" name="payroll_period_id" value="' + $('#payroll_period_id').val() + '">');
                form.submit();
                form.attr('action', originalAction);
                form.attr('method', originalMethod);
                form.find('input[name="_token"]').remove();
                form.find('input[name="payroll_period_id"]').remove();
            });
        });
    </script>
@endsection