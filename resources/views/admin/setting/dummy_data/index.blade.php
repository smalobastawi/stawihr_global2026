@extends('admin.master')
@section('title')
    Dummy Test Data
@endsection
@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-database fa-fw"></i> Dummy / Test Data Generator
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif

                        @if(session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif

                        <p class="text-muted">
                            Generate sample employees and related HR records for demos and testing.
                            Dummy data is tracked separately from initial seed data and can be fully removed without affecting
                            seeded users such as the super admin or default test employee.
                        </p>

                        <div class="alert alert-warning">
                            <strong>Important:</strong>
                            Dummy employees use emails ending in <code>{{ '@' }}stawihr-dummy.test</code> and payroll numbers prefixed with <code>DUM</code>.
                            Default login password for dummy users is <code>password123</code>.
                        </div>

                        @if($has_data)
                            <h4 class="box-title m-t-20">Current dummy data summary</h4>
                            @if(!empty($batch?->created_at))
                                <p class="text-muted">
                                    Generated on {{ $batch->created_at->format('d M Y H:i') }}
                                    @if($batch->user)
                                        by {{ $batch->user->user_name ?? $batch->user->email }}
                                    @endif
                                </p>
                            @endif

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Data type</th>
                                        <th class="text-right">Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $labels = [
                                            'employees' => 'Employees',
                                            'users' => 'User accounts',
                                            'staff_contracts' => 'Staff contracts',
                                            'employee_payrolls' => 'Payroll profiles',
                                            'payroll_records' => 'Payroll records',
                                            'payroll_periods_used' => 'Payroll periods covered',
                                            'disciplinary_cases' => 'Disciplinary cases',
                                            'employee_feedback' => 'Employee feedback',
                                            'anonymous_feedback' => 'Anonymous feedback',
                                            'trainings' => 'Training sessions',
                                            'training_invitees' => 'Training invitees',
                                            'training_attendants' => 'Training attendances',
                                            'leave_applications' => 'Leave applications',
                                            'attendance_records' => 'Attendance records',
                                            'employee_leavegroups' => 'Leave group assignments',
                                            'job_requisitions' => 'Job requisitions',
                                            'job_posts' => 'Job posts',
                                            'job_applications' => 'Job applications',
                                            'vehicles' => 'Vehicles',
                                            'vehicle_assignments' => 'Vehicle assignments',
                                            'notices' => 'Notices',
                                            'review_periods' => 'Review periods (policy)',
                                            'performance_rating_scales' => 'Rating scales (policy)',
                                            'performance_behavioral_items' => 'Behavioral items (policy)',
                                            'pdp_settings' => 'PDP policy settings',
                                            'performance_appraisals' => 'Performance appraisals',
                                            'performance_appraisal_scores' => 'Performance rating scores',
                                            'pip_plans' => 'Performance improvement plans',
                                            'pip_goals' => 'PIP goals',
                                            'pdp_plans' => 'Personal development plans',
                                            'pdp_goals' => 'PDP goals',
                                            'pdp_progress_entries' => 'PDP progress entries',
                                        ];
                                        $displaySummary = !empty($summary) ? $summary : $counts;
                                    @endphp
                                    @foreach($labels as $key => $label)
                                        @if(!empty($displaySummary[$key]))
                                            <tr>
                                                <td>{{ $label }}</td>
                                                <td class="text-right">{{ number_format($displaySummary[$key]) }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>

                            @if(!empty($counts))
                                <h5 class="m-t-20">Registry breakdown</h5>
                                <table class="table table-condensed table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Table</th>
                                            <th class="text-right">Records tracked</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($counts as $table => $total)
                                            <tr>
                                                <td><code>{{ $table }}</code></td>
                                                <td class="text-right">{{ number_format($total) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                            <form method="POST" action="{{ route('dummyData.destroy') }}" class="m-t-20"
                                  onsubmit="return confirm('Remove all dummy test data? Initial seed data will not be affected.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fa fa-trash"></i> Remove All Dummy Data
                                </button>
                            </form>
                        @else
                            <p>No dummy data is currently loaded in the system.</p>

                            <form method="POST" action="{{ route('dummyData.generate') }}" class="m-t-20"
                                  onsubmit="return confirm('Generate dummy test data? This will create 15 employees and related records.');">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fa fa-magic"></i> Generate Dummy Test Data
                                </button>
                            </form>

                            <h5 class="m-t-30">What will be created</h5>
                            <ul>
                                <li>15 employees with user accounts, contracts, and payroll profiles</li>
                                <li>Payroll calculation records for the latest 3 payroll periods</li>
                                <li>Disciplinary cases, named employee feedback, and anonymous feedback</li>
                                <li>10 training sessions with invitees and attendances</li>
                                <li>10 job requisitions, 10 job posts, and 10 job applications</li>
                                <li>10 vehicles with vehicle assignments</li>
                                <li>10 notices (with department/location targeting)</li>
                                <li>Performance policy settings (review periods, rating scales, behavioral items, PDP settings as needed)</li>
                                <li>10 performance appraisals with rating scores</li>
                                <li>5 performance improvement plans with goals, support, and reviews</li>
                                <li>8 personal development plans (active with progress and completed)</li>
                                <li>Leave applications and attendance entries</li>
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
