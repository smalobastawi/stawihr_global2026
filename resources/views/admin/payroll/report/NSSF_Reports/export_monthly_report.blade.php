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

<div class="container">

{{-- <table>
    <tr>
        <td class="tr_header">EMPLOYER CODE</td>
    <td>452350</td>
</tr>
<tr>
    <td class="tr_header">EMPLOYER NAME</td>
<td>STAWIHR</td>
</tr>
<tr>
    <td class="tr_header" >MONTH OF CONTRIBUTION</td>
<td>{{$dataExport['currentMonth']}}</td>
</tr>
    

</table> --}}


    <div class="table-responsive">
                            <table id="payrollReportsTable" class="table table-bordered">
                                <thead>
                                <tr class="tr_header">
                                    <th>PAYROLL NUMBER</th>
                                    <th>SURNAME</th>
                                    <th>OTHER NAMES</th>
                                    <th>ID NO</th>
                                    <th>KRA PIN</th>
                                    <th>NSSF NO</th>
                                    <th>GROSS PAY</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($dataExport['results'] AS $value)
                                    <tr>
                                        <td>{!! $value->employee->payroll_number !!}</td>
                                        <td>{!! $value->employee->last_name  ?? '' !!} </td>
                                        <td>{{$value->employee->first_name ?? '' }}</td>
                                        <td>{{$value->employee->national_id ?? ''}}</td>
                                        <td>{{$value->employee->KRA_Pin ?? ''}}</td>
                                        <td>{{$value->employee->NSSF_no ?? ''}}</td>
                                        <td>{!! $value->gross_salary ?? 0 !!}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="6" style="text-align:right">Total:</th>
                                    <th>{{ $dataExport['results']->sum('gross_salary') }}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    
                    </body>
                    </html>
