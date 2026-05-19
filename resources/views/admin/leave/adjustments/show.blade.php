@extends('admin.master')
@section('content')
@section('title', 'View Leave Adjustment')

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li><a href="{{ route('leave.adjustments.index') }}">Leave Adjustments</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-eye fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Employee</th>
                                    <td>{{ $adjustment->employee->fullname() }}
                                        ({{ $adjustment->employee->payroll_number }})</td>
                                </tr>
                                <tr>
                                    <th>Leave Type</th>
                                    <td>{{ $adjustment->leaveType->leave_type_name }}</td>
                                </tr>
                                <tr>
                                    <th>Financial Year</th>
                                    <td>{{ $adjustment->financialYear?->name }}</td>
                                </tr>
                                <tr>
                                    <th>Adjustment Type</th>
                                    <td>
                                        <span
                                            class="label label-{{ $adjustment->adjustment_type == 'add' ? 'success' : 'warning' }}">
                                            {{ ucfirst($adjustment->adjustment_type) }} Days
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Number of Days</th>
                                    <td><strong>{{ $adjustment->days }}</strong> days</td>
                                </tr>
                                <tr>
                                    <th>Reason</th>
                                    <td>{{ $adjustment->reason }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span
                                            class="label label-{{ $adjustment->status == 'approved' ? 'success' : ($adjustment->status == 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($adjustment->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created By</th>
                                    <td>{{ $adjustment->creator->user_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $adjustment->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                @if ($adjustment->approved_by)
                                    <tr>
                                        <th>Approved By</th>
                                        <td>{{ $adjustment->approver->user_name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Approved At</th>
                                        <td>{{ $adjustment->approved_at ? $adjustment->approved_at->format('Y-m-d H:i:s') : 'N/A' }}
                                        </td>
                                    </tr>
                                @endif
                                @if ($adjustment->rejection_reason)
                                    <tr>
                                        <th>Rejection Reason</th>
                                        <td>{{ $adjustment->rejection_reason }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>

                        <div class="form-group">
                            <a href="{{ route('leave.adjustments.index') }}" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Back to List
                            </a>
                            @if ($adjustment->status == 'pending')
                                <a href="{{ route('leave.adjustments.edit', $adjustment->id) }}"
                                    class="btn btn-warning">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
