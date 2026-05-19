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
    <div class="table-responsive">
                            <table id="payrollReportsTable" class="table table-bordered">
                                <thead>
                                <tr class="tr_header">
                                    <th>ID NUMBER</th>
                                    <th>NAMES</th>
                                    <th>KRA PIN</th>
                                    <th>GROSS SALARY</th>
                                    <th>AHL Contribution</th>
                                </tr>
                                </thead>
                                <tbody>
                              
                                @foreach($dataExport['results'] AS $value)
                                    <tr>
                                        <td>{{$value->employee->national_id ?? 0}}</td>
                                        <td>{!! $value->employee->first_name ?? 0 !!} {{$value->employee->last_name ?? 0 }}</td>
                                        <td>{!! $value->employee->KRA_Pin !!}</td>
                                        <td>{{$value->gross_salary}}</td>
                                        <td>{{$value->housing_levy}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" style="text-align: right; font-weight: bold;">Total AHL Contribution</td>
                                        <td style="font-weight: bold;">{{$dataExport['totalAHL']}}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    
                    </body>
                    </html>
