@extends('admin.master')
@section('content')
@section('title')
Document Consents - {{ $document->name }}
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('documents-upload.index') }}">Documents</a></li>
                <li class="breadcrumb-item active">Consents</li>
            </ol>
        </div>
        <div class="col-lg-12 col-sm-8 col-md-6 col-xs-6">
            <a href="{{ route('documents-upload.index') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> Back to Documents
            </a>
            <a href="{{ route('documents-upload.consents.download', $document->id) }}" class="btn btn-primary pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-download" aria-hidden="true"></i> Download Report
            </a>
        </div>
    </div>

    <!-- Document Info Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-file-document fa-fw"></i> Document Information
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Document Name:</strong> {{ $document->name }}</p>
                            <p><strong>Category:</strong> {{ $document->category->name ?? 'N/A' }}</p>
                            <p><strong>Uploaded On:</strong> {{ $document->created_at->format('d-m-Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Uploaded By:</strong> {{ $document->uploaded_by }}</p>
                            <p><strong>Total Consents:</strong> {{ $stats['consented'] }} / {{ $stats['total'] }}</p>
                            <p><strong>Completion Rate:</strong> {{ $stats['percentage'] }}%</p>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="row" style="margin-top: 15px;">
                        <div class="col-md-12">
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar progress-bar-success" role="progressbar"
                                     style="width: {{ $stats['percentage'] }}%; line-height: 30px; font-weight: bold;"
                                     aria-valuenow="{{ $stats['percentage'] }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                    {{ $stats['percentage'] }}% Acknowledged
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Consents Table -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-table fa-fw"></i> Employee Consents
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr class="tr_header">
                                        <th>#</th>
                                        <th>Employee Name</th>
                                        <th>Payroll Number</th>
                                        <th>Email</th>
                                        <th>Consented At</th>
                                        <th>IP Address</th>
                                        <th>Acknowledgment Text</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($consents as $index => $consent)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if($consent->employee)
                                                    {{ trim($consent->employee->first_name . ' ' . $consent->employee->middle_name . ' ' . $consent->employee->last_name) }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $consent->employee->payroll_number ?? 'N/A' }}</td>
                                            <td>{{ $consent->user->email ?? 'N/A' }}</td>
                                            <td>{{ $consent->consented_at->format('d-m-Y H:i:s') }}</td>
                                            <td>{{ $consent->ip_address ?? 'N/A' }}</td>
                                            <td>{{ $consent->acknowledgment_text }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No consents recorded yet.</td>
                                        </tr>
                                    @endforelse
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
