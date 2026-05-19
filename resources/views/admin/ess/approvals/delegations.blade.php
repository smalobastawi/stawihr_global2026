@extends('admin.master')

@section('title')
    Approval Delegations
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor">
                        <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                    </li>
                    <li><a href="{{ route('ess.approval.index') }}">Approvals</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <!-- My Delegations Panel -->
            <div class="col-md-8">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <i class="mdi mdi-account-switch fa-fw"></i> My Approval Delegations
                        <button class="btn btn-xs btn-primary pull-right" data-toggle="modal"
                            data-target="#addDelegationModal">
                            <i class="fa fa-plus"></i> Add New Delegation
                        </button>
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @include('admin.partials.alert')

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>Delegate To</th>
                                            <th>Scope</th>
                                            <th>Period</th>
                                            <th>Includes Submissions</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($myDelegations as $delegation)
                                            <tr>
                                                <td>
                                                    <strong>{{ $delegation->delegate->email }}</strong>
                                                    @if ($delegation->delegate->employeeDetails)
                                                        <br>
                                                        <small>{{ $delegation->delegate->employeeDetails->fullName() }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($delegation->delegation_type == 'all')
                                                        <span class="label label-primary">All Approvals</span>
                                                    @elseif($delegation->delegation_type == 'specific_model')
                                                        <span class="label label-info">
                                                            {{ class_basename($delegation->model_type) ?? 'Specific Model' }}
                                                        </span>
                                                    @else
                                                        <span class="label label-warning">
                                                            {{ $delegation->workflow->name ?? 'Specific Workflow' }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $delegation->start_date->format('M d, Y') }}
                                                    @if ($delegation->end_date)
                                                        <br>to {{ $delegation->end_date->format('M d, Y') }}
                                                    @else
                                                        <br>to Indefinite
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($delegation->include_submissions)
                                                        <i class="fa fa-check text-success"></i>
                                                    @else
                                                        <i class="fa fa-times text-danger"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($delegation->isValid())
                                                        <span class="label label-success">Active</span>
                                                    @else
                                                        <span class="label label-default">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>{{ $delegation->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button class="btn btn-xs btn-warning edit-delegation"
                                                            data-id="{{ $delegation->id }}" data-toggle="modal"
                                                            data-target="#editDelegationModal">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-xs btn-danger delete-delegation"
                                                            data-id="{{ $delegation->id }}"
                                                            data-delegate="{{ $delegation->delegate->email }}">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                        <button
                                                            class="btn btn-xs btn-{{ $delegation->is_active ? 'default' : 'success' }} toggle-status"
                                                            data-id="{{ $delegation->id }}"
                                                            data-status="{{ $delegation->is_active ? 1 : 0 }}">
                                                            <i class="fa fa-power-off"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">
                                                    <div class="alert alert-info">
                                                        You have not created any approval delegations yet.
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delegated To Me Panel -->
            <div class="col-md-4">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-account-multiple fa-fw"></i> Delegated To Me
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @forelse($delegatedToMe as $delegation)
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <h5>
                                            <i class="fa fa-user"></i>
                                            {{ $delegation->delegator->email }}
                                            @if ($delegation->delegator->employeeDetails)
                                                <br>
                                                <small>{{ $delegation->delegator->employeeDetails->fullName() }}</small>
                                            @endif
                                        </h5>
                                        <p>
                                            <strong>Scope:</strong>
                                            @if ($delegation->delegation_type == 'all')
                                                All Approvals
                                            @elseif($delegation->delegation_type == 'specific_model')
                                                {{ class_basename($delegation->model_type) }}
                                            @else
                                                {{ $delegation->workflow->name ?? 'Specific Workflow' }}
                                            @endif
                                        </p>
                                        <p>
                                            <strong>Period:</strong>
                                            {{ $delegation->start_date->format('M d, Y') }}
                                            @if ($delegation->end_date)
                                                to {{ $delegation->end_date->format('M d, Y') }}
                                            @endif
                                        </p>
                                        @if ($delegation->include_submissions)
                                            <span class="label label-info">Can view submissions</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-info">
                                    No one has delegated approval authority to you.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Delegation Modal -->
    <div class="modal fade" id="addDelegationModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Add Approval Delegation</h4>
                </div>
                <form action="{{ route('ess.approval.delegations.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="delegate_to_user_id">Delegate To *</label>
                                    <select name="delegate_to_user_id" id="delegate_to_user_id" class="form-control select2"
                                        required>
                                        <option value="">Select User</option>
                                        @foreach ($availableUsers as $user)
                                            <option value="{{ $user->id }}">
                                                {{ $user->email }}
                                                @if ($user->employeeDetails)
                                                    - {{ $user->employeeDetails->fullName() }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="delegation_type">Delegation Scope *</label>
                                    <select name="delegation_type" id="delegation_type" class="form-control" required>
                                        <option value="all">All Approvals</option>
                                        <option value="specific_model">Specific Model Type</option>
                                        <option value="specific_workflow">Specific Workflow</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6" id="model_type_container" style="display: none;">
                                <div class="form-group">
                                    <label for="model_type">Model Type</label>
                                    <select name="model_type" id="model_type" class="form-control">
                                        <option value="">Select Model Type</option>
                                        @foreach ($modelTypes as $modelType)
                                            <option value="{{ $modelType }}">{{ $modelType }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6" id="workflow_id_container" style="display: none;">
                                <div class="form-group">
                                    <label for="workflow_id">Workflow</label>
                                    <select name="workflow_id" id="workflow_id" class="form-control">
                                        <option value="">Select Workflow</option>
                                        @foreach ($workflows as $workflow)
                                            <option value="{{ $workflow->id }}">{{ $workflow->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Start Date *</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control"
                                        value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">End Date (Optional)</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control">
                                    <small class="text-muted">Leave empty for indefinite delegation</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" name="include_submissions" id="include_submissions"
                                        class="form-check-input" value="1" checked>
                                    <label class="form-check-label" for="include_submissions">
                                        Include access to my submissions
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input"
                                        value="1" checked>
                                    <label class="form-check-label" for="is_active">
                                        Active immediately
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label for="notes">Notes (Optional)</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"
                                placeholder="Add any notes about this delegation..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save Delegation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Delegation Modal -->
    <div class="modal fade" id="editDelegationModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Edit Approval Delegation</h4>
                </div>
                <form id="editDelegationForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body" id="editDelegationBody">
                        <!-- Content loaded via AJAX -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Update Delegation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2 for the delegate dropdown
            $('#delegate_to_user_id').select2({
                placeholder: "Search for a user...",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#addDelegationModal') // Important for modal
            });
            // Handle delegation type change
            $('#delegation_type').change(function() {
                const type = $(this).val();
                $('#model_type_container').hide();
                $('#workflow_id_container').hide();

                if (type === 'specific_model') {
                    $('#model_type_container').show();
                } else if (type === 'specific_workflow') {
                    $('#workflow_id_container').show();
                }
            });

            // Trigger initial state
            $('#delegation_type').trigger('change');

            // Handle edit delegation button click
            $('.edit-delegation').click(function() {
                const delegationId = $(this).data('id');

                $.ajax({
                    url: `/ess/approval/approval-delegations/${delegationId}/edit`,
                    method: 'GET',
                    success: function(response) {
                        $('#editDelegationBody').html(response);
                        $('#editDelegationForm').attr('action',
                            `/ess/approval/approval-delegations/update/${delegationId}`);
                    },
                    error: function(xhr) {
                        toastr.error('Failed to load delegation details.');
                    }
                });
            });

            // Handle delete delegation
            $('.delete-delegation').click(function() {
                const delegationId = $(this).data('id');
                const delegateEmail = $(this).data('delegate');

                if (confirm(`Are you sure you want to delete delegation to ${delegateEmail}?`)) {
                    $.ajax({
                        url: `/ess/approval/approval-delegations/delete/${delegationId}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            toastr.success(response.success ||
                                'Delegation deleted successfully.');
                            setTimeout(() => location.reload(), 1000);
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.error ||
                                'Failed to delete delegation.');
                        }
                    });
                }
            });

            // Handle toggle status
            $('.toggle-status').click(function() {
                const delegationId = $(this).data('id');
                const currentStatus = $(this).data('status');
                const newStatus = currentStatus ? 0 : 1;

                $.ajax({
                    url: `/ess/approval/approval-delegations/${delegationId}/toggle-status`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: newStatus
                    },
                    success: function(response) {
                        toastr.success(response.success || 'Delegation status updated.');
                        setTimeout(() => location.reload(), 1000);
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.error ||
                            'Failed to update delegation status.');
                    }
                });
            });
        });
    </script>
@endsection
