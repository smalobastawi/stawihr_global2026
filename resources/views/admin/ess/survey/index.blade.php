@extends('admin.master')

@section('title', 'Survey List')

@section('content')

    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor">
                        <a href="{{ url('dashboard') }}">
                            <i class="fa fa-home"></i>
                            @lang('dashboard.dashboard')
                        </a>
                    </li>
                    <li>@yield('title')</li>
                </ol>
            </div>

        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-table fa-fw"></i> @yield('title')
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if (session()->has('success'))
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <i
                                        class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                </div>
                            @endif
                            @if (session()->has('error'))
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <i
                                        class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table id="myTable" class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>@lang('common.serial')</th>
                                            <th>Survey</th>
                                            <th>Respond</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($results as $index => $value)
                                            <tr class="{{ $value->id }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $value->title }}</td>
                                                <td>
                                                    <a href="{{ $value->form_url }}" target="_blank"
                                                        class="btn btn-primary btn-xs" title="Open survey in new tab">
                                                        <i class="fa fa-external-link"></i> Take Survey
                                                    </a>
                                                </td>
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


@endsection
