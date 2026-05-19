@extends('admin.master')

@section('title')
   Migrate Attendance Data
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i
                                class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
            <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                {{--                <a href="{{ route('notice.create') }}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i class="fa fa-plus-circle" aria-hidden="true"></i> @lang('notice.add_new_notice')</a>--}}
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if(session()->has('success'))
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                    </button>
                                    <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                </div>
                            @endif
                            @if(session()->has('error'))
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                    </button>
                                    <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-4"></div>
                                <form action="{{route('saveMigrateAttendanceData')}}">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                    <div class="col-md-4">
                                        <select name="year" class="form-control">
                                            <option label="2020" value="2020">2020</option>
                                            <option label="2021" value="2021">2021</option>
                                            <option label="2022" value="2022">2022</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="submit" value="Proceed" class="form-control">
                                    </div>
                                </form>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
