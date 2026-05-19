@extends('admin.master')

@section('title')
   Approvals Index
@endsection
@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i
                                class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        {{-- <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <a href="{{ route('advances.create') }}" class="btn btn-success pull-right m-l-20 waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i>New Request</a>
        </div> --}}
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if(session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table id="myTableAdvances" class="table table-bordered">
                                <thead>
                                <tr class="tr_header">
                                    <th>@lang('common.serial')</th>
                                    <th>Action type</th>
                                    <th>Requested by</th>
                                    <th>Affected staff</th>
                                    <th>Request Date</th>
                                    <th>Current Approver</th>
                                    <th>Final Approval</th>
                                    <th>Request Details</th>
                                    <th>Notes</th>
                                    <th>@lang('common.action')</th>
                                </tr>
                                </thead>
                                <tbody>
                                    {!! $sl=null !!}
                                    @foreach($data AS $value)
                                        <tr >
                                            <td style="width: 100px;">{!! ++$sl !!}</td>
                                            <td>{!! $value->action_type !!}</td>
                                                @if($value->requester)
                                                    <td>{{ $value->requester->user_name}} </td>
                                                @else
                                                <td> Unknown</td>
                                                @endif                                        
                                                <td></td>
                                                <td>{!! $value->created_at !!}</td>
                                                                                
                                                <td>{!! $value->next_approver !!}</td>
                                            <td>{!! $value->final_approver !!}</td>
                                            <td>
                                                @php
                                                    $newData = json_decode($value->new, true);
                                                    $oldData = json_decode($value->old, true);
                                                    $dataToDisplay = $newData ?: $oldData;
                                            
                                                    if ($dataToDisplay) {
                                                        // Remove 'Token' and 'Holiday Id' keys from the data
                                                        if(isset($dataToDisplay['holiday_id'])) {
                                                            $dataToDisplay['approval_type'] = 'Holiday Approval';
                                                        }
                                                        $excludedKeys = ['_token', 'holiday_id','from_date','to_date'];
                                                        $dataToDisplay = array_filter($dataToDisplay, function($key) use ($excludedKeys) {
                                                            return !in_array(strtolower($key), $excludedKeys);
                                                        }, ARRAY_FILTER_USE_KEY);
                                                        
                                                        $limitedData = array_slice($dataToDisplay, 0, 2, true);
                                                    }

                                                    // dd($limitedData);

                                                @endphp
                                            
                                                @if($dataToDisplay)
                                                    <p>
                                                        {{ implode(', ', array_map(function($key, $val) {
                                                            return ucwords(str_replace('_', ' ', $key)) . ': ' . $val;
                                                        }, array_keys($limitedData), $limitedData)) }}
                                                        
                                                        @if(count($dataToDisplay) > 5)
                                                            ...
                                                        @endif
                                                    </p>
                                                @else
                                                    No Data
                                                @endif
                                            </td>
                                            
                                            <td></td>
                                            <td style="width: 100px;">
                                                <a href="{!! route('approvals.view',$value->id ) !!}"
                                                class="btn btn-success btn-xs btnColor">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>View more
                                                </a>

                                                <a href="{!! route('approvals.show',$value ) !!}"
                                                class="btn btn-success btn-xs btnColor">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                </a>
                                            
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
@endsection

