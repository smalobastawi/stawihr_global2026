@extends('admin.master')
@section('title', 'Targeted Employees for Survey: ' . $survey->title)
@section('content')

    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor">
                        <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    </li>
                    <li><a href="{{ route('survey.index') }}">Surveys</a></li>
                    <li>Targeted Employees</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="fa fa-users fa-fw"></i>
                        Targeted Employees for Survey: {{ $survey->title }}
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="pull-right">
                                        <span class="badge badge-primary">
                                            Total: {{ $employees->count() }} employees
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Employee ID</th>
                                            <th>Name</th>
                                            <th>Department</th>
                                            <th>Location</th>
                                            <th>Region</th>
                                            <th>Gender</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($employees as $key => $employee)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $employee->employee_id }}</td>
                                                <td>{{ $employee->full_name }}</td>
                                                <td>{{ $employee->department->department_name ?? 'N/A' }}</td>
                                                <td>{{ $employee->location->location_name ?? 'N/A' }}</td>
                                                <td>{{ $employee->location->region->name ?? 'N/A' }}</td>
                                                <td>{{ $employee->gender }}</td>
                                                <td>
                                                    <span
                                                        class="badge badge-{{ $employee->status == GeneralStatus::ACTIVE ? 'success' : 'danger' }}">
                                                        {{ $employee->status == GeneralStatus::ACTIVE ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <a href="{{ route('survey.index') }}" class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Back to Surveys
                                    </a>

                                    @if ($employees->count() > 0)
                                        <div class="pull-right">
                                            <a href="#" class="btn btn-primary" id="exportEmployees">
                                                <i class="fa fa-download"></i> Export to Excel
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page_scripts')
    <script>
        $(document).ready(function() {
            // Export to Excel functionality
            $('#exportEmployees').click(function(e) {
                e.preventDefault();
                window.location.href = "{{ route('survey.export-employees', $survey->id) }}";
            });
        });
    </script>
@endsection
