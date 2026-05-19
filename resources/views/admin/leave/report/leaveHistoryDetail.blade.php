@php
    use Carbon\Carbon;
@endphp

@extends('admin.master')

@section('title', 'Employee Leave History - ' . $employee->fullName())

@section('content')
    <style>
        .employee-header {
            background: linear-gradient(135deg, #00b3ee 0%, #0077a3 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .employee-photo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid white;
            object-fit: cover;
        }

        .summary-card {
            background: white;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .summary-number {
            font-size: 32px;
            font-weight: bold;
            color: #00b3ee;
        }

        .summary-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }

        .leave-entry {
            background: white;
            border-left: 4px solid #00b3ee;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .leave-entry:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .year-header {
            background: #f5f5f5;
            padding: 15px 20px;
            margin: 30px 0 15px 0;
            border-radius: 4px;
            font-weight: bold;
            font-size: 18px;
            color: #333;
        }

        .balance-table th {
            background-color: #00b3ee;
            color: white;
        }

        .info-row {
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: bold;
            color: #666;
        }

        .back-btn {
            margin-bottom: 20px;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-approved {
            background-color: #28a745;
            color: white;
        }

        .status-pending {
            background-color: #ffc107;
            color: #000;
        }

        .status-rejected {
            background-color: #dc3545;
            color: white;
        }

        .status-recalled {
            background-color: #6c757d;
            color: white;
        }

        .status-recall-approved {
            background-color: #17a2b8;
            color: white;
        }

        .status-default {
            background-color: #00b3ee;
            color: white;
        }

        .leave-type-badge {
            background-color: #00b3ee;
            color: white;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .panel-custom {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .panel-custom .panel-heading {
            background-color: #f8f9fa;
            border-bottom: 2px solid #00b3ee;
            font-weight: bold;
        }
    </style>

    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor">
                        <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                    </li>
                    <li>
                        <a href="{{ route('leave.report.history') }}">Leave History</a>
                    </li>
                    <li>{{ $employee->fullName() }}</li>
                </ol>
            </div>
        </div>

        <!-- Back Button -->
        <div class="row back-btn">
            <div class="col-md-12">
                <a href="{{ route('leave.report.history') }}" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Back to Employee List
                </a>
            </div>
        </div>

        <!-- Employee Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="employee-header">
                    <div class="row">
                        <div class="col-md-2 text-center">
                            @if ($employee->photo)
                                <img src="{{ asset('uploads/employee_photos/' . $employee->photo) }}" alt="{{ $employee->fullName() }}" class="employee-photo">
                            @else
                                <div class="employee-photo" style="background: #fff; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa fa-user fa-3x" style="color: #00b3ee;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-10">
                            <h2 style="margin-top: 0; color: white;">{{ $employee->fullName() }}</h2>
                            <p style="font-size: 16px; margin-bottom: 10px;">
                                <i class="fa fa-briefcase"></i> {{ $employee->designation->designation_name ?? 'N/A' }}
                                | <i class="fa fa-building"></i> {{ $employee->department->department_name ?? 'N/A' }}
                                | <i class="fa fa-map-marker"></i> {{ $employee->location->location_name ?? 'N/A' }}
                            </p>
                            <p style="margin-bottom: 0;">
                                <i class="fa fa-id-badge"></i> Work No: <strong>{{ $employee->staff_no ?? 'N/A' }}</strong>
                                | <i class="fa fa-calendar"></i> Joined: <strong>{{ $dateOfJoining }}</strong>
                                | <i class="fa fa-envelope"></i> {{ $employee->email ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-number">{{ $totalApplications }}</div>
                    <div class="summary-label">Total Leave Applications</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-number">{{ $leaveDaysThisFY }}</div>
                    <div class="summary-label">Days Taken (Current FY)</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-number">{{ $totalLeaveDaysAllTime }}</div>
                    <div class="summary-label">Total Days (All Time)</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-number">{{ count($leaveByYear) }}</div>
                    <div class="summary-label">Years with Leave</div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Leave Balance Summary -->
            <div class="col-md-4">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <i class="fa fa-balance-scale"></i> Current Leave Balances ({{ $currentFY->name ?? 'Current FY' }})
                    </div>
                    <div class="panel-body" style="padding: 0;">
                        <table class="table table-bordered balance-table" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th>Leave Type</th>
                                    <th class="text-center">Earned</th>
                                    <th class="text-center">Used</th>
                                    <th class="text-center">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($leaveBalances as $balance)
                                    <tr>
                                        <td>{{ $balance['leave_type']->leave_type_name }}</td>
                                        <td class="text-center">{{ $balance['earned'] }}</td>
                                        <td class="text-center">{{ $balance['used'] }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $balance['balance'] > 0 ? 'badge-success' : 'badge-danger' }}">
                                                {{ $balance['balance'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No leave types assigned</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Employee Contact Info -->
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <i class="fa fa-info-circle"></i> Employee Information
                    </div>
                    <div class="panel-body">
                        <div class="info-row">
                            <span class="info-label">Payroll Number:</span> {{ $employee->payroll_number ?? 'N/A' }}
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phone:</span> {{ $employee->phone ?? 'N/A' }}
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span> {{ $employee->email ?? 'N/A' }}
                        </div>
                        <div class="info-row">
                            <span class="info-label">National ID:</span> {{ $employee->national_id ?? 'N/A' }}
                        </div>
                        <div class="info-row">
                            <span class="info-label">Employment Type:</span> {{ $employee->employment_type ?? 'N/A' }}
                        </div>
                        <div class="info-row">
                            <span class="info-label">Date of Joining:</span> {{ $dateOfJoining }}
                        </div>
                        @if ($employee->supervisor)
                            <div class="info-row">
                                <span class="info-label">Supervisor:</span> {{ $employee->supervisor->fullName() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Leave History Timeline -->
            <div class="col-md-8">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <i class="fa fa-history"></i> Leave History Timeline
                    </div>
                    <div class="panel-body">
                        @if (count($leaveHistory) > 0)
                            @foreach ($leaveByYear as $year => $leaves)
                                <div class="year-header">
                                    <i class="fa fa-calendar-o"></i> {{ $year }}
                                    <span class="badge badge-info pull-right">{{ count($leaves) }} leave(s)</span>
                                </div>

                                @foreach ($leaves as $leave)
                                    @php
                                        $statusClass = match($leave->final_status) {
                                            \App\Lib\Enumerations\LeaveStatus::APPROVE => 'status-approved',
                                            \App\Lib\Enumerations\LeaveStatus::PENDING => 'status-pending',
                                            \App\Lib\Enumerations\LeaveStatus::REJECT => 'status-rejected',
                                            \App\Lib\Enumerations\LeaveStatus::RECALL => 'status-recalled',
                                            \App\Lib\Enumerations\LeaveStatus::RECALL_APPROVED => 'status-recall-approved',
                                            default => 'status-default',
                                        };
                                        $statusLabel = match($leave->final_status) {
                                            \App\Lib\Enumerations\LeaveStatus::APPROVE => 'APPROVED',
                                            \App\Lib\Enumerations\LeaveStatus::PENDING => 'PENDING',
                                            \App\Lib\Enumerations\LeaveStatus::REJECT => 'REJECTED',
                                            \App\Lib\Enumerations\LeaveStatus::RECALL => 'RECALLED',
                                            \App\Lib\Enumerations\LeaveStatus::RECALL_APPROVED => 'RECALL APPROVED',
                                            default => 'UNKNOWN',
                                        };
                                    @endphp
                                    <div class="leave-entry" style="border-left-color: {{ match($leave->final_status) {
                                            \App\Lib\Enumerations\LeaveStatus::APPROVE => '#28a745',
                                            \App\Lib\Enumerations\LeaveStatus::PENDING => '#ffc107',
                                            \App\Lib\Enumerations\LeaveStatus::REJECT => '#dc3545',
                                            \App\Lib\Enumerations\LeaveStatus::RECALL => '#6c757d',
                                            \App\Lib\Enumerations\LeaveStatus::RECALL_APPROVED => '#17a2b8',
                                            default => '#00b3ee',
                                        } }}">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <h4 style="margin-top: 0;">
                                                    <span class="leave-type-badge">{{ $leave->leaveType->leave_type_name }}</span>
                                                    <span class="status-badge {{ $statusClass }} pull-right">{{ $statusLabel }}</span>
                                                </h4>
                                                <div class="row" style="margin-top: 15px;">
                                                    <div class="col-md-6">
                                                        <p>
                                                            <strong><i class="fa fa-calendar"></i> From:</strong>
                                                            {{ Carbon::parse($leave->application_from_date)->format('d M Y') }}
                                                        </p>
                                                        <p>
                                                            <strong><i class="fa fa-calendar-check-o"></i> To:</strong>
                                                            {{ Carbon::parse($leave->application_to_date)->format('d M Y') }}
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p>
                                                            <strong><i class="fa fa-clock-o"></i> Days:</strong>
                                                            {{ $leave->number_of_day }} day(s)
                                                        </p>
                                                        <p>
                                                            <strong><i class="fa fa-paper-plane"></i> Applied:</strong>
                                                            {{ Carbon::parse($leave->application_date)->format('d M Y') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                @if ($leave->purpose)
                                                    <p style="margin-top: 10px;">
                                                        <strong><i class="fa fa-comment"></i> Purpose:</strong><br>
                                                        {{ $leave->purpose }}
                                                    </p>
                                                @endif
                                                @if ($leave->remarks)
                                                    <p>
                                                        <strong><i class="fa fa-sticky-note"></i> Remarks:</strong><br>
                                                        {{ $leave->remarks }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <div style="background: #f8f9fa; padding: 15px; border-radius: 4px;">
                                                    @if ($leave->final_status == \App\Lib\Enumerations\LeaveStatus::APPROVE)
                                                        <p style="margin-bottom: 10px;">
                                                            <strong><i class="fa fa-check-circle"></i> Approved By:</strong><br>
                                                            {{ $leave->approveBy ? $leave->approveBy->fullName() : 'N/A' }}
                                                        </p>
                                                        <p style="margin-bottom: 0;">
                                                            <strong><i class="fa fa-calendar-check-o"></i> Approval Date:</strong><br>
                                                            {{ $leave->approve_date ? Carbon::parse($leave->approve_date)->format('d M Y') : 'N/A' }}
                                                        </p>
                                                    @elseif ($leave->final_status == \App\Lib\Enumerations\LeaveStatus::PENDING)
                                                        <p style="margin-bottom: 0;">
                                                            <strong><i class="fa fa-clock-o"></i> Status:</strong><br>
                                                            <span class="text-warning">Awaiting Approval</span>
                                                        </p>
                                                    @elseif ($leave->final_status == \App\Lib\Enumerations\LeaveStatus::REJECT)
                                                        <p style="margin-bottom: 10px;">
                                                            <strong><i class="fa fa-times-circle"></i> Rejected By:</strong><br>
                                                            {{ $leave->rejectBy ? $leave->rejectBy->fullName() : 'N/A' }}
                                                        </p>
                                                        <p style="margin-bottom: 0;">
                                                            <strong><i class="fa fa-calendar-times-o"></i> Rejection Date:</strong><br>
                                                            {{ $leave->reject_date ? Carbon::parse($leave->reject_date)->format('d M Y') : 'N/A' }}
                                                        </p>
                                                    @elseif ($leave->final_status == \App\Lib\Enumerations\LeaveStatus::RECALL)
                                                        <p style="margin-bottom: 0;">
                                                            <strong><i class="fa fa-undo"></i> Status:</strong><br>
                                                            <span class="text-muted">Recalled</span>
                                                        </p>
                                                    @else
                                                        <p style="margin-bottom: 0;">
                                                            <strong><i class="fa fa-info-circle"></i> Status:</strong><br>
                                                            {{ $statusLabel }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        @else
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> No leave records found for this employee.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
