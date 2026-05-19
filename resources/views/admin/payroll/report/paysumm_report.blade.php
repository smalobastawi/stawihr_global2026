@extends('admin.master')

@section('title')
    Paysumm Report
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
                                <form id="paysummReportFilter" method="GET"
                                    action="{{ route('payroll.reports.paysumm') }}">
                                    <div class="row filter-row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="month">Month</label>
                                                <input type="month" name="month" class="form-control" value="{{ request('month') ?? date('Y-m') }}">
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
                                                <a href="{{ route('payroll.reports.paysumm') }}" class="btn btn-warning btn-block">
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
                                <table id="paysummReportTable" class="table table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Employee Code</th>
                                            <th>Employee Surname</th>
                                            <th>Employee First name</th>
                                            <th>Employee Second name</th>
                                            <th>Job Title</th>
                                            <th>Locations (HA)</th>
                                            <th>Sub_Programs (HA)</th>
                                            <th>Basic Income (Earning)</th>
                                            <th>Casuals (Earning)</th>
                                            <th>Acting Allowance (Earning)</th>
                                            <th>Arrears (Earning)</th>
                                            <th>Early Morning/Late Evening (Earning)</th>
                                            <th>Leave Days Pay (Earning)</th>
                                            <th>Notice Pay (Earning)</th>
                                            <th>OT Totals</th>
                                            <th>Service Pay (Earning)</th>
                                            <th>Teachers Allowance (Earning)</th>
                                            <th>Weekend Allowances (Earning)</th>
                                            <th>Other Allowance (Earning)</th>
                                            <th>Annual Bonus(Earning)</th>
                                            <th>Earning Total</th>
                                            <th>Unpaid</th>
                                            <th>Effective Earning</th>
                                            <th>Industrial Training Levy (CompanyContribution)</th>
                                            <th>NSSF Tier I (CompanyContribution)</th>
                                            <th>NSSF Tier II (CompanyContribution)</th>
                                            <th>TOTAL NSSF (CompanyContribution)</th>
                                            <th>Affordable Housing Levy (Company Contribution)</th>
                                            <th>Pension(Employer)</th>
                                            <th>CompanyContribution Total</th>
                                            <th>Total Cost</th>
                                            <th>PAYE</th>
                                            <th>NSSF Tier I (Deduction)</th>
                                            <th>NSSF Tier II (Deduction)</th>
                                            <th>TOTAL NSSF (Deduction)</th>
                                            <th>SHIF</th>
                                            <th>Affordable Housing Levy(Deduction)</th>
                                            <th>JUBILEE</th>
                                            <th>HERITAGE</th>
                                            <th>ICEA</th>
                                            <th>Helb (Deduction)</th>
                                            <th>Salary Advance (Deduction)</th>
                                            <th>Pension(Employee)</th>
                                            <th>Other Deductions (Deduction)</th>
                                            <th>Kimitsu Sacco (Deduction)</th>
                                            <th>Shofco Sacco (Deduction)</th>
                                            <th>Total Deductions</th>
                                            <th>NetPay</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($results) > 0)
                                            @foreach($results as $result)
                                                <tr>
                                                    <td>{{ $result['employee_code'] }}</td>
                                                    <td>{{ $result['employee_surname'] }}</td>
                                                    <td>{{ $result['employee_first_name'] }}</td>
                                                    <td>{{ $result['employee_second_name'] }}</td>
                                                    <td>{{ $result['job_title'] }}</td>
                                                    <td>{{ $result['location'] }}</td>
                                                    <td>{{ $result['sub_program'] }}</td>
                                                    <td>{{ number_format($result['Basic Income'], 2) }}</td>
                                                    <td>{{ number_format($result['Casuals'], 2) }}</td>
                                                    <td>{{ number_format($result['Acting Allowance'], 2) }}</td>
                                                    <td>{{ number_format($result['Arrears'], 2) }}</td>
                                                    <td>{{ number_format($result['Early Morning/Late Evening'], 2) }}</td>
                                                    <td>{{ number_format($result['Leave Days Pay'], 2) }}</td>
                                                    <td>{{ number_format($result['Notice Pay'], 2) }}</td>
                                                    <td>{{ number_format($result['OT Totals'], 2) }}</td>
                                                    <td>{{ number_format($result['Service Pay'], 2) }}</td>
                                                    <td>{{ number_format($result['Teachers Allowance'], 2) }}</td>
                                                    <td>{{ number_format($result['Weekend Allowances'], 2) }}</td>
                                                    <td>{{ number_format($result['Other Allowance'], 2) }}</td>
                                                    <td>{{ number_format($result['Annual Bonus'], 2) }}</td>
                                                    <td>{{ number_format($result['Earning Total'], 2) }}</td>
                                                    <td>{{ number_format($result['Unpaid'], 2) }}</td>
                                                    <td>{{ number_format($result['Effective Earning'], 2) }}</td>
                                                    <td>{{ number_format($result['Industrial Training Levy (CompanyContribution)'], 2) }}</td>
                                                    <td>{{ number_format($result['NSSF Tier I (CompanyContribution)'], 2) }}</td>
                                                    <td>{{ number_format($result['NSSF Tier II (CompanyContribution)'], 2) }}</td>
                                                    <td>{{ number_format($result['TOTAL NSSF (CompanyContribution)'], 2) }}</td>
                                                    <td>{{ number_format($result['Affordable Housing Levy (Company Contribution)'], 2) }}</td>
                                                    <td>{{ number_format($result['Pension(Employer)'], 2) }}</td>
                                                    <td>{{ number_format($result['CompanyContribution Total'], 2) }}</td>
                                                    <td>{{ number_format($result['Total Cost'], 2) }}</td>
                                                    <td>{{ number_format($result['PAYE'], 2) }}</td>
                                                    <td>{{ number_format($result['NSSF Tier I (Deduction)'], 2) }}</td>
                                                    <td>{{ number_format($result['NSSF Tier II (Deduction)'], 2) }}</td>
                                                    <td>{{ number_format($result['TOTAL NSSF (Deduction)'], 2) }}</td>
                                                    <td>{{ number_format($result['SHIF'], 2) }}</td>
                                                    <td>{{ number_format($result['Affordable Housing Levy(Deduction)'], 2) }}</td>
                                                    <td>{{ number_format($result['JUBILEE'], 2) }}</td>
                                                    <td>{{ number_format($result['HERITAGE'], 2) }}</td>
                                                    <td>{{ number_format($result['ICEA'], 2) }}</td>
                                                    <td>{{ number_format($result['Helb (Deduction)'], 2) }}</td>
                                                    <td>{{ number_format($result['Salary Advance (Deduction)'], 2) }}</td>
                                                    <td>{{ number_format($result['Pension(Employee)'], 2) }}</td>
                                                    <td>{{ number_format($result['Other Deductions (Deduction)'], 2) }}</td>
                                                    <td>{{ number_format($result['Kimitsu Sacco (Deduction)'], 2) }}</td>
                                                    <td>{{ number_format($result['Shofco Sacco (Deduction)'], 2) }}</td>
                                                    <td>{{ number_format($result['Total Deductions'], 2) }}</td>
                                                    <td>{{ number_format($result['NetPay'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="47" class="text-center">No data available.</td>
                                            </tr>
                                        @endif
                                    </tbody>
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
            $('.select2').select2();

            // Initialize DataTables
            if ($('#paysummReportTable tbody tr').length > 1 || ($('#paysummReportTable tbody tr').length === 1 && $('#paysummReportTable tbody tr td').length > 1)) {
                $('#paysummReportTable').DataTable({
                    "paging": true,
                    "ordering": true,
                    "info": true,
                    "searching": true,
                    "scrollX": true // Enable horizontal scrolling
                });
            }

            // Handle Download Excel button click
            $('#downloadExcelButton').on('click', function(e) {
                e.preventDefault();
                var form = $('#paysummReportFilter');
                var originalAction = form.attr('action');
                var originalMethod = form.attr('method');
                form.attr('action', '{{ route("payroll.reports.paysumm.export") }}');
                form.attr('method', 'POST');
                form.append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
                form.submit();
                form.attr('action', originalAction);
                form.attr('method', originalMethod);
                form.find('input[name="_token"]').remove();
            });
        });
    </script>
@endsection
