@extends('admin.master')

@section('title', 'Notifications')

@section('content')

    <style>
        .notification-message {
            font-size: 14px;
            color: #333;
        }

        .notification-message small {
            font-size: 12px;
        }

        .full-message {
            padding: 5px 0;
            font-size: 14px;
            border-top: 1px dashed #eee;
            margin-top: 5px;
        }
    </style>

    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor">
                        <a href="#">
                            <i class="fa fa-home"></i> @lang('dashboard.dashboard')
                        </a>
                    </li>
                    <li>@yield('title')</li>
                </ol>
            </div>
            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                <a href="{{ route('ess.notifications.markAllRead') }}" class="btn btn-success pull-right m-l-20">
                    <i class="fa fa-check-circle"></i> Mark All as Read
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-table fa-fw"></i> @yield('title')
                        <span class="badge badge-primary">
                            {{ auth()->user()->unreadNotifications->count() }} Unread
                        </span>
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if (session()->has('success'))
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;
                                    <strong>{{ session()->get('success') }}</strong>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table id="myTable" class="table table-hover manage-u-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Notification</th>
                                            <th>Type</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($notifications as $key => $notification)
                                            @php
                                                $hasLink = isset($notification->data['link']);
                                                $link = $hasLink ? $notification->data['link'] : '#';
                                            @endphp
                                            <tr class="{{ $notification->read_at ? '' : 'bg-light' }}">
                                                <td>{{ $key + 1 }}</td>
                                                <td style="max-width: 300px; min-width: 250px;">
                                                    <div class="notification-message"
                                                        style="
                                                            white-space: normal;
                                                            word-wrap: break-word;
                                                            overflow: hidden;
                                                            text-overflow: ellipsis;
                                                            display: -webkit-box;
                                                            -webkit-line-clamp: 3;
                                                            -webkit-box-orient: vertical;
                                                            line-height: 1.4;">
                                                        {{ $notification->data['message'] ?? 'No message' }}
                                                        @if (isset($notification->data['employee_name']))
                                                            <small class="text-muted d-block mt-1">
                                                                <i class="fa fa-user"></i>
                                                                {{ $notification->data['employee_name'] }}
                                                            </small>
                                                        @endif
                                                    </div>

                                                    <!-- Full message toggle (hidden by default) -->
                                                    <div class="full-message collapse" id="message-{{ $notification->id }}">
                                                        {{ $notification->data['message'] }}
                                                    </div>

                                                    <!-- Show more/less toggle -->
                                                    @if (strlen($notification->data['message'] ?? '') > 70)
                                                        <a href="#" class="text-primary toggle-message"
                                                            data-toggle="collapse"
                                                            data-target="#message-{{ $notification->id }}"
                                                            style="font-size: 12px;">
                                                            <span class="show-more">Show more <i
                                                                    class="fa fa-chevron-down"></i></span>
                                                            <span class="show-less" style="display: none">Show less <i
                                                                    class="fa fa-chevron-up"></i></span>
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $type = class_basename($notification->type);
                                                        $badgeColor =
                                                            [
                                                                'LeaveApplicationSubmitted' => 'info',
                                                            ][$type] ?? 'primary';
                                                    @endphp
                                                    <span class="badge badge-{{ $badgeColor }}">
                                                        {{ $type }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $notification->created_at->format('M d, Y h:i A') }}
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </small>
                                                </td>
                                                <td>
                                                    @if ($notification->read_at)
                                                        <span class="badge badge-success">Read</span>
                                                    @else
                                                        <span class="badge badge-warning">Unread</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (!$notification->read_at)
                                                        <a href="{{ $hasLink ? route('ess.notifications.markRead', ['id' => $notification->id, 'redirect' => $notification->data['link']]) : route('ess.notifications.markRead', $notification->id) }}"
                                                            class="btn btn-xs btn-primary" title="Mark as Read">
                                                            <i class="fa fa-check"></i>
                                                        </a>
                                                    @endif

                                                    <a href="{!! route('ess.notifications.delete', $notification->id) !!}"
                                                        class="delete btn btn-danger btn-xs deleteBtn btnColor"
                                                        data-token="{!! csrf_token() !!}"
                                                        data-id="{!! $notification->id !!}"><i class="fa fa-trash-o"
                                                            aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No notifications found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div class="text-center">
                                    {{ $notifications->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('page_scripts')

    <script>
        $(document).ready(function() {
            $('.toggle-message').click(function(e) {
                e.preventDefault();
                $(this).find('.show-more, .show-less').toggle();
            });
        });
    </script>

@endsection
