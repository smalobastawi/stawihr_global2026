@php
    $front_setting = getFrontData();
@endphp
        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="{{ asset('storage/uploads/front/'.$front_setting->logo) }}" type="image/x-icon"/>
    <title>P9 Preview - 2025 KRA Format</title>
    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" href="{{url('css/bootstrap.min.css')}}"/>

    <style>
        body {
            padding: 20px;
            font-size: 11px;
            text-align: justify;
        }
        tr {
            text-align: center;
        }
        .p9-table th, .p9-table td {
            font-size: 10px;
            padding: 4px 2px;
            vertical-align: middle;
        }
        .p9-table th {
            background-color: #d9d9d9;
        }
        .text-center {
            text-align: center;
        }
        .header-section {
            margin-bottom: 15px;
        }
    </style>
</head>

<body style="padding: 15px; font-size: 11px; text-align: justify;">

<div class="header-section">
    <div class="row">
        <div class="col-md-4">APPENDIX 2A</div>
        <div class="col-md-4"></div>
        <div class="col-md-4 text-right">(P.9A)</div>
    </div>
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4 text-center">
            <img src="{{ asset('admin_assets/img/KRAlogo.png') }}" width="250px">
        </div>
        <div class="col-md-4"></div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center">
            <strong>ISO 9001:2015 CERTIFIED</strong><br>
            <strong>KENYA REVENUE AUTHORITY DOMESTIC TAXES DEPARTMENT</strong><br>
            <strong>TAX DEDUCTION CARD YEAR {{$taxationData['tax_year']}}</strong>
        </div>
    </div>
</div>

<div class="row header-section">
    <div class="col-md-6">
        <strong>Employer's Name:</strong> {{$companySettings ? $companySettings->legal_Name : '' }}<br>
        <strong>Employee's Main Name:</strong> {{$employeeDetails->first_name}}<br>
        <strong>Employee's Other Names:</strong> {{$employeeDetails->last_name}}
    </div>
    <div class="col-md-6 text-right">
        <strong>Employer's PIN:</strong> {{ $companySettings->KRA_PIN ?? '...........................' }}<br>
        <strong>Employee's PIN:</strong> {{ $employeeDetails->KRA_Pin ?? '...........................' }}<br>
        <strong>Employee's Payroll No:</strong> {{ $employeeDetails->payroll_number ?? '...........................' }}
    </div>
</div>

<div class="row">
    <table class="table table-bordered table-sm p9-table">
        <thead>
            <tr>
                <th rowspan="3" style="text-align: center; vertical-align: middle; width: 8%;">MONTH</th>
                <th rowspan="2" style="text-align: center;">Basic Salary</th>
                <th rowspan="2" style="text-align: center;">Benefits - NonCash</th>
                <th rowspan="2" style="text-align: center;">Value of Quarters</th>
                <th rowspan="2" style="text-align: center;">Total Gross Pay</th>
                <th colspan="3" style="text-align: center;">Defined Contribution Retirement Scheme</th>
                <th rowspan="2" style="text-align: center;">Affordable Housing Levy (AHL)</th>
                <th rowspan="2" style="text-align: center;">Social Health Insurance Fund (SHIF)</th>
                <th rowspan="2" style="text-align: center;">Post Retirement Medical Fund (PRMF)</th>
                <th rowspan="2" style="text-align: center;">Owner-Occupied Interest</th>
                <th rowspan="2" style="text-align: center;">Total Deductions (Lower of E + F + G + H + I)</th>
                <th rowspan="2" style="text-align: center;">Chargeable Pay (D - J)</th>
                <th rowspan="2" style="text-align: center;">Tax Charged</th>
                <th rowspan="2" style="text-align: center;">Personal Relief</th>
                <th rowspan="2" style="text-align: center;">Insurance Relief</th>
                <th rowspan="2" style="text-align: center;">PAYE Tax (L - M - N)</th>
            </tr>
            <tr>
                <th style="text-align: center;">Kshs.</th>
                <th style="text-align: center;">Kshs.</th>
                <th style="text-align: center;">Kshs.</th>
                <th style="text-align: center;">Kshs.</th>
                <th style="text-align: center;">Kshs.</th>
                <th style="text-align: center;">Kshs.</th>
                <th style="text-align: center;">Kshs.</th>
                <th style="text-align: center;">Kshs.</th>
                <th style="text-align: center;">Kshs.</th>
                <th style="text-align: center;">Kshs.</th>
                <th style="text-align: center;">Kshs.</th>
                <th style="text-align: center;">Kshs.</th>
                <th style="text-align: center;">Kshs.</th>
                <th style="text-align: center;">Kshs.</th>
                <th style="text-align: center;">Kshs.</th>
                <th style="text-align: center;">Kshs.</th>
            </tr>
            <tr>
                <th></th>
                <th style="text-align: center;">A</th>
                <th style="text-align: center;">B</th>
                <th style="text-align: center;">C</th>
                <th style="text-align: center;">D</th>
                <th colspan="3" style="text-align: center;">E</th>
                <th style="text-align: center;">F</th>
                <th style="text-align: center;">G</th>
                <th style="text-align: center;">H</th>
                <th style="text-align: center;">I</th>
                <th style="text-align: center;">J</th>
                <th style="text-align: center;">K</th>
                <th style="text-align: center;">L</th>
                <th style="text-align: center;">M</th>
                <th style="text-align: center;">N</th>
                <th style="text-align: center;">O</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th style="text-align: center; font-size: 9px;">E1<br>30% of A</th>
                <th style="text-align: center; font-size: 9px;">E2<br>Actual</th>
                <th style="text-align: center; font-size: 9px;">E3<br>Fixed<br>30,000 p.m</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($salaryDetails as $salary)
                <tr>
                    <th scope="row">{{ date('F', strtotime($salary['month'])) }}</th>
                    <td style="text-align: center;">{{ number_format($salary['A'], 2) }}</td>
                    <td style="text-align: center;">{{ number_format($salary['B'], 2) }}</td>
                    <td style="text-align: center;">{{ number_format($salary['C'], 2) }}</td>
                    <td style="text-align: center;">{{ number_format($salary['D'], 2) }}</td>
                    <td style="text-align: center;">{{ number_format($salary['E1'], 2) }}</td>
                    <td style="text-align: center;">{{ number_format($salary['E2'], 2) }}</td>
                    <td style="text-align: center;">{{ number_format($salary['E3'], 2) }}</td>
                    <td style="text-align: center;">{{ number_format($salary['F'], 2) }}</td>
                    <td style="text-align: center;">{{ number_format($salary['G'], 2) }}</td>
                    <td style="text-align: center;">{{ number_format($salary['H'], 2) }}</td>
                    <td style="text-align: center;">{{ number_format($salary['I'], 2) }}</td>
                    <td style="text-align: center;">{{ number_format($salary['J'], 2) }}</td>
                    <td style="text-align: center;">{{ number_format($salary['K'], 2) }}</td>
                    <td style="text-align: center;">{{ number_format($salary['L'], 2) }}</td>
                    <td style="text-align: center;">{{ number_format($salary['M'], 2) }}</td>
                    <td style="text-align: center;">{{ number_format($salary['N'], 2) }}</td>
                    <td style="text-align: center;">{{ number_format($salary['O'], 2) }}</td>
                </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #f2f2f2;">
                <th>TOTAL</th>
                <td style="text-align: center;">{{ number_format($totals['A'], 2) }}</td>
                <td style="text-align: center;">{{ number_format($totals['B'], 2) }}</td>
                <td style="text-align: center;">{{ number_format($totals['C'], 2) }}</td>
                <td style="text-align: center;">{{ number_format($totals['D'], 2) }}</td>
                <td style="text-align: center;">{{ number_format($totals['E1'], 2) }}</td>
                <td style="text-align: center;">{{ number_format($totals['E2'], 2) }}</td>
                <td style="text-align: center;">{{ number_format($totals['E3'], 2) }}</td>
                <td style="text-align: center;">{{ number_format($totals['F'], 2) }}</td>
                <td style="text-align: center;">{{ number_format($totals['G'], 2) }}</td>
                <td style="text-align: center;">{{ number_format($totals['H'], 2) }}</td>
                <td style="text-align: center;">{{ number_format($totals['I'], 2) }}</td>
                <td style="text-align: center;">{{ number_format($totals['J'], 2) }}</td>
                <td style="text-align: center;">{{ number_format($totals['K'], 2) }}</td>
                <td style="text-align: center;">{{ number_format($totals['L'], 2) }}</td>
                <td style="text-align: center;">{{ number_format($totals['M'], 2) }}</td>
                <td style="text-align: center;">{{ number_format($totals['N'], 2) }}</td>
                <td style="text-align: center;">{{ number_format($totals['O'], 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="row" style="margin-top: 15px;">
    <div class="col-md-6">
        <strong>To be completed by Employer at end of year</strong><br>
        <strong>TOTAL CHARGEABLE PAY (COL. K) Kshs.</strong> {{ number_format($taxationData['total_chargeable_pay'], 2) }}
    </div>
    <div class="col-md-6 text-right">
        <strong>TOTAL TAX (COL. O) Kshs.</strong> {{ number_format($taxationData['total_tax'], 2) }}
    </div>
</div>

<div class="row" style="margin-top: 20px; font-size: 9px;">
    <div class="col-md-6">
        <strong>IMPORTANT</strong><br>
        1. Use P9A<br>
        &nbsp;&nbsp;&nbsp;(a) For all liable employees and where director/employee received Benefits in addition to cash emoluments<br>
        &nbsp;&nbsp;&nbsp;(b) Where an employee is eligible to deduction on owner occupier interest.<br>
        &nbsp;&nbsp;&nbsp;(c) Where an employee contributes to a post retirement medical fund<br>
        2. (a) Deductible interest in respect of any month prior to December 2024 must not exceed Kshs. 25,000/= and commencing December 2024 must not exceed 30,000/=<br>
        &nbsp;&nbsp;&nbsp;(b) Deductible pension contribution in respect of any month prior to December 2024 must not exceed Kshs. 20,000/= and commencing December 2024 must not exceed 30,000/=<br>
        &nbsp;&nbsp;&nbsp;(c) Deductible contribution to a post retirement medical fund in respect of any month is effective from December 2024, must not exceed Kshs.15,000/=<br>
        &nbsp;&nbsp;&nbsp;(d) Deductible Contribution to the Social Health Insurance Fund (SHIF) and deductions made towards Affordable Housing Levy (AHL) are effective December 2024<br>
        &nbsp;&nbsp;&nbsp;(e) Personal Relief is Kshs. 2400 per Month or 28,800 per year<br>
        &nbsp;&nbsp;&nbsp;(f) Insurance Relief is 15% of the Premium up to a Maximum of Kshs. 5,000 per month or Kshs. 60,000 per year
    </div>
    <div class="col-md-6">
        <strong>c) Attach</strong><br>
        (i) Photostat copy of interest certificate and statement of account from the Financial Institution<br>
        (ii) The DECLARATION duly signed by the employee.
    </div>
</div>

<script src="{!! asset('admin_assets/bootstrap/dist/js/bootstrap.min.js') !!}"></script>
</body>
</html>
