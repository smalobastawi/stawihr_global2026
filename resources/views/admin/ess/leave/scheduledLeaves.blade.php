@extends('admin.master')
@section('content')
@section('title')
    My Scheduled Leaves
@endsection

<style>
    .schedule-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 25px;
        margin-bottom: 25px;
        border-left: 4px solid #667eea;
    }
    .schedule-card.upcoming {
        border-left-color: #28a745;
    }
    .schedule-card.past {
        border-left-color: #6c757d;
        opacity: 0.9;
    }
    .schedule-card.notification-sent {
        border-left-color: #17a2b8;
    }
    .schedule-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }
    .schedule-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #333;
        margin: 0;
    }
    .status-badge {
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-scheduled { background: #fff3cd; color: #856404; }
    .status-applied { background: #d4edda; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }
    .status-completed { background: #d1ecf1; color: #0c5460; }
    .schedule-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }
    .detail-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .detail-item i {
        color: #667eea;
        font-size: 1.1rem;
    }
    .detail-label {
        font-size: 0.85rem;
        color: #6c757d;
    }
    .detail-value {
        font-weight: 500;
        color: #333;
    }
    .apply-btn {
        margin-top: 20px;
    }
    .info-box {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
    }
</style>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li>My Scheduled Leaves</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- Info Box -->
            <div class="info-box">
                <h5><i class="fa fa-info-circle" style="color: #667eea;"></i> About Scheduled Leaves</h5>
                <p style="margin-bottom: 0; color: #6c757d;">
                    These are leave schedules prepared by HR for your reference. 
                    <strong>Please note:</strong> These are not formal leave applications. 
                    You need to apply formally through the system when it's time to take your scheduled leave. 
                    If you cannot take the scheduled leave, your leave balance will not be affected.
                </p>
            </div>

            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;
                    <strong>{{ session()->get('success') }}</strong>
                </div>
            @endif

            <!-- Upcoming Scheduled Leaves -->
            <h4 style="margin-bottom: 20px; color: #333;">
                <i class="fa fa-calendar-check-o" style="color: #28a745;"></i> Upcoming Scheduled Leaves
            </h4>

            @if ($upcomingSchedules->isEmpty())
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> No upcoming scheduled leaves at this time.
                </div>
            @else
                @foreach ($upcomingSchedules as $schedule)
                    <div class="schedule-card upcoming {{ $schedule->notification_sent ? 'notification-sent' : '' }}">
                        <div class="schedule-header">
                            <div>
                                <h5 class="schedule-title">{{ $schedule->leaveType->leave_type_name ?? 'Leave' }}</h5>
                                @if ($schedule->purpose)
                                    <small class="text-muted">{{ $schedule->purpose }}</small>
                                @endif
                            </div>
                            <span class="status-badge status-{{ $schedule->status }}">
                                {{ ucfirst($schedule->status) }}
                            </span>
                        </div>

                        <div class="schedule-details">
                            <div class="detail-item">
                                <i class="fa fa-calendar"></i>
                                <div>
                                    <div class="detail-label">From Date</div>
                                    <div class="detail-value">{{ dateConvertDBtoForm($schedule->scheduled_from_date) }}</div>
                                </div>
                            </div>
                            <div class="detail-item">
                                <i class="fa fa-calendar"></i>
                                <div>
                                    <div class="detail-label">To Date</div>
                                    <div class="detail-value">{{ dateConvertDBtoForm($schedule->scheduled_to_date) }}</div>
                                </div>
                            </div>
                            <div class="detail-item">
                                <i class="fa fa-clock-o"></i>
                                <div>
                                    <div class="detail-label">Days</div>
                                    <div class="detail-value">{{ $schedule->number_of_days }} days</div>
                                </div>
                            </div>
                            @if ($schedule->notification_sent)
                                <div class="detail-item">
                                    <i class="fa fa-bell" style="color: #17a2b8;"></i>
                                    <div>
                                        <div class="detail-label">Reminder</div>
                                        <div class="detail-value">Sent on {{ $schedule->notification_sent_at->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if ($schedule->remarks)
                            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #e0e0e0;">
                                <small class="text-muted">
                                    <strong>Remarks:</strong> {{ $schedule->remarks }}
                                </small>
                            </div>
                        @endif

                        <div class="apply-btn">
                            <a href="{{ route('ess.leave.form') }}" class="btn btn-success">
                                <i class="fa fa-paper-plane"></i> Apply for Leave Now
                            </a>
                            <small class="text-muted" style="margin-left: 10px;">
                                Schedule ID: {{ $schedule->id }}
                            </small>
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- Past Scheduled Leaves -->
            @if ($pastSchedules->isNotEmpty())
                <h4 style="margin-top: 30px; margin-bottom: 20px; color: #333;">
                    <i class="fa fa-history" style="color: #6c757d;"></i> Past Scheduled Leaves
                </h4>

                @foreach ($pastSchedules as $schedule)
                    <div class="schedule-card past">
                        <div class="schedule-header">
                            <div>
                                <h5 class="schedule-title">{{ $schedule->leaveType->leave_type_name ?? 'Leave' }}</h5>
                                @if ($schedule->purpose)
                                    <small class="text-muted">{{ $schedule->purpose }}</small>
                                @endif
                            </div>
                            <span class="status-badge status-{{ $schedule->status }}">
                                {{ ucfirst($schedule->status) }}
                            </span>
                        </div>

                        <div class="schedule-details">
                            <div class="detail-item">
                                <i class="fa fa-calendar"></i>
                                <div>
                                    <div class="detail-label">From Date</div>
                                    <div class="detail-value">{{ dateConvertDBtoForm($schedule->scheduled_from_date) }}</div>
                                </div>
                            </div>
                            <div class="detail-item">
                                <i class="fa fa-calendar"></i>
                                <div>
                                    <div class="detail-label">To Date</div>
                                    <div class="detail-value">{{ dateConvertDBtoForm($schedule->scheduled_to_date) }}</div>
                                </div>
                            </div>
                            <div class="detail-item">
                                <i class="fa fa-clock-o"></i>
                                <div>
                                    <div class="detail-label">Days</div>
                                    <div class="detail-value">{{ $schedule->number_of_days }} days</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
