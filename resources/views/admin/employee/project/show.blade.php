@extends('admin.master')

@section('content')
@section('title', 'Project Details')

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                <li><a href="{{ route('employee.project.index') }}">Projects List</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <a href="{{ route('employee.project.index') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-list-ul" aria-hidden="true"></i> View Projects
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i> Project Details</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> {{ $project->name }}</p>
                                <p><strong>Code:</strong> {{ $project->code }}</p>
                                <p><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($project->start_date)->format('d M, Y') }}</p>
                                <p><strong>End Date:</strong> {{ \Carbon\Carbon::parse($project->end_date)->format('d M, Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Main Project:</strong> 
                                    @if($project->main_project)
                                        {{ $project->parent->name ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                                <p><strong>Created By:</strong> {{ $project->creator->email ?? 'Unknown' }}</p>
                                <p><strong>Status:</strong> 
                                    @if($project->status == 'active')
                                        <span class="label label-info">Active</span>
                                    @elseif($project->status == 'inactive')
                                        <span class="label label-warning">Inactive</span>
                                    @else
                                        <span class="label label-success">Completed</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
