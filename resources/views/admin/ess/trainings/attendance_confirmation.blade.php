@extends('admin.master')

@section('title', trans('training.attendance_response'))

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor">
                        <a href="{{ url('dashboard') }}">
                            <i class="fa fa-home"></i> @lang('dashboard.dashboard')
                        </a>
                    </li>
                    <li>@yield('title')</li>
                </ol>
            </div>
        </div>

         <div class="row">
            <div class="col-sm-12">
                <br>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h2>Confirm Attendance: <small> {{ $training->subject }}</small></h2>
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <p><strong>Dates:</strong> {{ $training->start_date->format('M j, Y') }} to {{ $training->end_date->format('M j, Y') }}</p>
                            <p><strong>Description:</strong> {{ $training->description }}</p>
                            <br>
                            <form method="POST" action="{{ route('ess.trainings.attendance.confirm', [
                                'training' => $training->id,
                                'employee' => $employee->employee_id,
                            ]) }}">
                                @csrf
                                <div class="form-group mt-4" style="margin-top:1rem;">
                                    <button type="submit" name="status" value="confirmed" class="btn btn-success btn-lg mr-3" style="margin-right: 3px;">
                                        Confirm Attendance
                                    </button>
                                    <button type="submit" name="status" value="declined" class="btn btn-danger btn-lg">
                                        Decline Attendance
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection