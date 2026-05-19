@extends('admin.master')

@section('title')
    Approvals Index
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                            @lang('dashboard.dashboard')</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
            
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if (session()->has('success'))
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <i
                                        class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                </div>
                            @endif
                            @if (session()->has('error'))
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <i
                                        class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table id="myTableAdvances" class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            
                                            <th style="width: 10px;">@lang('common.serial')</th>
                                            <th>Action type</th>
                                            <th>Requested by</th>
                                            <th>Request Date</th>
                                            <th>Current Status</th>
                                            <th>Current Approver</th>
                                            <th>Final Approver</th>
                                            <th>Request Details</th>
                                            <th>@lang('common.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {!! $sl = null !!}
                                        @foreach ($approvalRequests as $approvalRequest)
                                            <tr>
                                                <td style="width: 10px;">{!! ++$sl !!}</td>
                                                <td>{!! $approvalRequest->action_type !!}</td>

                                                <td>
                                                @if( $approvalRequest->requester->employeeDetails)
                                                    {{ $approvalRequest->requester->employeeDetails->fullname() ?? '..' }} 
                                                @endif
                                                </td>

                                                <td>{{ $approvalRequest->created_at }}</td>
                                                <td>{{ $approvalRequest->status }}</td>
                                                <td>
                                                    @if ($approvalRequest->module->approvers()->whereNotIn('user_id', $approvalRequest->approvals()->pluck('approver_id')->toArray())->first())
                                                        {{ $approvalRequest->module->approvers()->whereNotIn('user_id', $approvalRequest->approvals()->pluck('approver_id')->toArray())->first()->user->employeeDetails->fullname() }}
                                                    
                                                    @else
                                                    None
                                                        @endif
                                                    {{-- {{$approvalRequest->module->approvers()
                                                ->whereNotIn('user_id',$approvalRequest->approvals()->pluck('approver_id')->toArray())
                                                ->first()->user->employeeDetails->fullname()}} --}}

                                                </td>
                                                <td>{{ optional($approvalRequest->module->approvers()->orderBy('id', 'desc')->first()?->user?->employeeDetails)->fullname() ?? 'N/A' }}</td>
                                                </td>

                                                <td>
                                                    @if ($approvalRequest->request_data)
                                                        @php
                                                            $requestData = json_decode(
                                                                $approvalRequest->request_data,
                                                                true,
                                                            );
                                                        @endphp
                                                        <ul>
                                                            {{-- @if ($requestData && is_array($requestData))
                                                            @foreach ($requestData as $key => $value)
                                                                @if (!\Illuminate\Support\Str::endsWith($key, '_id'))
                                                                    
                                                                    <li>
                                                                        <strong>{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $key)) }}:</strong>
                                                                        {{ json_encode($value) }}
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                            @endif --}}
                                                        </ul>
                                                    @else
                                                        <em>No Data</em>
                                                    @endif
                                                </td>
                                                <td style="width: 100px;">
                                                    <a href="{!! route('approvals.show', $approvalRequest) !!}"
                                                        class="btn btn-success btn-xs btnColor">
                                                        <i class="fa fa-eye" aria-hidden="true"></i>View more
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
