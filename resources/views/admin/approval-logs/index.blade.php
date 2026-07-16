@extends('admin.master')

@section('title')
    Approval Logs
@endsection

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
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
                <div class="card-body">
                    <p class="text-muted" style="padding: 0 15px;">
                        Approval workflow history across the system, including stages, approvers, and action times.
                    </p>

                    <div class="table-responsive">
                        <table id="example1" class="table table-sm table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Date / Time</th>
                                    <th>Record</th>
                                    <th>Stage</th>
                                    <th>Action</th>
                                    <th>Approved / Actioned By</th>
                                    <th>Comments</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $log)
                                    @php
                                        $actionLabel = ucfirst(str_replace('_', ' ', (string) $log->action));
                                        $actionClass = match ($log->action) {
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            'reviewed' => 'primary',
                                            'submitted' => 'info',
                                            'pending', 'queued' => 'warning',
                                            default => 'default',
                                        };

                                        $actorName = 'System';
                                        if ($log->user) {
                                            if ($log->user->employeeDetails) {
                                                $actorName = method_exists($log->user->employeeDetails, 'fullName')
                                                    ? $log->user->employeeDetails->fullName()
                                                    : trim(($log->user->employeeDetails->first_name ?? '') . ' ' . ($log->user->employeeDetails->last_name ?? ''));
                                            } else {
                                                $actorName = $log->user->user_name
                                                    ?? $log->user->email
                                                    ?? ('User #' . $log->user->id);
                                            }
                                        }

                                        $recordLabel = class_basename((string) $log->approvable_type) . ' #' . $log->approvable_id;
                                        if ($log->approvable) {
                                            $recordLabel = class_basename($log->approvable_type);
                                            if (isset($log->approvable->requisition_number)) {
                                                $recordLabel .= ' ' . $log->approvable->requisition_number;
                                            } elseif (isset($log->approvable->case_number)) {
                                                $recordLabel .= ' ' . $log->approvable->case_number;
                                            } elseif (isset($log->approvable->payroll_number)) {
                                                $recordLabel .= ' ' . $log->approvable->payroll_number;
                                            } elseif (method_exists($log->approvable, 'getKey')) {
                                                $recordLabel .= ' #' . $log->approvable->getKey();
                                            }
                                        }

                                        $stageLabel = 'N/A';
                                        if ($log->step) {
                                            $stageParts = array_filter([
                                                $log->step->name,
                                                $log->step->type ? ucfirst($log->step->type) : null,
                                                $log->step->level !== null ? 'Level ' . $log->step->level : null,
                                            ]);
                                            $stageLabel = implode(' · ', $stageParts);
                                        }

                                        $actionAt = $log->action_date ?? $log->created_at;
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $actionAt ? \Carbon\Carbon::parse($actionAt)->format('Y-m-d H:i:s') : '—' }}
                                        </td>
                                        <td>
                                            <strong>{{ $recordLabel }}</strong>
                                            @if($log->step && $log->step->workflow)
                                                <br><small class="text-muted">{{ class_basename($log->step->workflow->model_type ?? $log->approvable_type) }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $stageLabel }}</td>
                                        <td>
                                            <span class="label label-{{ $actionClass }}">{{ $actionLabel }}</span>
                                        </td>
                                        <td>
                                            {{ $actorName }}
                                            @if($log->user && $log->user->email)
                                                <br><small class="text-muted">{{ $log->user->email }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $log->comments ?: '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No approval logs found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
