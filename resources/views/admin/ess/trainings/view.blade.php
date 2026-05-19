@extends('admin.master')
@section('content')
@section('title')
    @lang('training.employee_trainig_details')
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
            <a href="{{ route('ess.trainings.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                    class="fa fa-list-ul" aria-hidden="true"></i> @lang('training.view_employee_training') </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">

                        {{-- Validation and success/error messages --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                @foreach ($errors->all() as $error)
                                    <strong>{{ $error }}</strong><br>
                                @endforeach
                            </div>
                        @endif

                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif

                        {{-- Form --}}
                        @if (isset($editModeData))
                            <form action="{{ route('trainingInfo.update', $editModeData->id) }}" method="POST" enctype="multipart/form-data" id="trainingForm">
@csrf
@method('PUT')

                            <input type="hidden" name="_method" value="PUT">
                        @else
                            <form method="POST">
							@csrf
                        @endif
                        @csrf
                        <div class="form-body readonly">
                            {{-- Training Type and Facilitator --}}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('training.training_type') <span class="validateRq">*</span></label>
                                        <select name="training_type_id" class="form-control">
                                            @foreach($trainingTypeList as $__key => $__value)
                                                <option value="{{ $__key }}" {{ (string)($editModeData->training_type_id ?? '') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('training.facilitator') <span class="validateRq">*</span></label>
                                        <select name="facilitator_id" class="form-control">
                                            @foreach($facilitatorList as $__key => $__value)
                                                <option value="{{ $__key }}" {{ (string)($editModeData->facilitator_id ?? '') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('training.subject') <span class="validateRq">*</span></label>
                                        <input type="text" name="subject" class="form-control" value="{{ $editModeData->subject ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Attendance Details --}}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('training.attendance_type') <span class="validateRq">*</span></label>
                                        <select name="attendance_type" class="form-control">
                                            <option value="physical" {{ ($editModeData->attendance_type ?? '') == 'physical' ? 'selected' : '' }}>Physical</option>
                                            <option value="online" {{ ($editModeData->attendance_type ?? '') == 'online' ? 'selected' : '' }}>Online</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('training.attendance_link')</label>
                                        <input type="text" name="attendance_link" class="form-control" value="{{ $editModeData->attendance_link ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('training.attendance_location')</label>
                                        <input type="text" name="attendance_location" class="form-control" value="{{ $editModeData->attendance_location ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Date Range --}}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('common.start_date') <span class="validateRq">*</span></label>
                                        <p class="form-control-static">{{ isset($editModeData) && $editModeData->start_date ? $editModeData->start_date->format('d/m/Y') : 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('common.start_time') <span class="validateRq">*</span></label>
                                        <p class="form-control-static">{{ $editModeData->start_time ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('common.end_date') <span class="validateRq">*</span></label>
                                        <p class="form-control-static">{{ isset($editModeData) && $editModeData->end_date ? $editModeData->end_date->format('d/m/Y') : 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('common.end_time') <span class="validateRq">*</span></label>
                                        <p class="form-control-static">{{ $editModeData->end_time ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Description --}}
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>@lang('training.description')</label>
                                        <textarea name="description" class="form-control" rows="3">{{ $editModeData->description ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>


                        @if (isset($invitationStatus) && $invitationStatus)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        @if (isset($employee))
                                            <label>Your Response</label>
                                            <div class="response-buttons">
                                                @if ($invitationStatus->status == TrainingInvitationStatus::SENT)
                                                    {{-- Initial response buttons --}}
                                                    <div class="initial-response">
                                                        <a href="{{ route('ess.trainings.invitation.response', [
                                                            'training' => $editModeData->id,
                                                            'employee' => $employee->employee_id,
                                                            'status' => 'accepted',
                                                        ]) }}"
                                                            class="btn btn-success">
                                                            <i class="fa fa-check"></i> Accept Invitation
                                                        </a>
                                                        <a href="{{ route('ess.trainings.invitation.response', [
                                                            'training' => $editModeData->id,
                                                            'employee' => $employee->employee_id,
                                                            'status' => 'declined',
                                                        ]) }}"
                                                            class="btn btn-danger">
                                                            <i class="fa fa-times"></i> Decline Invitation
                                                        </a>
                                                    </div>
                                                @else
                                                    {{-- Current status display with change option --}}
                                                    <div class="current-status">
                                                        <div
                                                            class="alert alert-info mb-3">
                                                            Your current response:
                                                            <strong>{{ TrainingInvitationStatus::getName($invitationStatus->status) }}</strong>
                                                        </div>

                                                        @if ($invitationStatus->status == TrainingInvitationStatus::ACCEPTED)
                                                            <a href="{{ route('ess.trainings.invitation.response', [
                                                                'training' => $editModeData->id,
                                                                'employee' => $employee->employee_id,
                                                                'status' => 'declined',
                                                            ]) }}"
                                                                class="btn btn-danger"
                                                                style="color: #f9f9f9 !important;"
                                                                >
                                                                <i class="fa fa-times"></i> Change to Decline
                                                            </a>
                                                        @else
                                                            <a href="{{ route('ess.trainings.invitation.response', [
                                                                'training' => $editModeData->id,
                                                                'employee' => $employee->employee_id,
                                                                'status' => 'accepted',
                                                            ]) }}"
                                                                class="btn btn-success" 
                                                                style="color: #f9f9f9 !important;">
                                                                <i class="fa fa-check"></i> Change to Accept
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif


                        {{-- @if (isset($showOnly))
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        @if ($editModeData->google_calendar_link)
                                            <a href="{{ $editModeData->google_calendar_link }}" target="_blank" class="btn btn-danger">
                                                <i class="fa fa-google"></i> Add to Google Calendar
                                            </a>
                                        @endif
                                        @if ($editModeData->outlook_calendar_link)
                                            <a href="{{ $editModeData->outlook_calendar_link }}" target="_blank" class="btn btn-primary">
                                                <i class="fa fa-windows"></i> Add to Outlook Calendar
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif --}}

                        {{-- Submit Button --}}
                        @if (!isset($showOnly))
                            <div class="form-actions">
                                <button type="submit" class="btn btn-info">
                                    @if (isset($editModeData))
                                        <i class="fa fa-pencil"></i> @lang('common.update')
                                    @else
                                        <i class="fa fa-check"></i> @lang('common.save')
                                    @endif
                                </button>
                            </div>
                        @endif


                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
<style>
    .readonly-form input,
    .readonly-form select,
    .readonly-form textarea {
        background-color: #f9f9f9 !important;
        cursor: not-allowed !important;
    }

    .readonly-form .select2-selection {
        background-color: #f9f9f9 !important;
        cursor: not-allowed !important;
    }
</style>

<script>
    $(document).ready(function() {
        @if (isset($showOnly))
            // Disable all form elements
            $('#trainingForm').find('input, select, textarea, button').prop('disabled', true);

            // Special handling for Select2
            $('.select2').prop('disabled', true).trigger('change');

            // Add readonly class to form
            $('#trainingForm').addClass('readonly-form');
        @endif

        // Initialize Select2
        $('.select2').select2({
            disabled: {{ isset($showOnly) ? 'true' : 'false' }}
        });
    });
</script>

@if (isset($invitationStatus) && $invitationStatus && $invitationStatus->status == 'SENT')
    <script>
        function respondToTraining(response) {
            if (!confirm('Are you sure you want to ' + response.toLowerCase() + ' this training invitation?')) {
                return;
            }

            $.ajax({
                url: '{{ route('employee.training.respond', $editModeData->id) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    response: response
                },
                success: function() {
                    location.reload();
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        }
    </script>
@endif
@endsection

