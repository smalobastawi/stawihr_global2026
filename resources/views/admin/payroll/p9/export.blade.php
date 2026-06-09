<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>P9 Download - {{ $taxationData['tax_year'] }} KRA Format</title>
    <link rel="stylesheet" href="{{ url('css/bootstrap.min.css') }}"/>

    <style>
        body {
            padding: 15px;
            font-size: 10px;
            text-align: justify;
        }
        tr {
            text-align: center;
        }
        .p9-table th, .p9-table td {
            font-size: 9px;
            padding: 3px 2px;
            vertical-align: middle;
            border: 1px solid #000;
        }
        .p9-table th {
            background-color: #d9d9d9;
        }
        .text-center {
            text-align: center;
        }
        .header-section {
            margin-bottom: 12px;
        }
        .col-md-4, .col-md-6, .col-md-12 {
            float: left;
            position: relative;
            min-height: 1px;
            padding-right: 10px;
            padding-left: 10px;
            display: block;
        }
        .col-md-4 { width: 33.33333333%; }
        .col-md-6 { width: 50%; }
        .col-md-12 { width: 100%; }
        .text-right { text-align: right; }
        .row { margin-right: -10px; margin-left: -10px; }
        .row::after, .row::before { display: table; content: " "; }
        .row::after { clear: both; }
    </style>
</head>

<body>

@include('admin.payroll.p9.partials.form_header')

<div class="row">
    @include('admin.payroll.p9.partials.form_table')
</div>

@include('admin.payroll.p9.partials.form_footer')

</body>
</html>
