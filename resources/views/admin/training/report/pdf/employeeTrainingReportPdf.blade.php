<!DOCTYPE html>
<html lang="en">
<head>
    <title>@lang('training.employee_training_report')</title>
    <meta charset="utf-8">
    <style>
        /* body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
        }
        .report-container {
            width: 90%;
            margin: 0 auto;
            text-align: center;
        } */
        .header {
            margin-bottom: 20px;
        }
        .details {
            text-align: left;
            margin-bottom: 20px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid black;
            padding: 2px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
		.details-container {
        width: 60%;
        margin: 20px auto;
        border-collapse: collapse;
        border: 1px solid #ddd;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
        background: #f9f9f9;
    }
    
    .details-container th, .details-container td {
        padding: 4px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .details-container th {
        background: #007bff;
        color: white;
        text-align: center;
    }

    .details-container td {
        background: #fff;
    }
    </style>
</head>
<body>
<div class="report-container">
    <div class="header">
        @if($printHead)
            {!! $printHead->description !!}
        @endif
    </div>
@php
	$columns=[];
@endphp

	<table class="details-container">
		<thead>
			<tr><th colspan="2">Training Report Details</th></tr>
		</thead>
		<tbody>
			@if($employee)
				<tr><td><strong>Employee</strong></td><td>{{ $employee->fullName() }}</td></tr>
			@else
				@php $columns[] = 'employee_name'; @endphp
			@endif
			
			@if($department)
				<tr><td><strong>Department</strong></td><td>{{ $department->department_name }}</td></tr>
			@else
				@php $columns[] = 'employee_department'; @endphp
			@endif
	
			@if($trainingType)
				<tr><td><strong>Training Type</strong></td><td>{{ $trainingType->training_type_name }}</td></tr>
			@else
				@php $columns[] = 'training_type'; @endphp
			@endif
	
			@if($training)
				<tr><td><strong>Training</strong></td><td>{{ $training->description }}</td></tr>
			@else
				@php $columns[] = 'training'; @endphp
			@endif
	
			@if($facilitator)
				<tr><td><strong>Facilitator</strong></td><td>{{ $facilitator->name }} ({{ $facilitator->type }})</td></tr>
			@else
				@php $columns[] = 'facilitator_name'; @endphp
			@endif
		</tbody>
	</table>
 
	
    <table>
        <thead>
            <tr>
                <th>@lang('common.serial')</th>
                @if(in_array('training_type', $columns))<th>@lang('training.training_type')</th>@endif
                @if(in_array('training', $columns))<th>@lang('training.training')</th>@endif
                @if(in_array('facilitator_name', $columns))<th>@lang('training.facilitator')</th>@endif
                <th>@lang('training.start_date')</th>
                <th>@lang('training.end_date')</th>
                @if(in_array('employee_department', $columns))<th>Department</th>@endif
                @if(in_array('employee_name', $columns))<th>@lang('common.employee')</th>@endif
                <th>@lang('training.invitation')</th>
                <th>Attendance</th> 
            </tr>
        </thead>
        <tbody>
            @if($results->isNotEmpty())
                @php $sl = 0; @endphp
                @foreach($results as $value)
                    <tr>
                        <td>{{ ++$sl }}</td>
                        @if(in_array('training_type', $columns))<td>{{ $value->training_type }}</td>@endif
                        @if(in_array('training', $columns))<td>{{ $value->training }}</td>@endif
                        @if(in_array('facilitator_name', $columns))<td>{{ $value->facilitator_name }} ({{ $value->facilitator_type }})</td>@endif
                        <td>{{ $value->start_date }}</td>
                        <td>{{ $value->end_date }}</td>
                        @if(in_array('employee_department', $columns))<td>{{ $value->employee_department }}</td>@endif
                        @if(in_array('employee_name', $columns))<td>{{ $value->employee_name }}</td>@endif
                        <td>{{ TrainingInvitationStatus::getName($value->invited_status) }}</td>
                        <td>{{ TrainingAttendanceStatus::getName($value->attendance_status) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="12">@lang('common.no_data_available')</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
</body>
</html>
