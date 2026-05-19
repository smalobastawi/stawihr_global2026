@extends('admin.master')

@section('content')
@section('title', 'Projects List')

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <a href="{{ route('project.project-allocations.index') }}" class="btn btn-info pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-tasks" aria-hidden="true"></i> Project Allocations
            </a>
            <a href="{{ route('employee.project.create') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> Add New Project
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
                                        <th>Main Project</th>
                                        <th>Created By</th>
                                        <th>Status</th>
                                        <th style="text-align: center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach($projects as $project)
                                        <tr>
                                            <td>{!! ++$sl !!}</td>
                                            <td>{{ $project->name }}</td>
                                            <td>{{ $project->code }}</td>
                                            <td>{{ \Carbon\Carbon::parse($project->start_date)->format('d M, Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($project->end_date)->format('d M, Y') }}</td>
                                            <td>
                                                @if($project->main_project)
                                                    {{ $project->parent->name ?? 'N/A' }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $project->creator->email ?? 'Unknown' }}</td>
                                            <td>
                                                @if($project->status == 'active')
                                                    <span class="label label-info">Active</span>
                                                @elseif($project->status == 'inactive')
                                                    <span class="label label-warning">Inactive</span>
                                                @else
                                                    <span class="label label-success">Completed</span>
                                                @endif
                                            </td>
                                            <td style="width: 100px; text-align: center;">
                                                
                                                <a href="{{ route('employee.project.edit', $project->id) }}" class="btn btn-success btn-xs btnColor">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                </a>
												<a href="{!!route('employee.project.destroy',$project->id  )!!}" data-token="{!! csrf_token() !!}" data-id="{!! $project->id !!}" class="delete btn btn-danger btn-xs deleteBtn btnColor"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
												
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="pagination-wrapper">
                            {{-- {{ $projects->links() }} --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection