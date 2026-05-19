@extends('admin.master')

@section('content')
@section('title', 'Programs List')

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <a href="{{ route('employee.program.create') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> Add New Program
            </a>
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
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Main Program</th>
                                        <th>Created By</th>
                                        <th>Status</th>
                                        <th style="text-align: center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach($programs as $program)
                                        <tr>
                                            <td>{!! ++$sl !!}</td>
                                            <td>{{ $program->name }}</td>
                                            <td>{{ $program->code }}</td>
                                            <td>{{ \Carbon\Carbon::parse($program->start_date)->format('d M, Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($program->end_date)->format('d M, Y') }}</td>
                                            <td>
                                                @if($program->parent)
                                                    {{ $program->parent->name ?? 'N/A' }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $program->creator->email ?? 'Unknown' }}</td>
                                            <td>
                                                @if($program->status == 'active')
                                                    <span class="label label-info">Active</span>
                                                @elseif($program->status == 'inactive')
                                                    <span class="label label-warning">Inactive</span>
                                                @else
                                                    <span class="label label-success">Completed</span>
                                                @endif
                                            </td>
                                            <td style="width: 100px; text-align: center;">
                                                
                                                <a href="{{ route('employee.program.edit', $program->id) }}" class="btn btn-success btn-xs btnColor">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                </a>
												<a href="{!!route('employee.program.destroy',$program->id  )!!}" data-token="{!! csrf_token() !!}" data-id="{!! $program->id !!}" class="delete btn btn-danger btn-xs deleteBtn btnColor"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
												
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="pagination-wrapper">
                            {{-- {{ $programs->links() }} --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
