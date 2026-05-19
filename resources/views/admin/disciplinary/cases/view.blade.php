@extends('admin.master')
@section('content')
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> --}}
@section('title')
    Case Details
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        @if (!isset($ess))
            <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                <a href="{{ route('disciplinary.cases.create') }}"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> Add New
                </a>
                <a href="{{ route('disciplinary.cases.closed') }}"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> Closed
                </a>
            </div>
        @endif
    </div>
    <div class="row">
        <div class="col-md-offset-2 col-md-6">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                    @foreach ($errors->all() as $error)
                        <strong>{!! $error !!}</strong><br>
                    @endforeach
                </div>
            @endif
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                </div>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">

                        <div class="row cleafix">

                            <div class="col-md-6 float-left">
                                <h4 class="text-dark">Case Details</h4>
                            </div>
                            <div class="col-md-6 float-right">
                                <h4>
                                    @if (!isset($ess))
                                        @if ($case->status != DisciplinaryCaseStatus::CLOSED)
                                            <button class="btn btn-success" data-toggle="modal"
                                                data-target="#markClosed">Mark as closed</button>
                                        @elseif($case->status == DisciplinaryCaseStatus::CLOSED)
                                            <button class="btn btn-danger" data-toggle="modal"
                                                data-target="#reopen">Re-Open</button>
                                        @endif
                                </h4>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Case Number</th>
                                        <td>{{ $case->case_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Category</th>
                                        <td>{{ $case->category->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date of Incident</th>
                                        <td>{{ $case->date_of_incident }}</td>
                                    </tr>
                                    <tr>
                                        <th>Address/Location</th>
                                        <td>{{ $case->location }}</td>
                                    </tr>
                                    <tr>
                                        <th>Location</th>
                                        <td>
                                            @if ($case->officeLocation)
                                                {{ $case->officeLocation->location_name }}
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Employee</th>
                                        <td>{{ $case->employee->first_name }} {{ $case->employee->last_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Reporter</th>
                                        <td>{{ $case->reporter->first_name }} {{ $case->reporter->last_name }}</td>
                                    </tr>
                                    <tr>
                                        <th
                                            @if ($case->status != DisciplinaryCaseStatus::CLOSED) class="bg-danger" @else class="bg-success" @endif>
                                            Status</th>
                                        <td
                                            @if ($case->status != DisciplinaryCaseStatus::CLOSED) class="bg-danger" @else class="bg-success" @endif>
                                            {{ DisciplinaryCaseStatus::getName($case->status) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date of Report</th>
                                        <td>{{ $case->date_of_report }}</td>
                                    </tr>
                                    <tr>
                                        <th>Assigned Officer</th>
                                        <td>{{ optional($case->assignedOfficer)->first_name }}
                                            {{ optional($case->assignedOfficer)->last_name }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div
                            class="row border  @if ($case->status != DisciplinaryCaseStatus::CLOSED) border-danger @else border-success @endif rounded">
                            <div class="col-md-12">
                                <h4>Description</h4>
                                <p>{{ $case->description }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h4>Attachment</h4>
                                @if ($case->attachment)
                                    <a href="{{ asset('storage/' . $case->attachment) }}" target="_blank">Download
                                        Attachment</a>
                                @else
                                    <p>No attachment available.</p>
                                @endif
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            @if (!isset($ess))
                                <button type="button" class="btn btn-primary col-sm-3" data-toggle="modal"
                                    data-target="#addAction">
                                    Add Action
                                </button>
                            @endif

                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h4>Actions</h4>
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Action Type</th>
                                            <th>Remarks</th>
                                            <th>Action Date</th>
                                            <th>Status</th>
                                            <th>Attachment</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($case->actions as $action)
                                            <tr>
                                                <td>{{ DisciplinaryActionTypes::getName($action->action_type) }}</td>
                                                <td>{{ $action->remarks }}</td>
                                                <td>{{ $action->action_date }}</td>
                                                <td>{{ DisciplinaryCaseStatus::getName($action->status) }}</td>
                                                <td>
                                                    @if ($action->attachment)
                                                        <a href="{{ asset('storage/' . $action->attachment) }}"
                                                            target="_blank">Download Attachment</a>
                                                    @else
                                                        <p>No attachment available.</p>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if (!isset($ess))
                                @include('admin.disciplinary.cases.modals.add-action')
                                @include('admin.disciplinary.cases.modals.mark-closed')
                                @include('admin.disciplinary.cases.modals.reopen')
                                <div class="form-actions">
                                    <a href="{{ route('disciplinary.cases.index') }}" class="btn btn-primary">
                                        <i class="fa fa-arrow-left"></i> Back to List
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
