<!-- Salary Change History Section -->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title"><i class="fa fa-line-chart"></i> @lang('payroll.salary_change_history')</h4>
            </div>
            <div class="panel-body">
                @php
                    $salaryHistory = app('App\Services\Payroll\PayrollChangeService')->getSalaryHistory(
                        $employeePayroll->employee_id,
                    );
                @endphp

                @if ($salaryHistory->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr class="tr_header">
                                    <th>@lang('payroll.effective_date')</th>
                                    <th>@lang('payroll.previous_salary')</th>
                                    <th>@lang('payroll.new_salary')</th>
                                    <th>@lang('payroll.change_amount')</th>
                                    <th>@lang('payroll.change_percentage')</th>
                                    <th>@lang('payroll.change_type')</th>
                                    <th>@lang('payroll.change_reason')</th>
                                    <th>@lang('common.changed_by')</th>
                                    <th>@lang('common.date_changed')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($salaryHistory as $history)
                                    <tr>
                                        <td>
                                            <strong>{{ $history->effective_date->format('d M Y') }}</strong>
                                        </td>
                                        <td class="text-right">
                                            <span class="text-muted">KES
                                                {{ number_format($history->previous_salary, 2) }}</span>
                                        </td>
                                        <td class="text-right">
                                            <strong class="text-success">KES
                                                {{ number_format($history->new_salary, 2) }}</strong>
                                        </td>
                                        <td class="text-right">
                                            <span
                                                class="label label-{{ $history->salary_change_amount >= 0 ? 'success' : 'danger' }}">
                                                {{ $history->salary_change_amount >= 0 ? '+' : '' }}KES
                                                {{ number_format(abs($history->salary_change_amount), 2) }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <span
                                                class="label label-{{ $history->salary_change_percentage >= 0 ? 'success' : 'danger' }}">
                                                {{ $history->salary_change_percentage >= 0 ? '+' : '' }}{{ number_format($history->salary_change_percentage, 2) }}%
                                            </span>
                                        </td>
                                        <td>
                                            <span class="label label-info">
                                                {{ ucfirst(str_replace('_', ' ', $history->change_type)) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $history->change_reason }}
                                            @if ($history->metadata && is_array($history->metadata) && isset($history->metadata['updated_fields']))
                                                <br>
                                                <small class="text-muted">
                                                    <strong>Other changes:</strong>
                                                    {{ implode(', ', array_keys($history->metadata['updated_fields'])) }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $history->changedBy->name ?? 'System' }}</small>
                                        </td>
                                        <td>
                                            <small
                                                class="text-muted">{{ $history->created_at->format('d M Y H:i') }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Salary Progression Summary -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <h5 class="panel-title"><i class="fa fa-bar-chart"></i> @lang('payroll.salary_progression_summary')</h5>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-3 text-center">
                                            <div class="stat-item">
                                                <h3 class="text-primary">{{ $salaryHistory->count() }}</h3>
                                                <p><strong>@lang('payroll.total_changes')</strong></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <div class="stat-item">
                                                @php
                                                    $firstSalary =
                                                        $salaryHistory->last()->previous_salary ??
                                                        $employeePayroll->basic_salary;
                                                    $currentSalary = $employeePayroll->basic_salary;
                                                    $totalGrowth = $currentSalary - $firstSalary;
                                                    $growthPercentage =
                                                        $firstSalary > 0 ? ($totalGrowth / $firstSalary) * 100 : 0;
                                                @endphp
                                                <h3 class="text-success">+{{ number_format($growthPercentage, 1) }}%
                                                </h3>
                                                <p><strong>@lang('payroll.total_growth')</strong></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <div class="stat-item">
                                                <h3 class="text-info">KES
                                                    {{ number_format($salaryHistory->avg('salary_change_amount'), 2) }}
                                                </h3>
                                                <p><strong>@lang('payroll.average_change')</strong></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <div class="stat-item">
                                                @php
                                                    $increases = $salaryHistory
                                                        ->where('salary_change_amount', '>', 0)
                                                        ->count();
                                                    $increasePercentage =
                                                        $salaryHistory->count() > 0
                                                            ? round(($increases / $salaryHistory->count()) * 100, 1)
                                                            : 0;
                                                @endphp
                                                <h3 class="text-warning">{{ $increasePercentage }}%</h3>
                                                <p><strong>@lang('payroll.positive_changes')</strong></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Salary Timeline (Optional - remove if causing issues) -->
                                    @if ($salaryHistory->count() > 1)
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <h6><strong>@lang('payroll.salary_timeline'):</strong></h6>
                                                <div class="salary-timeline"
                                                    style="padding: 10px; background-color: #f5f5f5; border-radius: 5px;">
                                                    @php
                                                        $sortedHistory = $salaryHistory->sortBy('effective_date');
                                                        $minSalary = $sortedHistory->min('previous_salary');
                                                        $maxSalary = $sortedHistory->max('new_salary');
                                                        $range = $maxSalary - $minSalary;
                                                    @endphp
                                                    @foreach ($sortedHistory as $index => $history)
                                                        @php
                                                            $position =
                                                                $range > 0
                                                                    ? (($history->new_salary - $minSalary) / $range) *
                                                                        100
                                                                    : 50;
                                                            $color =
                                                                $history->salary_change_amount >= 0
                                                                    ? '#5cb85c'
                                                                    : '#d9534f';
                                                        @endphp
                                                        <div
                                                            style="display: inline-block; position: relative; margin-right: 20px;">
                                                            <div
                                                                style="width: 12px; height: 12px; background-color: {{ $color }}; border-radius: 50%; display: inline-block; margin-right: 5px;">
                                                            </div>
                                                            <small style="color: #333;">
                                                                {{ $history->effective_date->format('M Y') }}:
                                                                <strong>KES
                                                                    {{ number_format($history->new_salary, 0) }}</strong>
                                                            </small>
                                                        </div>
                                                        @if (!$loop->last)
                                                            <div
                                                                style="display: inline-block; width: 20px; height: 2px; background-color: #ddd; margin-right: 5px;">
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        <strong>@lang('payroll.no_salary_history'):</strong>
                        @lang('payroll.no_salary_changes_recorded')
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
