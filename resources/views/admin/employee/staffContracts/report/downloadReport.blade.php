<html lang="en">
<head>
    <title>@lang('salary_sheet.employee_payslip')</title>
    <meta charset="utf-8">
</head>
<style>
    body {
        font-size: 9px;
    }

    div.breakNow {
        page-break-inside: avoid;
        page-break-after: always;
    }

    table {
        margin: 0 0 40px 0;
        width: 100%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        display: table;
        border-spacing: 0px;
    }

    table, td, th {
        border: 1px solid #ddd;
    }

    td {
        padding: 3px;
    }

    th {
        padding: 3px;
    }

    .text-center {
        text-align: center;
    }

    .companyAddress {
        width: 367px;
        margin: 0 auto;
    }

    .container {
        padding-right: 15px;
        padding-left: 15px;
        margin-right: auto;
        margin-left: auto;
        width: 95%;
    }

    .row {
        margin-right: -15px;
        margin-left: -15px;
    }

    .col-md-6 {
        width: 49%;
        float: left;
        padding-right: .5%;
        padding-left: .5%;
    }

    .div1 {
        position: relative;
        margin-top: -4%;
    }

    .div2 {
        position: absolute;
        width: 100%;
        /*border: 1px solid;*/
        padding: 1px 12px 0px 12px;
    }

    .col-md-4 {
        width: 33.33333333%;
        float: left;
    }

    .clearFix {
        clear: both;
    }

    .padding {
        margin-bottom: 32px;
    }
</style>
<body>
<div class="container-fluid">
    <div class="row bg-title">

    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <h4>Total active users : {{count($results)}} as at {{date('H:i d-M-Y')}}</h4>
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if(session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        <div class="table-responsive">

                            <table id="myTable" class="table table-bordered">
                                <thead>

                                <tr class="tr_header">
                                    <th>@lang('common.serial')</th>
                                    <th>@lang('deduction.employee_name')</th>
                                    <th>Payroll Number</th>
                                    <th>Date joined</th>
                                    <th>Job category</th>
                                </tr>
                                </thead>
                                <tbody>
                                {!! $sl=null !!}
                                @foreach($results AS $value)
                                    <tr class="">
                                        <td style="width: 100px;">{!! ++$sl !!}</td>
                                        <td>{!! $value->first_name !!}{!!  $value->last_name!!}</td>

                                        <td>{{$value->payroll_number}}</td>
                                        <td>{{$value->date_of_joining}}</td>
                                        <td>{!! $value->jobCategory->name !!}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
