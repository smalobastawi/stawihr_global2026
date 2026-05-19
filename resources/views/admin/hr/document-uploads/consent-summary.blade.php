@extends('admin.master')
@section('content')
@section('title')
Document Consent Summary
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('documents-upload.index') }}">Documents</a></li>
                <li class="breadcrumb-item active">Consent Summary</li>
            </ol>
        </div>
        <div class="col-lg-12 col-sm-8 col-md-6 col-xs-6">
            <a href="{{ route('documents-upload.index') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> Back to Documents
            </a>
        </div>
    </div>

    <!-- Overall Statistics -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-chart-bar fa-fw"></i> Overall Statistics
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="alert alert-info">
                                <h3>{{ $employees->count() }}</h3>
                                <p>Total Employees</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="alert alert-warning">
                                <h3>{{ $documents->count() }}</h3>
                                <p>Total Documents</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="alert alert-success">
                                @php
                                    $totalConsents = 0;
                                    $totalExpected = $employees->count() * $documents->count();
                                    foreach ($summary as $stats) {
                                        $totalConsents += $stats['stats']['consented'];
                                    }
                                    $overallPercentage = $totalExpected > 0 ? round(($totalConsents / $totalExpected) * 100, 2) : 0;
                                @endphp
                                <h3>{{ $totalConsents }}</h3>
                                <p>Total Consents</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="alert alert-primary">
                                <h3>{{ $overallPercentage }}%</h3>
                                <p>Overall Completion</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents Consent Summary -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-table fa-fw"></i> Document-wise Consent Summary
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr class="tr_header">
                                        <th>#</th>
                                        <th>Document Name</th>
                                        <th>Category</th>
                                        <th>Total Employees</th>
                                        <th>Consented</th>
                                        <th>Pending</th>
                                        <th>Completion %</th>
                                        <th style="text-align: center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($documents as $index => $document)
                                        @php
                                            $stats = $summary[$document->id]['stats'];
                                            $progressClass = $stats['percentage'] >= 80 ? 'success' : ($stats['percentage'] >= 50 ? 'warning' : 'danger');
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $document->name }}</td>
                                            <td>{{ $document->category->name ?? 'N/A' }}</td>
                                            <td>{{ $stats['total'] }}</td>
                                            <td>
                                                <span class="label label-success">{{ $stats['consented'] }}</span>
                                            </td>
                                            <td>
                                                <span class="label label-warning">{{ $stats['pending'] }}</span>
                                            </td>
                                            <td>
                                                <div class="progress" style="margin-bottom: 0; height: 20px;">
                                                    <div class="progress-bar progress-bar-{{ $progressClass }}" role="progressbar"
                                                         style="width: {{ $stats['percentage'] }}%; line-height: 20px; font-weight: bold;"
                                                         aria-valuenow="{{ $stats['percentage'] }}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100">
                                                        {{ $stats['percentage'] }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="width: 100px; text-align: center;">
                                                <a href="{{ route('documents-upload.consents', $document->id) }}" class="btn btn-info btn-xs" title="View Consents">
                                                    <i class="fa fa-users"></i>
                                                </a>
                                                <a href="{{ route('documents-upload.consents.download', $document->id) }}" class="btn btn-primary btn-xs" title="Download Report">
                                                    <i class="fa fa-download"></i>
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

    <!-- Pending Acknowledgments -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <i class="mdi mdi-alert fa-fw"></i> Pending Acknowledgments
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="pendingTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr class="tr_header">
                                        <th>Document Name</th>
                                        <th>Pending Employees</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($documents as $document)
                                        @php
                                            $stats = $summary[$document->id]['stats'];
                                        @endphp
                                        @if($stats['pending'] > 0)
                                            <tr>
                                                <td>{{ $document->name }}</td>
                                                <td>
                                                    <span class="label label-danger">{{ $stats['pending'] }} employees pending</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('documents-upload.consents', $document->id) }}" class="btn btn-info btn-xs">
                                                        <i class="fa fa-eye"></i> View Details
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
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
