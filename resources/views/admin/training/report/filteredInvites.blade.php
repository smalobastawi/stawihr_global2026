
    <table class="table table-bordered">
        <thead class="tr_header">
            <tr>
                <th style="width:100px;">@lang('common.serial')</th>
                <th>@lang('training.training_type')</th>
                <th>@lang('training.training')</th>
                <th>@lang('training.start_date')</th>
                <th>@lang('training.end_date')</th>
                <th>@lang('training.facilitator')</th>
                <th>@lang('common.department')</th>
                <th>@lang('common.employee')</th>
                <th>@lang('training.invitation')</th>
            </tr>
        </thead>
        <tbody>
            @if($results->isNotEmpty())
                @php $sl = 0; @endphp
                @foreach($results as $value)
                    <tr>
                        <td>{{ ++$sl }}</td>
                        <td>{{ $value->training_type }}</td>
                        <td>{{ $value->training }}</td>
                        <td>{{ $value->start_date }}</td>
                        <td>{{ $value->end_date }}</td> 
                        <td>{{ $value->facilitator_name }}({{ $value->facilitator_type }})</td>
                        <td>{{ $value->employee_department }}</td>
                        <td>{{ $value->employee_name }}</td>
                        <td>{{ TrainingInvitationStatus::getName($value->invited_status) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="13">@lang('common.no_data_available') !</td>
                </tr>
            @endif
        </tbody>
    </table>
