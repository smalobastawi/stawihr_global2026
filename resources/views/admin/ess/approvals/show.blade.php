@extends('admin.master')

@section('title')
    Approval Details
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li><a href="{{ route('ess.approval.index') }}">My Approvals</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-file-document fa-fw"></i> Approval Details: 
                        {{ class_basename($model) }} #{{ $model->id }}
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @include('admin.partials.alert')

                            <div class="row">
                                <!-- Left Column - Basic Info -->
                                <div class="col-md-6">
                                    <div class="white-box">
                                        <h3 class="box-title">Approval Information</h3>
                                        <hr>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <tr>
                                                    <th width="40%">Status</th>
                                                    <td>
                                                        <span class="label label-{{ $model->status === GeneralStatus::ACTIVE ? 'success' : ($model->status === GeneralStatus::INACTIVE ? 'danger' : 'warning') }}">
                                                            {{ ucfirst($model->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Submitted By</th>
                                                    <td>
                                                        {{ $model->submitter->name ?? 'System' }}
                                                        @if($model->submitter && $model->submitter->employeeDetails)
                                                            <br><small>{{ $model->submitter->employeeDetails->department->name ?? '' }}</small>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Date Submitted</th>
                                                    <td>{{ $model->created_at->format('M d, Y H:i') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Current Stage</th>
                                                    <td>
                                                        @if($currentStep)
                                                            <span class="label label-{{ $currentStep->type === 'reviewer' ? 'primary' : 'success' }}">
                                                                {{ ucfirst($currentStep->type) }} Level {{ $currentStep->level }}
                                                            </span>
                                                        @else
                                                            <span class="label label-default">No active step</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Current Approvers</th>
                                                    <td>
                                                        @if($currentStep && $currentStep->assignments->count())
                                                            <ul class="list-unstyled">
                                                                @foreach($currentStep->assignments as $assignment)
                                                                    <li>
                                                                        <i class="fa fa-user"></i> 
                                                                        {{ $assignment->user->name }}
                                                                        @if($assignment->user->employeeDetails)
                                                                            ({{ $assignment->user->employeeDetails->fullName() ?? '' }})
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <span class="text-muted">No approvers assigned</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column - Model Details -->
                                <div class="col-md-6">
                                    <div class="white-box">
                                        <h3 class="box-title">{{ class_basename($model) }} Details</h3>
                                        <hr>
                                        <div class="table-responsive">
                                            <table class="table">
                                                @if(method_exists($model, 'getApprovalDetails'))
                                                    @foreach($model->getApprovalDetails() as $label => $value)
                                                        <tr>
                                                            <th width="40%">{{ $label }}</th>
                                                            <td>{!! $value !!}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    @foreach($model->getAttributes() as $key => $value)
                                                        @if(!in_array($key, ['id', 'created_at', 'updated_at', 'deleted_at']))
                                                            <tr>
                                                                <th width="40%">{{ Str::title(str_replace('_', ' ', $key)) }}</th>
                                                                <td>
                                                                    @if(is_array($value) || is_object($value))
                                                                        {{ json_encode($value) }}
                                                                    @else
                                                                        {{ $value }}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Approval History -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="white-box">
                                        <h3 class="box-title">Approval History</h3>
                                        <hr>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Action</th>
                                                        <th>User</th>
                                                        <th>Step</th>
                                                        <th>Comments</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($approvalLogs as $index => $log)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>
                                                                <span class="label label-{{ $log->action === 'approved' ? 'success' : ($log->action === 'rejected' ? 'danger' : 'info') }}">
                                                                    {{ ucfirst($log->action) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                {{ $log->user->employeeDetails ? $log->user->employeeDetails->fullName(): $log->user->email }}
                                                                @if($log->user && $log->user->employeeDetails)
                                                                    <br><small>{{ $log->user->employeeDetails->fullName() ?? '' }}</small>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($log->step)
                                                                    {{ $log->step->name }} ({{ ucfirst($log->step->type) }})
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </td>
                                                            <td>{{ $log->comments ?? 'No comments' }}</td>
                                                            <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center">No approval history found</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            @php
                                // Check if current user can take action on this approval
                                $userHasApproved = $model->approvalLogs()
                                    ->where('user_id', auth()->id())
                                    ->whereIn('action', ['approved', 'rejected'])
                                    ->exists();
                                
                                $canTakeAction = $currentStep &&
                                    $currentStep->assignments->contains('user_id', auth()->id()) &&
                                    !$userHasApproved ;
                                  $submittedByCurrentUser =  $model->approvalLogs()
                                    ->where('action', 'submitted')
                                    ->where('created_by', auth()->id())
                                    ->exists();
                            @endphp
                            
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="btn-group">
                                        @if($submittedByCurrentUser)
                                         <div class="alert-warning">
                                                    <i class="fa fa-check-circle"></i> You cannot approve your own submission!
                                                </div>

                                            @elseif($canTakeAction)
                                                <button class="btn btn-success approve-btn"
                                                    data-model="{{ str_replace('\\', '_', get_class($model)) }}"
                                                    data-id="{{ $model->id }}">
                                                    <i class="fa fa-check"></i> Approve
                                            </button>
                                            <button class="btn btn-danger reject-btn"
                                                data-model="{{ str_replace('\\', '_', get_class($model)) }}"
                                                data-id="{{ $model->id }}">
                                                <i class="fa fa-times"></i> Reject
                                            </button>
                                        @else
                                            @if($userHasApproved)
                                                <div class="alert alert-success">
                                                    <i class="fa fa-check-circle"></i> You have already taken action on this approval.
                                                </div>
                                            @else
                                                <div class="alert alert-info">
                                                    <i class="fa fa-info-circle"></i> This approval is not currently assigned to you.
                                                </div>
                                            @endif
                                        @endif
                                        <a href="{{ route('ess.approval.index') }}" class="btn btn-default">
                                            <i class="fa fa-arrow-left"></i> Back to List
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approval/Reject Modals -->
    <div class="modal fade" id="actionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="modalTitle">Approve/Reject Request</h4>
                </div>
                <form id="actionForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="comments">Comments</label>
                            <textarea name="comments" id="comments" class="form-control" rows="3"
                                placeholder="Enter your comments here..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn" id="modalActionBtn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script>
    $(document).ready(function() {
        // Handle approve button click
        $('.approve-btn').click(function() {
            const model = $(this).data('model');
            const id = $(this).data('id');
            
            $('#modalTitle').text('Approve Request');
            $('#modalActionBtn').removeClass('btn-danger').addClass('btn-success').text('Approve');
            $('#actionForm').attr('action', `/approvals/${model}/${id}/approve`);
            $('#comments').removeAttr('required');
            
            $('#actionModal').modal('show');
        });
        
        // Handle reject button click
        $('.reject-btn').click(function() {
            const model = $(this).data('model');
            const id = $(this).data('id');
            
            $('#modalTitle').text('Reject Request');
            $('#modalActionBtn').removeClass('btn-success').addClass('btn-danger').text('Reject');
            $('#actionForm').attr('action', `/approvals/${model}/${id}/reject`);
            $('#comments').attr('required', 'required');
            
            $('#actionModal').modal('show');
        });
        
        // Handle form submission
        $('#actionForm').submit(function(e) {
            e.preventDefault();
            const form = $(this);
            const button = form.find('button[type="submit"]');
            
            button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
            
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#actionModal').modal('hide');
                        toastr.success(response.message);
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        toastr.error(response.message);
                        button.prop('disabled', false).text($('#modalTitle').text().includes('Approve') ? 'Approve' : 'Reject');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'An error occurred');
                    button.prop('disabled', false).text($('#modalTitle').text().includes('Approve') ? 'Approve' : 'Reject');
                }
            });
        });
    });
</script>
@endsection