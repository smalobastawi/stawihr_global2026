@extends('admin.master')

@section('title', getPageTitle() . ' | ' . config('app.name'))
@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-md-6">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                @foreach (breadCrumbs() as $item)
                <li class="breadcrumb-item"><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
            @endforeach
            </ol>
        </div>
        <div class="">
            {{--				<a href="{{route('calculatePaye')}}" class="btn btn-primary pull-right">Calculate Paye</a>--}}
            <a href="{{ route('generateSalarySheet.create') }}"  class="btn btn-success pull-right m-l-20  waves-effect waves-light"> <i class="fa fa-plus-circle" aria-hidden="true"></i> @lang('salary_sheet.generate_salary')</a>
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
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if(session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        @include('admin.payroll.payroll_calculator.header-links')
                       
                        <br>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page_scripts')
    <script>
        
    </script>
@endsection

