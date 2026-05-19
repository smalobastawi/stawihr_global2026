@extends('admin.master')

@section('title')
    {{ config('app.name') }}-Permissions
@endsection
@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i
                                class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>Permissions</li>
            </ol>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <a href="{{ route('permissions.create') }}"
               class="btn btn-success pull-right m-l-20 waves-effect waves-light"> <i class="fa fa-plus-circle"
                                                                                      aria-hidden="true"></i>Assign Permissions</a>

            <a href="{{ route('roles.create') }}" class="btn btn-success pull-right m-l-20 waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i>Add new Role</a>

            <a href="{{ route('roles.create') }}" class="btn btn-success pull-right m-l-20 waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i>Add new Role</a>

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
                                <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table id="" class="table table-bordered">
                                <thead>
                                <tr class="tr_header">
                                    <th>@lang('common.serial')</th>
                                    <th>Name</th>
                                    <th>@lang('common.action')</th>
                                </tr>
                                </thead>
                                <tbody>
                                {!! $sl=null !!}
                                @foreach($permissions AS $value)
                                    <tr class="{!! $value->salary_advance_id !!}">
                                        <td style="width: 100px;">{!! ++$sl !!}</td>
                                        <td>{!! $value->name !!}</td>

                                        <td style="width: 100px;">
                                            <a href="{!! route('permissions.edit',$value->id ) !!}"
                                               class="btn btn-success btn-xs btnColor">
                                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                            </a>
                                            <a href="{!!route('permissions.destroy',$value->id  )!!}"
                                               data-token="{!! csrf_token() !!}" data-id="{!! $value->id !!}"
                                               class="delete btn btn-danger btn-xs deleteBtn btnColor"><i
                                                        class="fa fa-trash-o" aria-hidden="true"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="5" style="text-align:right">Total:</th>
                                    <th></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

