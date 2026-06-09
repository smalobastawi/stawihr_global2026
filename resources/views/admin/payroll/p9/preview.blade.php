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
    <title>P9 Preview - {{ $taxationData['tax_year'] }} KRA Format</title>
    <link rel="stylesheet" href="{{ url('css/bootstrap.min.css') }}"/>

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

<body>

@include('admin.payroll.p9.partials.form_header')

<div class="row">
    @include('admin.payroll.p9.partials.form_table')
</div>

@include('admin.payroll.p9.partials.form_footer')

<script src="{!! asset('admin_assets/bootstrap/dist/js/bootstrap.min.js') !!}"></script>
</body>
</html>
