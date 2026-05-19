@extends('admin.master')
@section('content')
@section('title')
    Leave Schedule Management
@endsection

<style>
    .status-badge {
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-scheduled { background: #fff3cd; color: #856404; }
    .status-applied { background: #d4edda; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }
    .status-completed { background: #d1ecf1; color: #0c5460; }
</style>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li>Leave Schedule Management</li>
            </ol>
        </div>
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
            <div class="pull-right">
                <a href="{{ route('leave.schedule.bulkUpload') }}" class="btn btn-info m-l-10">
                    <i class="fa fa-upload"></i> Bulk Upload
                </a>
                <a href="{{ route('leave.schedule.create') }}" class="btn btn-success m-l-10">
                    <i class="fa fa-plus"></i> Add Schedule
                </a>
                <button onclick="sendReminders()" class="btn btn-warning m-l-10">
                    <i class="fa fa-bell"></i> Send Reminders
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-calendar-clock fa-fw"></i> Leave Schedules
                    <span class="badge badge-info">{{ $schedules->total() }} total</span>
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;
                                <strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif

                        @if (session()->has('warning'))
                            <div class="alert alert-warning alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="fa fa-exclamation-triangle"></i>&nbsp;
                                <strong>{{ session()->get('warning') }}</strong>
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="glyphicon glyphicon-remove"></i>&nbsp;
                                <strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif

                        @if (session()->has('import_errors'))
                            <div class="alert alert-warning">
                                <h5><i class="fa fa-exclamation-circle"></i> Import Errors:</h5>
                                <ul>
                                    @foreach (session()->get('import_errors') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>ID</th>
                                        <th>Employee</th>
                                        <th>Leave Type</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Days</th>
                                        <th>Status</th>
                                        <th>Notification</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($schedules as $schedule)
                                        <tr>
                                            <td>{{ $schedule->id }}</td>
                                            <td>{{ $schedule->employee->first_name ?? '' }} {{ $schedule->employee->last_name ?? '' }}</td>
                                            <td>{{ $schedule->leaveType->leave_type_name ?? 'N/A' }}</td>
                                            <td>{{ dateConvertDBtoForm($schedule->scheduled_from_date) }}</td>
                                            <td>{{ dateConvertDBtoForm($schedule->scheduled_to_date) }}</td>
                                            <td>{{ $schedule->number_of_days }}</td>
                                            <td>
                                                <span class="status-badge status-{{ $schedule->status }}">
                                                    {{ ucfirst($schedule->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($schedule->notification_sent)
                                                    <span class="label label-success">
                                                        <i class="fa fa-check"></i> Sent
                                                    </span>
                                                    <br>
                                                    <small>{{ $schedule->notification_sent_at->format('d/m/Y H:i') }}</small>
                                                @else
                                                    <span class="label label-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td style="width: 120px;">
                                                <a href="{{ route('leave.schedule.edit', $schedule->id) }}" class="btn btn-success btn-xs">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <a href="javascript:void(0)" onclick="deleteSchedule({{ $schedule->id }})" class="btn btn-danger btn-xs">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="pagination-wrapper" style="text-align: center; padding: 20px;">
                            {{ $schedules->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
<script>
    function deleteSchedule(id) {
        if (confirm('Are you sure you want to delete this leave schedule?')) {
            $.ajax({
                url: '{{ url('leaveManagement/leaveSchedule') }}/' + id + '/delete',
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the schedule.');
                }
            });
        }
    }

    function sendReminders() {
        if (confirm('Send reminder notifications for all upcoming scheduled leaves?')) {
            $.ajax({
                url: '{{ route('leave.schedule.reminders') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while sending reminders.');
                }
            });
        }
    }
</script>
@endsection
