@extends('admin.master')

@section('title', 'Notices & Announcements')

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor">
                        <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                    </li>
                    <li>@yield('title')</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-bullhorn fa-fw"></i> @yield('title')
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if (session()->has('success'))
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <strong>{{ session()->get('success') }}</strong>
                                </div>
                            @endif
                            @if (session()->has('error'))
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <strong>{{ session()->get('error') }}</strong>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table id="myTable" class="table table-bordered">
                                    <thead>
                                        <tr class="tr_header">
                                            <th>@lang('common.serial')</th>
                                            <th>@lang('notice.title')</th>
                                            <th>@lang('notice.publish_date')</th>
                                            <th>Targeted Audience</th>
                                            <th>@lang('common.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $sl = 0; @endphp
                                        @forelse ($results as $notice)
                                            <tr>
                                                <td>{{ ++$sl }}</td>
                                                <td>{{ $notice->title }}</td>
                                                <td>{{ dateConvertDBtoForm($notice->publish_date) }}</td>
                                                <td><small>{{ $notice->targeted_audience_summary }}</small></td>
                                                <td>
                                                    <a href="{{ route('ess.notices.show', $notice->notice_id) }}"
                                                        class="btn btn-primary btn-xs btnColor" title="View Notice">
                                                        <i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No notices available for you at this time.</td>
                                            </tr>
                                        @endforelse
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
