@extends('admin.master')

@section('title')
   StawiHR - PAYE Report
@endsection

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('payroll.dashboard') }}">Payroll</a></li>
                <li><a href="{{ route('reports.paye') }}">PAYE Reports</a></li>
                <li>Report</li>
            </ol>
        </div>
        <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
            <a href="{{ route('reports.paye') }}" class="btn btn-default pull-right m-l-20 waves-effect waves-light">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> PAYE Report - {{ $period->name }}</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @include('admin.partials.company_report_header')

                        <div class="row m-b-20">
                            <div class="col-md-4">
                                <p><strong>Period:</strong> {{ $period->name }}</p>
                                <p><strong>Date Range:</strong> {{ $period->start_date->format('d M Y') }} - {{ $period->end_date->format('d M Y') }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Employees:</strong> {{ $totals['employees'] }}</p>
                                <p><strong>Total Gross:</strong> KES {{ number_format($totals['total_gross'], 2) }}</p>
                                <p><strong>Total PAYE:</strong> KES {{ number_format($totals['total_paye'], 2) }}</p>
                            </div>
                            <div class="col-md-4 text-right">
                                <form action="{{ route('reports.paye.generate') }}" method="POST" class="form-inline" style="display: inline-block;">
                                    @csrf
                                    <input type="hidden" name="period_id" value="{{ $period->id }}">
                                    <div class="btn-group">
                                        <button type="submit" name="format" value="excel" class="btn btn-success">
                                            <i class="fa fa-file-excel-o"></i> Excel
                                        </button>
                                        <button type="submit" name="format" value="csv" class="btn btn-info">
                                            <i class="fa fa-file-text-o"></i> CSV
                                        </button>
                                        <button type="submit" name="format" value="pdf" class="btn btn-danger">
                                            <i class="fa fa-file-pdf-o"></i> PDF
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="payeReportTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr class="tr_header">
                                        <th>#</th>
                                        <th>Employee Code</th>
                                        <th>Employee Name</th>
                                        <th>KRA PIN</th>
                                        <th>Basic Salary</th>
                                        <th>Gross Salary</th>
                                        <th>NSSF</th>
                                        <th>SHIF</th>
                                        <th>Housing Levy</th>
                                        <th>Pension</th>
                                        <th>PAYE Tax</th>
                                        <th>Net Salary</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($records as $index => $record)
                                        @php
                                            $employee = $record->employee;
                                            $employeePayroll = $record->employeePayroll;
                                            $kraPin = $employeePayroll->kra_pin ?? $employee->KRA_Pin ?? '';
                                            $employeeName = trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? ''));
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $employee->staff_no ?? $employeePayroll->payroll_number ?? '' }}</td>
                                            <td>{{ $employeeName }}</td>
                                            <td>{{ $kraPin }}</td>
                                            <td class="text-right">{{ number_format($record->basic_salary ?? 0, 2) }}</td>
                                            <td class="text-right">{{ number_format($record->gross_salary ?? 0, 2) }}</td>
                                            <td class="text-right">{{ number_format($record->nssf_contribution ?? 0, 2) }}</td>
                                            <td class="text-right">{{ number_format($record->shif_contribution ?? 0, 2) }}</td>
                                            <td class="text-right">{{ number_format($record->housing_levy ?? 0, 2) }}</td>
                                            <td class="text-right">{{ number_format($record->pension_contribution ?? 0, 2) }}</td>
                                            <td class="text-right"><strong>{{ number_format($record->paye_tax ?? 0, 2) }}</strong></td>
                                            <td class="text-right">{{ number_format($record->net_salary ?? 0, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12" class="text-center">No PAYE records found for this period.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($records->isNotEmpty())
                                    <tfoot>
                                        <tr class="tr_header">
                                            <th colspan="4" class="text-right">Totals:</th>
                                            <th class="text-right">{{ number_format($records->sum('basic_salary'), 2) }}</th>
                                            <th class="text-right">{{ number_format($totals['total_gross'], 2) }}</th>
                                            <th class="text-right">{{ number_format($records->sum('nssf_contribution'), 2) }}</th>
                                            <th class="text-right">{{ number_format($records->sum('shif_contribution'), 2) }}</th>
                                            <th class="text-right">{{ number_format($records->sum('housing_levy'), 2) }}</th>
                                            <th class="text-right">{{ number_format($records->sum('pension_contribution'), 2) }}</th>
                                            <th class="text-right">{{ number_format($totals['total_paye'], 2) }}</th>
                                            <th class="text-right">{{ number_format($records->sum('net_salary'), 2) }}</th>
                                        </tr>
                                    </tfoot>
                                @endif
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
        $('#payeReportTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            pageLength: 25
        });
    });
</script>
@endsection
