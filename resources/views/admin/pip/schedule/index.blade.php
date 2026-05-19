@extends('admin.master')
@section('content')
@section('title')
PIP Review Schedule - {{ $plan->employee ? $plan->employee->full_name : '' }}
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                @foreach (urlTree() as $item)
                    <li class="breadcrumb-item text-primary"><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
                @endforeach
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if(session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                    <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>Stage</th>
                                        <th>Scheduled Date</th>
                                        <th>Status</th>
                                        <th>Conducted By</th>
                                        <th>Comments</th>
                                        <th style="text-align: center;">@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl=null !!}
                                    @foreach($results as $value)
                                        <tr class="{!! $value->schedule_id !!}">
                                            <td style="width: 50px;">{!! ++$sl !!}</td>
                                            <td>{{ $value->review_stage }}</td>
                                            <td>{{ $value->scheduled_date ? $value->scheduled_date->format('Y-m-d') : '' }}</td>
                                            <td>
                                                <span class="label label-{{ $value->status == 'completed' ? 'success' : ($value->status == 'missed' ? 'danger' : ($value->status == 'rescheduled' ? 'info' : 'warning')) }}">{{ ucfirst($value->status) }}</span>
                                            </td>
                                            <td>{{ $value->conductor ? $value->conductor->full_name : '-' }}</td>
                                            <td>{{ $value->comments }}</td>
                                            <td style="width: 180px;">
                                                @if($value->status == 'pending')
                                                    <button class="btn btn-success btn-xs btnColor" data-toggle="modal" data-target="#conductModal{{ $value->schedule_id }}">Conduct</button>
                                                    <button class="btn btn-info btn-xs btnColor" data-toggle="modal" data-target="#rescheduleModal{{ $value->schedule_id }}">Reschedule</button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($results as $value)
@if($value->status == 'pending')
<div class="modal fade" id="conductModal{{ $value->schedule_id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('pip.schedule.conduct', $value->schedule_id) }}" method="POST">
                @csrf
            <div class="modal-header">
                <h4 class="modal-title">Conduct Review - {{ $value->review_stage }}</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Comments</label>
                    <textarea name="comments" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Findings</label>
                    <textarea name="findings" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Conduct</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="rescheduleModal{{ $value->schedule_id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form action="{{ route('pip.schedule.reschedule', $value->schedule_id) }}" method="POST">
                @csrf
            <div class="modal-header">
                <h4 class="modal-title">Reschedule Review</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>New Date<span class="validateRq">*</span></label>
                    <input type="date" name="scheduled_date" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-info">Reschedule</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach
@endsection
