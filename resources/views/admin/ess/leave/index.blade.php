@extends('admin.master')
@section('content')
@section('title')
    My Leave Applications
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="#"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
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
                        <div class="table-responsive">
                            <table id="myTable" class="table table-hover manage-u-table table-responsive{-sm} table-striped">
                                <div class="row">
                                    @php $fiscal_year = getCurrentFinancialYear() ? : 0; @endphp
                                    @if (!$fiscal_year)
                                        <div class="col-sm-8" style="color: rgb(173, 21, 21)">
                                            <span class="btn btn-danger pull-right" style="color: white">
                                                <i class="fa fa-plus-circle" aria-hidden="true" style="color: white"></i>
                                                Leave Application Not Allowed: Financial Year Not set. Contact Support
                                            </span>
                                        </div>
                                    @else
                                        <div class="col-sm-6" style="color: white">
                                            @if (!isset($ess))
                                                <a href="{{ route('applyForLeave.create') }}" class="btn btn-success pull-right" style="color: white">
                                                    <i class="fa fa-plus-circle" aria-hidden="true" style="color: white"></i>
                                                    @lang('leave.apply_for_leave')
                                                </a>
                                            @else
                                                <a href="{{ route('ess.leave.form') }}" class="btn btn-success pull-right" style="color: white">
                                                    <i class="fa fa-plus-circle" aria-hidden="true" style="color: white"></i>
                                                    @lang('leave.apply_for_leave')
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">@lang('leave.leave_type')</th>
                                        <th scope="col">Start</th>
                                        <th scope="col">End</th>
                                        <th scope="col">Days</th>
                                        <th scope="col">Supervisor Approval</th>
                                        {{-- <th scope="col">P&C In Charge</th> --}}
                                        <th scope="col">Comments</th>
                                        <th scope="col">Final Status</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $sl = null; @endphp
                                    @foreach ($results as $value)
                                        <tr>
                                            <td style="width: 100px;">{!! ++$sl !!}</td>
                                            <td>  {!! dateConvertDBtoForm($value->application_date) !!}</td>
                                            <td>
                                                @if (isset($value->employee->first_name))
                                                    {!! $value->employee->first_name !!}
                                                @endif
                                                @if (isset($value->employee->last_name))
                                                    {!! $value->employee->last_name !!}
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($value->leaveType->leave_type_name))
                                                    {!! $value->leaveType->leave_type_name !!}
                                                @endif
                                            </td>
                                            <td>
                                                {!! dateConvertDBtoForm($value->application_from_date) !!} 
                                            </td>
                                            <td>
                                                 {!! dateConvertDBtoForm($value->application_to_date) !!}
                                                
                                            </td>
                                            <td>{!! $value->number_of_day !!}</td>
                                            <td>
                                                @if ($value->final_status == LeaveStatus::RECALL)
                                                    <span class="label label-primary">Recalled/N/A</span>
                                                @elseif (isset($value->approve_date))
                                                    <br /><span class="text-muted">@lang('leave.approve_date'): {!! dateConvertDBtoForm($value->approve_date) !!}</span>
                                                @else
                                                @endif
                                            </td>
                                            <td>{{ $value->remarks }}</td>
                                            <td>
                                                @if ($value->final_status == LeaveStatus::PENDING)
                                                    <span class="label label-warning">@lang('common.pending')</span>
                                                @elseif($value->final_status == LeaveStatus::APPROVE)
                                                    <span class="label label-success">@lang('common.approved')</span>
                                                @elseif($value->final_status == LeaveStatus::RECALL)
                                                    <span class="label label-primary">Recalled</span>
                                                @else
                                                    <span class="label label-danger">@lang('common.rejected')</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a title="View Details" href="{{ route('ess.leave.applyForLeave.show', $value->leave_application_id) }}" class="btn btn-primary btn-xs btnColor">
                                                    <i class="glyphicon glyphicon-th-large" aria-hidden="true">View</i>
                                                </a>


                                                @php
                                                    $currentDate = now()->startOfDay();
                                                    $applicationToDate = \Carbon\Carbon::parse($value->application_to_date)->startOfDay();
                                                    $isPastLeave = $applicationToDate->lt($currentDate);
                                                    $isApproved = $value->final_status == LeaveStatus::APPROVE;
                                                    $allowEdit = !$isPastLeave || !$isApproved; // Allow edit if not past OR not approved
                                                @endphp

                                                <!-- Edit Button - Always enabled unless it's an approved past leave -->
                                                <button title="{{ !$allowEdit ? 'Past approved leave cannot be edited' : 'Edit' }}" class="btn btn-primary btn-xs btnColor {{ !$allowEdit ? 'disabled' : '' }}" @if (!$allowEdit) disabled @endif onclick="{{ $allowEdit ? "window.location.href='" . route('ess.leave.edit', $value->leave_application_id) . "'" : '' }}">
                                                    <i class="fa fa-edit" aria-hidden="true"></i> Edit
                                                </button>

                                                @if($value->final_status == LeaveStatus::RECALL)
                                                Recalled
                                                @else
                                                    <button title="{{ $isPastLeave ? 'Past leave cannot be recalled' : 'Recall' }}" class="btn btn-danger btn-xs recall-btn {{ $isPastLeave ? 'disabled' : '' }}" data-id="{{ $value->leave_application_id }}" @if ($isPastLeave) disabled @endif>
                                                        <i class="fa fa-undo" aria-hidden="true"></i> Recall
                                                    </button>
                                                    @endif
                                             

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="text-center">
                                {{ $results->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recall Confirmation Modal -->
<div class="modal fade" id="recallModal" tabindex="-1" role="dialog" aria-labelledby="recallModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="recallModalLabel">Recall Leave Application</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to recall this leave application?</p>
                <p><strong>Note:</strong> This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <form id="recallForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm Recall</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
<script>
    $(document).ready(function() {
        // Handle recall button click
        $('.recall-btn').click(function() {
            var leaveId = $(this).data('id');
            var url = "{{ route('ess.leave.recall', ':id') }}";
            url = url.replace(':id', leaveId);

            // Set the form action
            $('#recallForm').attr('action', url);

            // Show the modal
            $('#recallModal').modal('show');
        });
    });
</script>
@endsection
