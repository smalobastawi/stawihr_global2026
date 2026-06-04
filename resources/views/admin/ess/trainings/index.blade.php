@extends('admin.master')

@section('title', trans('training.employee_training_list'))

@section('content')

    <style>
        .label {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
        }

        .label-success {
            background-color: #5cb85c;
        }

        .label-danger {
            background-color: #d9534f;
        }

        .label-warning {
            background-color: #f0ad4e;
        }

        .label-default {
            background-color: #777;
        }
    </style>
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                            @lang('dashboard.dashboard')</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>

        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-table fa-fw"></i> @yield('title')
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
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
                                    <i
                                        class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                </div>
                            @endif
                            @if (!$employee)
                                <div class="alert alert-warning" role="alert">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    <strong>Employee profile not found.</strong>
                                    Your account is not linked to an employee record, so training invitations cannot be displayed.
                                    Please contact P&amp;C for assistance.
                                </div>
                            @elseif ($all_trainings->isEmpty())
                                <div class="text-center" style="padding: 48px 24px;">
                                    <i class="mdi mdi-school" style="font-size: 64px; color: #ccc;"></i>
                                    <h4 style="margin-top: 20px; color: #555;">No trainings yet</h4>
                                    <p class="text-muted" style="max-width: 480px; margin: 12px auto 0;">
                                        You have not been assigned or invited to any trainings.
                                        When HR schedules a training for you, it will appear here with invitation details and response options.
                                    </p>
                                    <p class="text-muted" style="margin-top: 8px;">
                                        <small>Check back later or contact your supervisor or P&amp;C if you expect to see a training listed.</small>
                                    </p>
                                </div>
                            @else
                            <div class="table-responsive">

                                <table id="myTable" class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>@lang('common.serial')</th>
                                            <th>@lang('training.training_type')</th>
                                            <th>@lang('training.facilitator')</th>
                                            <th>@lang('training.subject')</th>
                                            <th>@lang('training.attendance_type')</th>
                                            <th>@lang('training.training_duration')</th>
                                            <th>@lang('training.attendance_details')</th>
                                            <th>Invitation Status</th>
                                            <th>Response</th>
                                            <th>@lang('common.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($all_trainings as $key => $training)
                                            @php
                                                // Get the training model (from either invitation or attendance)
                                                $trainingModel = $training->training ?? $training;
                                                // Get invitation status for this training
                                                $invitation = $invitations->firstWhere(
                                                    'training_id',
                                                    $trainingModel->id,
                                                );
                                                $status = $invitation ? $invitation->status : null;
                                            @endphp
                                            <tr class="{{ $trainingModel->id }}">
                                                <td style="width: 50px;">
                                                    {{ $loop->iteration }}
                                                </td>
                                                <td>
                                                    @if ($trainingModel->trainingType)
                                                        {{ $trainingModel->trainingType->training_type_name }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($trainingModel->facilitator)
                                                        {{ $trainingModel->facilitator->name }}
                                                    @endif
                                                </td>
                                                <td>{{ $trainingModel->subject }}</td>
                                                <td>{{ ucfirst($trainingModel->attendance_type) }}</td>
                                                <td>
                                                    {{ dateConvertDBtoForm($trainingModel->start_date) }}
                                                    to
                                                    {{ dateConvertDBtoForm($trainingModel->end_date) }}
                                                </td>
                                                <td>
                                                    @if ($trainingModel->attendance_type == 'physical')
                                                        {{ $trainingModel->attendance_location }}
                                                    @else
                                                        <a href="{{ $trainingModel->attendance_link }}" target="_blank">
                                                            @lang('training.attendance_link')
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($invitation)
                                                        <span
                                                            class="label label-{{ $invitation->status == TrainingInvitationStatus::ACCEPTED ? 'success' : ($invitation->status == TrainingInvitationStatus::DECLINED ? 'danger' : 'warning') }}">
                                                            {{ TrainingInvitationStatus::getName($invitation->status) }}
                                                        </span>
                                                    @else
                                                        <span class="label label-default">N/A</span>
                                                    @endif
                                                </td>
                                                <td>

                                                    @if ($status == TrainingInvitationStatus::ACCEPTED)
                                                        <a href="{{ route('ess.trainings.attendance.confirm', [
                                                            'training' => $trainingModel->id,
                                                            'employee' => $employee->employee_id,
                                                        ]) }}"
                                                            class="btn btn-primary btn-sm" style="color: #fff;">
                                                            <i class="glyphicon glyphicon-th-large"></i> Attendance Details
                                                        </a>
                                                    @else
                                                    @php
                                                    // For start datetime
                                                    $trainingStart = null;
                                                    if ($trainingModel->start_date && $trainingModel->start_time) {
                                                        $trainingStart = Carbon\Carbon::parse(
                                                            $trainingModel->start_date->format('Y-m-d') . ' ' . $trainingModel->start_time->format('H:i:s')
                                                        );
                                                    }
                                                
                                                    // For end datetime
                                                    $trainingEnd = null;
                                                    if ($trainingModel->end_date && $trainingModel->end_time) {
                                                        $trainingEnd = Carbon\Carbon::parse(
                                                            $trainingModel->end_date->format('Y-m-d') . ' ' . $trainingModel->end_time->format('H:i:s')
                                                        );
                                                    }
                                                
                                                    $now = Carbon\Carbon::now();
                                                @endphp
                                                        @if ($now->lt($trainingStart))
                                                            <div class="btn-group">
                                                                <button type="button" class="btn btn-success btn-sm">
                                                                    <i class="fa fa-envelope"></i> Respond
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-success dropdown-toggle dropdown-icon"
                                                                    data-toggle="dropdown">
                                                                    <span class="sr-only">Toggle Dropdown</span>
                                                                </button>
                                                                <div class="dropdown-menu" role="menu">
                                                                    <a
                                                                        href="{{ route('ess.trainings.invitation.response', [
                                                                            'training' => $trainingModel->id,
                                                                            'employee' => $employee->employee_id,
                                                                            'status' => 'accepted',
                                                                        ]) }}">
                                                                        <i class="fa fa-check text-success"></i> Accept
                                                                    </a>
                                                                    <div class="dropdown-divider"></div>
                                                                    <a
                                                                        href="{{ route('ess.trainings.invitation.response', [
                                                                            'training' => $trainingModel->id,
                                                                            'employee' => $employee->employee_id,
                                                                            'status' => 'declined',
                                                                        ]) }}">
                                                                        <i class="fa fa-times text-danger"></i> Decline
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @else
                                                            @if ($now->between($trainingStart, $trainingEnd))
                                                                <span class="badge badge-warning">Training in
                                                                    progress</span>
                                                            @else
                                                                <span class="badge badge-secondary">Training
                                                                    completed</span>
                                                            @endif
                                                        @endif
                                                    @endif

                                                </td>
                                                <td style="width: 500px;">
                                                    <a href="{{ route('ess.trainings.show', $trainingModel->id) }}"
                                                        class="btn btn-primary btn-sm" style="color: #fff;">
                                                        <i class="glyphicon glyphicon-th-large"></i> Invitation Details
                                                    </a>


                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
