@php
    $selectedStartPeriod = old(
        'payroll_period_id',
        isset($editModeData) ? $editModeData->payroll_period_id : (isset($currentPayrollPeriod) ? $currentPayrollPeriod->id : '')
    );
    $selectedEndPeriod = old(
        'end_payroll_period_id',
        isset($editModeData) ? $editModeData->end_payroll_period_id : ''
    );
    $isRecurringChecked = (bool) old('is_recurring', isset($editModeData) ? $editModeData->is_recurring : false);
@endphp

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label col-md-4" for="payroll_period_id">
                <span id="start_period_label">Payroll Period</span>
                <span class="validateRq">*</span>
                @include('admin.partials.field-tooltip', ['tooltip' => 'The payroll period when this amount applies. For one-time items, select the single period only.'])
            </label>
            <div class="col-md-8">
                <select name="payroll_period_id"
                        class="form-control required payroll_period_id"
                        id="payroll_period_id">
                    <option value="">Select payroll period</option>
                    @foreach ($payrollPeriods as $period)
                        <option value="{{ $period->id }}"
                            {{ (string) $selectedStartPeriod === (string) $period->id ? 'selected' : '' }}>
                            {{ $period->name }}
                            ({{ ($period->input_period_start ?? $period->start_date)?->format('M d, Y') }}
                            - {{ ($period->input_period_end ?? $period->end_date)?->format('M d, Y') }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="col-md-6" id="end_payroll_period_group" style="{{ $isRecurringChecked ? '' : 'display:none;' }}">
        <div class="form-group">
            <label class="control-label col-md-4" for="end_payroll_period_id">
                End Payroll Period
                <span class="validateRq recurring-required" style="{{ $isRecurringChecked ? '' : 'display:none;' }}">*</span>
                @include('admin.partials.field-tooltip', ['tooltip' => 'Last payroll period for a recurring earning or deduction. Leave blank only when the item repeats indefinitely from the start period.'])
            </label>
            <div class="col-md-8">
                <select name="end_payroll_period_id"
                        class="form-control end_payroll_period_id"
                        id="end_payroll_period_id">
                    <option value="">Select end period (optional)</option>
                    @foreach ($payrollPeriods as $period)
                        <option value="{{ $period->id }}"
                            {{ (string) $selectedEndPeriod === (string) $period->id ? 'selected' : '' }}>
                            {{ $period->name }}
                            ({{ ($period->input_period_start ?? $period->start_date)?->format('M d, Y') }}
                            - {{ ($period->input_period_end ?? $period->end_date)?->format('M d, Y') }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        function toggleRecurringPeriodFields() {
            var isRecurring = document.getElementById('is_recurring') && document.getElementById('is_recurring').checked;
            var frequency = document.getElementById('frequency') ? document.getElementById('frequency').value : '';
            var showEnd = isRecurring && frequency !== 'one_time';
            var endGroup = document.getElementById('end_payroll_period_group');
            var startLabel = document.getElementById('start_period_label');
            var recurringRequired = document.querySelector('.recurring-required');

            if (endGroup) {
                endGroup.style.display = showEnd ? '' : 'none';
            }
            if (startLabel) {
                startLabel.textContent = showEnd ? 'Start Payroll Period' : 'Payroll Period';
            }
            if (recurringRequired) {
                recurringRequired.style.display = showEnd ? '' : 'none';
            }

            var endSelect = document.getElementById('end_payroll_period_id');
            if (endSelect) {
                if (showEnd) {
                    endSelect.classList.add('required');
                } else {
                    endSelect.classList.remove('required');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var recurring = document.getElementById('is_recurring');
            var frequency = document.getElementById('frequency');

            if (recurring) {
                recurring.addEventListener('change', toggleRecurringPeriodFields);
            }
            if (frequency) {
                frequency.addEventListener('change', toggleRecurringPeriodFields);
            }

            toggleRecurringPeriodFields();
        });
    })();
</script>
