@extends('admin.master')
@section('content')
@section('title')
 Anonymous feedback
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
           <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>	
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <a href="{{ route('ess.feedback.create') }}"  class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i class="fa fa-plus-circle" aria-hidden="true"></i> Add New</a>
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
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                     <tr class="tr_header">
                                        <th>@lang('common.serial')</th>
                                        <th>Category</th>
                                        <th>Title</th>
                                        <th>Content</th>
                                        <th>Date</th>
                                        <th>Updated at</th>
                                        <th>Review comments</th>
                                        <th>Status</th>
                                        <th style="text-align: center;">@lang('common.action')</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl=null !!}
                                    @foreach($data AS $value)
                                        <tr class="{!! $value->id !!}">
                                            <td >{!! ++$sl !!}</td>
                                            <td>{!! $value->category->name !!}</td>
                                            <td>{!! $value->title !!}</td>
                                            <td>{!! $value->content !!}</td>
                                            <td>{!! $value->created_at !!}</td>
                                            <td>{!! $value->updated_at !!}</td>
                                            <td>{!! $value->action_description !!}</td>
                                            <td>{!! FeedbackStatus::getName($value->status) !!}</td>
                                            <td style="width: 200px; white-space: nowrap;"> <!-- Increased width & prevent wrapping -->
                                                @if($value->deleted_at)
                                                    <a href="{!! route('anonymous.feedback.restore', $value->id) !!}" 
                                                       data-token="{!! csrf_token() !!}" 
                                                       data-id="{!! $value->id !!}" 
                                                       class="delete btn btn-danger btn-xs deleteBtn btnColor" 
                                                       style="display: inline-block; margin-right: 5px;"> <!-- Force inline & add spacing -->
                                                        <i class="fa fa-undo" aria-hidden="true"></i> Undo
                                                    </a>
                                                @else

                                                   @can('anonymous.feedback.review')
                                                        <a href="{!! route('anonymous.feedback.review', $value->id) !!}"  
                                                           class="btn btn-success btn-xs btnColor"
                                                           style="display: inline-block; margin-right: 5px;"> <!-- Force inline & add spacing -->
                                                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Review
                                                        </a>
                                                        @endcan
                                                        @can('anonymous.feedback.delete')
                                                   
                                                    
                                                    <a href="{!! route('anonymous.feedback.delete', $value->id) !!}" 
                                                       data-token="{!! csrf_token() !!}" 
                                                       data-id="{!! $value->id !!}" 
                                                       class="delete btn btn-danger btn-xs deleteBtn btnColor"
                                                       style="display: inline-block;"> <!-- Force inline -->
                                                        <i class="fa fa-trash-o" aria-hidden="true"></i> Delete
                                                    </a>
                                                    @endcan
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




@endsection
