@extends('admin.master')

@section('title')
    StawiHR - Payroll Reports Dashboard
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <h4 class="page-title">Pension Scheme Details</h4>
            </div>
            @include('admin.partials.alert')
            <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor">
                        <a href="{{ route('home.dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    </li>
                    <li><a href="{{ route('payroll.index') }}">Payroll</a></li>
                    <li><a href="{{ route('payroll.settings.pension-schemes.index') }}">Pension Schemes</a></li>
                    <li>{{ $pensionScheme->name }}</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <!-- Left Column -->
            <div class="col-md-8">
                <!-- Scheme Details -->
                <div class="white-box">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="box-title m-b-0">{{ $pensionScheme->name }}</h3>
                            <p class="text-muted m-b-30">Pension scheme details and employee enrollment</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="{{ route('payroll.settings.pension-schemes.edit', $pensionScheme) }}"
                                class="btn btn-warning">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('payroll.settings.pension-schemes.download-template', $pensionScheme) }}"
                                class="btn btn-info">
                                <i class="fa fa-download"></i> Download Template
                            </a>
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#uploadModal">
                                <i class="fa fa-upload"></i> Upload Assignments
                            </button>
                            <a href="{{ route('payroll.settings.pension-schemes.index') }}" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Scheme Code:</strong></td>
                                    <td><code>{{ $pensionScheme->code }}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Provider:</strong></td>
                                    <td>{{ $pensionScheme->provider_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Contact:</strong></td>
                                    <td>{{ $pensionScheme->provider_contact ?: 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Employee Contribution Rate:</strong></td>
                                    <td>
                                        <span
                                            class="label label-info">{{ number_format($pensionScheme->employee_contribution_rate, 2) }}%</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Employer Contribution Rate:</strong></td>
                                    <td>
                                        <span
                                            class="label label-success">{{ number_format($pensionScheme->employer_contribution_rate, 2) }}%</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Contribution Rate:</strong></td>
                                    <td>
                                        <span
                                            class="label label-primary">{{ number_format($pensionScheme->employee_contribution_rate + $pensionScheme->employer_contribution_rate, 2) }}%</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Minimum Contribution:</strong></td>
                                    <td>{{ $pensionScheme->minimum_contribution ? 'KES ' . number_format($pensionScheme->minimum_contribution, 2) : 'Not specified' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Maximum Contribution:</strong></td>
                                    <td>{{ $pensionScheme->maximum_contribution ? 'KES ' . number_format($pensionScheme->maximum_contribution, 2) : 'Not specified' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="label label-{{ $pensionScheme->is_active ? 'success' : 'danger' }}">
                                            {{ $pensionScheme->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created By:</strong></td>
                                    <td>{{ $pensionScheme->creator->name ?? 'System' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created At:</strong></td>
                                    <td>{{ $pensionScheme->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Updated:</strong></td>
                                    <td>{{ $pensionScheme->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h4>Description</h4>
                            <p class="text-muted">
                                {{ $pensionScheme->description ?: 'No description available for this pension scheme.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Enrolled Employees -->
                <div class="white-box">
                    <h4 class="box-title">Enrolled Employees</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Basic Salary</th>
                                    <th>Employee Contribution</th>
                                    <th>Employer Contribution</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pensionScheme->employeePayrolls as $employeePayroll)
                                    @php
                                        $employee = $employeePayroll->employee;
                                        $basicSalary = $employee->basic_salary ?? 0;
                                        $employeeContribution = $pensionScheme->calculateEmployeeContribution(
                                            $basicSalary,
                                        );
                                        $employerContribution = $pensionScheme->calculateEmployerContribution(
                                            $basicSalary,
                                        );
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $employee->full_name ?? $employee->name }}</strong><br>
                                            <small
                                                class="text-muted">{{ $employee->employee_number ?? $employee->id }}</small>
                                        </td>
                                        <td>{{ $employee->department->name ?? 'Not Assigned' }}</td>
                                        <td>KES {{ number_format($basicSalary, 2) }}</td>
                                        <td>KES {{ number_format($employeeContribution, 2) }}</td>
                                        <td>KES {{ number_format($employerContribution, 2) }}</td>
                                        <td>
                                            <span
                                                class="label label-{{ $employeePayroll->is_active ? 'success' : 'warning' }}">
                                                {{ $employeePayroll->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            <em>No employees enrolled in this pension scheme yet.</em>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-4">
                <!-- Scheme Statistics -->
                <div class="white-box">
                    <h4 class="box-title">Scheme Statistics</h4>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="text-center">
                                <h2 class="text-info">{{ $stats['total_employees'] }}</h2>
                                <p class="text-muted">Total Employees</p>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="text-center">
                                <h2 class="text-success">{{ $stats['active_employees'] }}</h2>
                                <p class="text-muted">Active Employees</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="text-center">
                                <h2 class="text-warning">KES {{ number_format($stats['total_contributions'], 0) }}</h2>
                                <p class="text-muted">Total Contributions</p>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="text-center">
                                <h2 class="text-primary">KES {{ number_format($stats['average_contribution'], 0) }}</h2>
                                <p class="text-muted">Average Contribution</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contribution Calculator -->
                <div class="white-box">
                    <h4 class="box-title">Contribution Calculator</h4>
                    <form id="calculatorForm">
                        <div class="form-group">
                            <label>Pensionable Pay (KES)</label>
                            <input type="number" id="pensionablePay" class="form-control" step="0.01" min="0"
                                placeholder="Enter amount" value="75000">
                        </div>
                        <button type="button" class="btn btn-primary btn-block" onclick="calculateContribution()">
                            <i class="fa fa-calculator"></i> Calculate
                        </button>
                    </form>
                    <div id="calculationResults" style="margin-top: 15px;">
                        <hr>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Employee Contribution:</strong></td>
                                <td id="employeeContribution" class="text-right">KES 3,750.00</td>
                            </tr>
                            <tr>
                                <td><strong>Employer Contribution:</strong></td>
                                <td id="employerContribution" class="text-right">KES 5,250.00</td>
                            </tr>
                            <tr class="success">
                                <td><strong>Total Contribution:</strong></td>
                                <td id="totalContribution" class="text-right">KES 9,000.00</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Actions -->
                <div class="white-box">
                    <h4 class="box-title">Actions</h4>
                    <div class="list-group">
                        <a href="{{ route('payroll.settings.pension-schemes.edit', $pensionScheme) }}"
                            class="list-group-item">
                            <i class="fa fa-edit text-warning"></i> Edit Scheme
                        </a>

                        <a href="{{ route('payroll.settings.pension-schemes.toggle-status', $pensionScheme) }}"
                            class="list-group-item">
                            <i class="fa fa-ban text-{{ $pensionScheme->is_active ? 'danger' : 'success' }}"></i>
                            {{ $pensionScheme->is_active ? 'Deactivate' : 'Activate' }} Scheme
                        </a>

                        <a href="{{ route('payroll.settings.pension-schemes.generate-report', $pensionScheme) }}"
                            class="list-group-item">
                            <i class="fa fa-file-pdf-o text-info"></i> Generate Report
                        </a>

                        <form method="POST"
                            action="{{ route('payroll.settings.pension-schemes.delete', $pensionScheme) }}"
                            onsubmit="return confirm('Are you sure you want to delete this pension scheme?')"
                            style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="list-group-item text-danger"
                                style="border: none; background: none; width: 100%; text-align: left;">
                                <i class="fa fa-trash"></i> Delete Scheme
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="uploadModalLabel">Upload Pension Scheme Assignments</h4>
            </div>
            <form method="POST"
                action="{{ route('payroll.settings.pension-schemes.upload-assignments', $pensionScheme) }}"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="file">Select CSV File</label>
                        <input type="file" name="file" id="file" class="form-control" accept=".csv"
                            required>
                        <p class="help-block">
                            Upload a CSV file with employee pension scheme assignments.
                            <a href="{{ route('payroll.settings.pension-schemes.download-template', $pensionScheme) }}"
                                target="_blank">Download template</a> if you don't have one.
                        </p>
                    </div>
                    @if (session('errors'))
                        <div class="alert alert-danger">
                            <strong>Upload Errors:</strong>
                            <ul>
                                @foreach (session('errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('page_scripts')
    <script>
        function calculateContribution() {
            var pensionablePay = $('#pensionablePay').val();

            if (!pensionablePay || pensionablePay <= 0) {
                alert('Please enter a valid pensionable pay amount');
                return;
            }

            // Calculate contributions using scheme rates
            var employeeRate = {{ $pensionScheme->employee_contribution_rate }};
            var employerRate = {{ $pensionScheme->employer_contribution_rate }};
            var employeeContribution = (pensionablePay * (employeeRate / 100)).toFixed(2);
            var employerContribution = (pensionablePay * (employerRate / 100)).toFixed(2);
            var totalContribution = (parseFloat(employeeContribution) + parseFloat(employerContribution)).toFixed(2);

            // Format with thousands separators
            function formatCurrency(amount) {
                return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            // Update the display
            $('#employeeContribution').text('KES ' + formatCurrency(employeeContribution));
            $('#employerContribution').text('KES ' + formatCurrency(employerContribution));
            $('#totalContribution').text('KES ' + formatCurrency(totalContribution));

            $('#calculationResults').show();
        }

        // Initialize with example values
        $(document).ready(function() {
            calculateContribution();
        });
    </script>
@endsection
