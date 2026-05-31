@extends('admin.master')

@section('title')
   StawiHR - P10 Monthly Return
@endsection

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('payroll.dashboard') }}">Payroll</a></li>
                <li><a href="{{ route('reports.paye') }}">PAYE Reports</a></li>
                <li>P10 Return</li>
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
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> P10 Monthly PAYE Return - {{ $period->name }}</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="row m-b-20">
                            <div class="col-md-4">
                                <p><strong>Period:</strong> {{ $period->name }}</p>
                                <p><strong>Date Range:</strong> {{ $period->start_date->format('d M Y') }} - {{ $period->end_date->format('d M Y') }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Employees:</strong> {{ $summary['total_employees'] }}</p>
                                <p><strong>Total Gross:</strong> KES {{ number_format($summary['total_gross_salary'], 2) }}</p>
                                <p><strong>Total PAYE:</strong> KES {{ number_format($summary['total_paye'], 2) }}</p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="{{ route('reports.paye.p10', ['period' => $period->id, 'format' => 'excel']) }}" class="btn btn-success">
                                    <i class="fa fa-file-excel-o"></i> Download Excel
                                </a>
                                <button type="button" class="btn btn-danger" onclick="window.print()">
                                    <i class="fa fa-print"></i> Print / PDF
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="p10ReportTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr class="tr_header">
                                        <th>#</th>
                                        <th>PIN of Employee</th>
                                        <th>Name of Employee</th>
                                        <th>Resident Status</th>
                                        <th>Type of Employee</th>
                                        <th>Basic Salary</th>
                                        <th>Housing Allowance</th>
                                        <th>Transport Allowance</th>
                                        <th>Over Time Allowance</th>
                                        <th>Other Allowance</th>
                                        <th>SHIF (J)</th>
                                        <th>AHL (N)</th>
                                        <th>Pension (K)</th>
                                        <th>Insurance Relief (S)</th>
                                        <th>PAYE Tax</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($p10Rows as $index => $row)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $row['PIN of Employee'] }}</td>
                                            <td>{{ $row['Name of Employee'] }}</td>
                                            <td>{{ $row['Resident Status'] }}</td>
                                            <td>{{ $row['Type of Employee'] }}</td>
                                            <td class="text-right">{{ number_format($row['Basic Salary'], 2) }}</td>
                                            <td class="text-right">{{ number_format($row['Housing Allowance'], 2) }}</td>
                                            <td class="text-right">{{ number_format($row['Transport Allowance'], 2) }}</td>
                                            <td class="text-right">{{ number_format($row['Over Time Allowance'], 2) }}</td>
                                            <td class="text-right">{{ number_format($row['Other Allowance'], 2) }}</td>
                                            <td class="text-right">{{ number_format($row['Social Health Insurance Fund (J)'], 2) }}</td>
                                            <td class="text-right">{{ number_format($row['Affordable Housing Levy (N)'], 2) }}</td>
                                            <td class="text-right">{{ number_format($row['Actual Pension Contribution (K)'], 2) }}</td>
                                            <td class="text-right">{{ number_format($row['Amount of Insurance Relief (Ksh) (S)'], 2) }}</td>
                                            <td class="text-right"><strong>{{ number_format($row['PAYE Tax'], 2) }}</strong></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="15" class="text-center">No PAYE records found for this period.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($p10Rows->isNotEmpty())
                                    <tfoot>
                                        <tr class="tr_header">
                                            <th colspan="5" class="text-right">Totals:</th>
                                            <th class="text-right">{{ number_format($p10Rows->sum('Basic Salary'), 2) }}</th>
                                            <th class="text-right">{{ number_format($p10Rows->sum('Housing Allowance'), 2) }}</th>
                                            <th class="text-right">{{ number_format($p10Rows->sum('Transport Allowance'), 2) }}</th>
                                            <th class="text-right">{{ number_format($p10Rows->sum('Over Time Allowance'), 2) }}</th>
                                            <th class="text-right">{{ number_format($p10Rows->sum('Other Allowance'), 2) }}</th>
                                            <th class="text-right">{{ number_format($p10Rows->sum('Social Health Insurance Fund (J)'), 2) }}</th>
                                            <th class="text-right">{{ number_format($p10Rows->sum('Affordable Housing Levy (N)'), 2) }}</th>
                                            <th class="text-right">{{ number_format($p10Rows->sum('Actual Pension Contribution (K)'), 2) }}</th>
                                            <th class="text-right">{{ number_format($p10Rows->sum('Amount of Insurance Relief (Ksh) (S)'), 2) }}</th>
                                            <th class="text-right">{{ number_format($summary['total_paye'], 2) }}</th>
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
        $('#p10ReportTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            pageLength: 25,
            scrollX: true
        });
    });
</script>
@endsection
