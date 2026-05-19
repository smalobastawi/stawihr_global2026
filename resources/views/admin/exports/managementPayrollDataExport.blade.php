<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payroll Data exports</title>
    <meta charset="utf-8">
</head>
<style>
    table {

        margin: 0 0 40px 0;
        width: 100%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        display: table;
        border-collapse: collapse;
    }

    .printHead {

        width: 35%;
        margin: 0 auto;
    }

    table, td, th {

        border: 1px solid black;

    }

    td {
        padding: 5px;
    }

    th {
        padding: 5px;
    }

</style>
<body>

<div class="table-responsive">

    <table id="myTablePayrollDetails" class="table table-bordered">
        <thead>

        <tr class="tr_header">
            <th>@lang('common.serial')</th>
            <th>Month</th>
            <th>Payroll Number</th>
            <th>Name</th>
            <th>Basic</th>
            <th>H.A</th>
            <th>T.A</th>
            <th>Overtime</th>
            <th>Bonuses</th>
            <th>Airtime-non-taxable</th>
            <th>Pro-rata</th>
            <th>Public Holiday</th>
            <th>B/A</th>
            <th>Gross</th>
            <th>Lost Days</th>
            <th>Lost Days Amount</th>
            <th>Total Advance</th>
            <th>NHIF</th>
            <th>NSSF</th>
            <th>PAYE</th>
            <th>Net Pay</th>
            <th>Sign</th>

        </tr>
        </thead>
        <tbody>
        {!! $sl=null !!} <h4>Total records: {{count($dataExport)}} as at {{date('H:i d-M-Y')}}</h4>
        @foreach($dataExport AS $value)
            <tr class="">
                <td style="width: 100px;">{!! ++$sl !!}</td>
                <td>{!! $value->month_of_salary !!}</td>
                <td>{{$value->payroll_no}}</td>
                <td>{{$value->employee->first_name}} {{$value->employee->last_name}}</td>
                <td>{{$value->basic_salary}}</td>
                <td>{{$value->house_allowance}}</td>
                <td>{{$value->transport_allowance}}</td>
                <td>{{$value->total_overtime_amount}}</td>

                @php
                    $totalBonuses =0;
                    if (count($value->SalaryBonuses) >0 )
           foreach ($value->SalaryBonuses as $bonus1) {
              $totalBonuses = + $bonus1->amount_of_bonus;
           }
                @endphp
                <td>{{$totalBonuses}}</td>

                <td>{{$value->airtime_untaxed}}</td>
                <td>{{$value->pro_rata}}</td>
                <td>{{$value->public_holidays_pay}}</td>
                <td>{{$value->banking_allowance}}</td>
                <td>{{$value->gross_salary}}</td>
                <td>{{$value->total_absence}}</td>
                <td>{{$value->total_absence_amount}}</td>
                <td>{{$value->total_advances}}</td>
                <td>{{$value->nhifRate}}</td>
                <td>{{$value->nssf_amount}}</td>
                <td>{{$value->PAYE_tax}}</td>
                <td>{{$value->net_salary}}</td>
                <td></td>


            </tr>
        @endforeach

        </tbody>

    </table>
</div>

</body>
</html>
