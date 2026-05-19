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

<table>
    <tr>
        <td class="tr_header">EMPLOYER CODE</td>
    <td>----------</td>
</tr>
<tr>
    <td class="tr_header">EMPLOYER NAME</td>
<td>{{getFrontData()->company_title}}</td>
</tr>
<tr>
    <td class="tr_header" >MONTH OF CONTRIBUTION</td>
<td>{{$dataExport['currentMonth']}}</td>
</tr>


</table>


    <div class="table-responsive">
                            <table id="payrollReportsTable" class="table table-bordered">
                                <thead>
                                <tr class="tr_header">
                                  
                                    <th>Payroll Number</th>
                                    <th>Last Name</th>
                                    <th>First Name </th>
                                    <th>ID No</th>
                                    <th>NHIF No</th>
                                    <th>Amount</th>
                                   
                                </tr>
                                </thead>
                                <tbody>
                              
                                @foreach($dataExport['results'] AS $value)
                                    <tr>

                                        <td>{!! $value->payroll_no !!}</td>
                                        <td>{!! $value->employee->first_name ?? 0 !!} </td>
                                            <td>{{$value->employee->last_name ?? 0 }}</td>
                                        <td>{{$value->employee->finger_id ?? 0}}</td>
                                        <td>{{$value->nhif_no}}</td>
                                        <td>{!! $value->nhifRate ?? 0 !!}</td>
                                       
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align:right">Total</th>
                                    <th>{{$dataExport['totalNHIF']}}</th>
                                                 
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    
                    </body>
                    </html>
