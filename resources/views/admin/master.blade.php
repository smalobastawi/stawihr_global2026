<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

@php
    $front_setting = getFrontData();
@endphp
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        @isset($employeeInfo)
            <meta name="employee-id" content="{{ $employeeInfo->employee_id }}">
        @endisset
    @endauth
    <link rel="shortcut icon" href="{{ asset('storage/uploads/front/' . ($front_setting?->logo ?? '')) }}" type="image/x-icon" />

    <title>@yield('title')</title>
    @include('admin.components.styles')
</head>

<body class="fix-header" onload="addMenuClass()">

    @include('admin.layouts.pre-loader')

    <div id="wrapper container-fluid">

        @php
            $employeeInfo = Auth::user();
            if ($employeeInfo && $employeeInfo->employeeDetails) {
                $employeeInfo = $employeeInfo->employeeDetails;
            }
        @endphp
        <div>
            <!-- ============================================================== -->
            <!-- Topbar header - style you can find in pages.scss -->
            <!-- ============================================================== -->
            @include('admin.layouts.header')
            <!-- End Top Navigation -->
            <!-- ============================================================== -->
            <!-- Left Sidebar - style you can find in sidebar.scss  -->
            <!-- ============================================================== -->
            @include('admin.layouts.aside')


            <div id="page-wrapper">
                @if (session()->has('approval'))
                    <div class="alert alert-warning alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <i
                            class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('approval') }}</strong>
                    </div>
                @endif

                {{-- @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif --}}
                @yield('content')
            </div>
            <!-- /.container-fluid -->
            @include('admin.layouts.footer')
        </div>

    </div>
    @include('admin.components.scripts')

    @yield('page_scripts')

</body>

</html>
