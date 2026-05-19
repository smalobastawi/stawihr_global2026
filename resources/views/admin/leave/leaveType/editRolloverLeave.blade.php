@extends('admin.master')
@section('content')
@section('title')
    Edit Rollover Leave
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class=" col-md-4 col-sm-4 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i
                                class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>
    <div class="row">
        @if(session()->has('messages'))
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
            </div>
        @endif
        @if(session()->has('messages'))
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>{{ session()->get('error') }}</strong>
            </div>
        @endif
        <form method="POST" action="{{route('storeRolloverLeave')}}">
            {{ csrf_field() }}
            <div class="col-md-4">
                <label class="form-group">Select Employee</label>
                <select  name="employee" class="form-control employee_id select2 required" required>

                    @foreach($employee as $employee)
                        <option value='{{ $employee->employee_id }}' class="form-control">{{ $employee->first_name }} {{$employee->last_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-group">Enter Number of days</label>
                <input type="number" name="no_of_days" class="form-control" min="1" required>
            </div>
            <div class="col-md-2">
                <button  type="submit" class="form-control btn-rounded bg-success">Save</button>
                <br>
                <button type="reset" class="form-control bg-info btn-rounded">Reset</button>
            </div>
            <div class="col-md-2"></div>
        </form>

    </div>
    <br><br>
    <div class="row">
        <div class="col-md-2">
            <a href="{{route('rolloverLeaves')}}"> <button class="form-control btn-warning"> Cancel </button></a>
        </div>
    </div>
</div>
@endsection