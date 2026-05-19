@extends('admin.master')
@section('content')
@section('title')
 @lang('approval.edit_workflow')
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <!-- ... existing header code ... -->
    </div>
    
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                @include('admin.partials.alert')
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <form class="form-horizontal" action="{{ route('approval-workflows.update', $workflow->id) }}" method="POST" id="approvalWorkflowForm" autocomplete="off">
                            @csrf
                            @method('PUT')
                            
                            <!-- Model Type (readonly) -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">@lang('approval.model_type')</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" value="{{ class_basename($workflow->model_type) }}" readonly>
                                </div>
                            </div>
                            
                            <!-- Reviewer Levels -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">@lang('approval.reviewer_levels')<span class="validateRq">*</span></label>
                                <div class="col-md-9">
                                    <input type="number" name="reviewer_levels" class="form-control no-autofill" 
                                        value="{{ $workflow->reviewer_config['levels'] }}" min="0" max="5" required
                                        autocomplete="new-reviewer-levels-{{ $workflow->id }}">
                                </div>
                            </div>
                            
                            <!-- Reviewer Required Levels -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">@lang('approval.reviewer_required_levels')<span class="validateRq">*</span></label>
                                <div class="col-md-9">
                                    <input type="number" name="reviewer_required_levels" class="form-control no-autofill" 
                                        value="{{ $workflow->reviewer_config['required_levels'] }}" min="0" max="5" required
                                        autocomplete="new-reviewer-required-levels-{{ $workflow->id }}">
                                </div>
                            </div>
                            
                            <!-- Approver Levels -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">@lang('approval.approver_levels')<span class="validateRq">*</span></label>
                                <div class="col-md-9">
                                    <input type="number" name="approver_levels" class="form-control no-autofill" 
                                        value="{{ $workflow->approver_config['levels'] }}" min="0" max="5" required
                                        autocomplete="new-approver-levels-{{ $workflow->id }}">
                                </div>
                            </div>
                            
                            <!-- Approver Required Levels -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">@lang('approval.approver_required_levels')<span class="validateRq">*</span></label>
                                <div class="col-md-9">
                                    <input type="number" name="approver_required_levels" class="form-control no-autofill" 
                                        value="{{ $workflow->approver_config['required_levels'] }}" min="0" max="5" required
                                        autocomplete="new-approver-required-levels-{{ $workflow->id }}">
                                </div>
                            </div>
                            
                            <!-- Status Checkbox -->
                            <div class="form-group">
                                <label class="col-md-3 control-label">@lang('common.status')</label>
                                <div class="col-md-9">
                                    <div class="checkbox checkbox-success">
                                        <input id="is_active" name="is_active" type="checkbox" value="1" {{ $workflow->is_active ? 'checked' : '' }}>
                                        <label for="is_active">@lang('common.active')</label>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            <h4 class="box-title">@lang('approval.step_assignments')</h4>
                            
                            @foreach($workflow->steps as $step)
                                <div class="form-group">
                                    <label class="col-md-3 control-label">
                                        {{ $step->name }} ({{ ucfirst($step->type) }} - Level {{ $step->level }})
                                    </label>
                                    <div class="col-md-9">
                                        <select readonly name="assignments[{{ $step->id }}][]" class="form-control select2 no-autofill" id="assignments-{{ $step->id }}" multiple autocomplete="off">
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" 
                                                    {{ $step->assignments->contains('user_id', $user->id) ? 'selected' : '' }}>
                                                    {{ $user->employeeDetails ? $user->employeeDetails->fullName() : $user->name }} ({{ $user->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="form-group">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-info btn_style">@lang('common.update')</button>
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

@push('styles')
<style>
    /* Hide autofill icons in Chrome */
    input:-webkit-autofill,
    input:-webkit-autofill:hover, 
    input:-webkit-autofill:focus {
        -webkit-box-shadow: 0 0 0 1000px white inset !important;
        transition: background-color 5000s ease-in-out 0s;
    }
</style>
@endpush
@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2();
        
        // Advanced autofill prevention for number inputs only
        $('#approvalWorkflowForm').find('input.no-autofill').each(function() {
            // Set random name attribute temporarily
            var randomName = 'field-' + Math.random().toString(36).substring(2, 15);
            $(this).attr('data-original-name', $(this).attr('name'));
            $(this).attr('name', randomName);
            
            // Set readonly and remove on focus
            $(this).attr('readonly', true).on('focus', function() {
                $(this).removeAttr('readonly');
                // Restore original name
                $(this).attr('name', $(this).data('original-name'));
            });
        });
        
        // Before form submission, restore all original names
        $('#approvalWorkflowForm').on('submit', function() {
            $(this).find('input.no-autofill').each(function() {
                $(this).attr('name', $(this).data('original-name'));
            });
            return true;
        });
    });
</script>
@endpush