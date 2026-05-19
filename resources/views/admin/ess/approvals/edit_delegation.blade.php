@extends('admin.master')

@section('title')
    Edit Approval Delegation
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                            Dashboard</a></li>
                    <li><a href="{{ route('ess.approval.index') }}">My Approvals</a></li>
                    <li><a href="{{ route('ess.approval.delegations.index') }}">Approval Delegations</a></li>
                    <li>Edit Delegation</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-account-switch fa-fw"></i> Edit Approval Delegation
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @include('admin.partials.alert')

                            <form action="{{ route('ess.approval.delegations.update', $delegation->id) }}" method="POST"
                                class="form-horizontal">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="delegate_to_user_id" class="control-label">Delegate To *</label>
                                            <select name="delegate_to_user_id" id="delegate_to_user_id" class="form-control"
                                                required>
                                                <option value="">Select User</option>
                                                @foreach ($availableUsers as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ $delegation->delegate_to_user_id == $user->id ? 'selected' : '' }}>
                                                        {{ $user->email }}
                                                        @if ($user->employeeDetails)
                                                            ({{ $user->employeeDetails->fullName() }})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('delegate_to_user_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="delegation_type" class="control-label">Delegation Type *</label>
                                            <select name="delegation_type" id="delegation_type" class="form-control"
                                                required>
                                                <option value="all"
                                                    {{ $delegation->delegation_type == 'all' ? 'selected' : '' }}>All
                                                    Approvals</option>
                                                <option value="specific"
                                                    {{ $delegation->delegation_type == 'specific' ? 'selected' : '' }}>
                                                    Specific Module</option>
                                            </select>
                                            @error('delegation_type')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group" id="model_type_group"
                                            style="{{ $delegation->delegation_type == 'specific' ? '' : 'display: none;' }}">
                                            <label for="model_type" class="control-label">Module *</label>
                                            <select name="model_type" id="model_type" class="form-control">
                                                @foreach ($modelTypes as $modelType)
                                                    <option value="{{ $modelType }}"
                                                        {{ $delegation->model_type == $modelType ? 'selected' : '' }}>
                                                        {{ $modelType }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('model_type')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group" id="workflow_group">
                                            <label for="workflow_id" class="control-label">Workflow *</label>
                                            <select name="workflow_id" id="workflow_id" class="form-control" required>
                                                <option value="">Select Workflow</option>
                                                @foreach ($workflows as $workflow)
                                                    <option value="{{ $workflow->id }}"
                                                        {{ $delegation->workflow_id == $workflow->id ? 'selected' : '' }}>
                                                        {{ $workflow->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('workflow_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="start_date" class="control-label">Start Date *</label>
                                            <input type="date" name="start_date" id="start_date" class="form-control"
                                                value="{{ old('start_date', $delegation->start_date ? $delegation->start_date->format('Y-m-d') : '') }}"
                                                required>
                                            @error('start_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="end_date" class="control-label">End Date</label>
                                            <input type="date" name="end_date" id="end_date" class="form-control"
                                                value="{{ old('end_date', $delegation->end_date ? $delegation->end_date->format('Y-m-d') : '') }}">
                                            @error('end_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                            <small class="text-muted">Leave empty for indefinite delegation</small>
                                        </div>

                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="is_active" value="1"
                                                        {{ $delegation->is_active ? 'checked' : '' }}>
                                                    <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                                                    Active
                                                </label>
                                            </div>
                                            @error('is_active')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="include_submissions" value="1"
                                                        {{ $delegation->include_submissions ? 'checked' : '' }}>
                                                    <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                                                    Include Submissions
                                                </label>
                                            </div>
                                            @error('include_submissions')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="notes" class="control-label">Notes</label>
                                            <textarea name="notes" id="notes" class="form-control" rows="3"
                                                placeholder="Any additional notes about this delegation...">{{ old('notes', $delegation->notes) }}</textarea>
                                            @error('notes')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <i class="fa fa-info-circle"></i> Current Delegation Details
                                            </div>
                                            <div class="panel-body">
                                                <p><strong>Created:</strong>
                                                    {{ $delegation->created_at->format('M d, Y H:i') }}</p>
                                                <p><strong>Last Updated:</strong>
                                                    {{ $delegation->updated_at->format('M d, Y H:i') }}</p>
                                                <p><strong>Status:</strong>
                                                    <span
                                                        class="label label-{{ $delegation->is_active ? 'success' : 'danger' }}">
                                                        {{ $delegation->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </p>
                                                @if ($delegation->delegate)
                                                    <p><strong>Current Delegate:</strong>
                                                        {{ $delegation->delegate->email }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="panel panel-warning">
                                            <div class="panel-heading">
                                                <i class="fa fa-exclamation-triangle"></i> Important Notes
                                            </div>
                                            <div class="panel-body">
                                                <ul>
                                                    <li>Delegates will be notified via email of any changes</li>
                                                    <li>Active delegations cannot be assigned to the same delegate again
                                                    </li>
                                                    <li>Deactivating a delegation will immediately stop notifications</li>
                                                    <li>Deleted delegations cannot be recovered</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="pull-right">
                                                <a href="{{ route('ess.approval.delegations.index') }}"
                                                    class="btn btn-default">
                                                    <i class="fa fa-times"></i> Cancel
                                                </a>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fa fa-save"></i> Update Delegation
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
            // Toggle model type and workflow groups based on delegation type
            $('#delegation_type').change(function() {
                const delegationType = $(this).val();

                if (delegationType === 'specific') {
                    $('#model_type_group').show();
                    $('#workflow_group').show();
                } else {
                    $('#model_type_group').hide();
                    $('#workflow_group').show();
                }
            });

            // Form validation
            $('form').on('submit', function(e) {
                const startDate = new Date($('#start_date').val());
                const endDate = $('#end_date').val() ? new Date($('#end_date').val()) : null;

                if (endDate && endDate <= startDate) {
                    e.preventDefault();
                    alert('End date must be after start date.');
                    return false;
                }

                return true;
            });
        });
    </script>
@endsection
