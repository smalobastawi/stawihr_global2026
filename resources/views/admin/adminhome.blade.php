@extends('admin.master')

@section('title', getPageTitle() . ' | ' . config('app.name'))

<style>
    .dash_image {

        width: 60px;
    }

    @if (count($attendanceData) > 3)
        tbody {
            /*display: block;*/
            /*!*height: 300px;*!*/
            /*overflow: auto;*/
        }

        tr,
        td {
            word-wrap: break-word;
            width: 40px;
            border: 1px solid black;
        }

    @endif
    @if (count($leaveApplication) >= 1)
        .leaveApplication {
            overflow-x: hidden;
            height: 210px;
        }

    @endif
    @if (count($notice) >= 1)
        .noticeBord {
            overflow-x: hidden;
            height: 210px;
        }

    @endif
</style>
@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="#"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                </ol>
            </div>
        </div>

        <div class="row">
            <!-- panel -->
            <div class="col-lg-3 col-xs-6">
                <a href="{{ route('employee.index') }}">
                    <!-- small box -->
                </a>
                <div class="small-box bg-teal"><a href="{{ route('employee.index') }}">
                        <div class="inner">
                            <h3 class="text-white">{{ $totalEmployee }}</h3>
                            <p>Active Employees</p>
                        </div>
                        <div class="icon" aria-hidden="true">
                            <i class="fa fa-user" aria-hidden="true"></i>
                        </div>
                    </a><a href="{{ route('employee.index') }}" class="small-box-footer">view all <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                </div>

            </div><!-- ./col -->

            <div class="col-lg-3 col-xs-6">
                <a href="{{ route('department.index') }}">
                    <!-- small box -->
                </a>
                <div class="small-box bg-maroon">
                    <a href="{{ route('department.index') }}">
                        <div class="inner">
                            <h3 class="text-white">{{ $totalDepartment }}</h3>
                            <p>Departments</p>
                        </div>
                        <div class="icon" aria-hidden="true">
                            <i class="fa fa-archive"></i>
                        </div>
                    </a><a href="{{ route('department.index') }}" class="small-box-footer">View all <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                </div>

            </div><!-- ./col -->


            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <a href="{{ route('dailyAttendance.dailyAttendance') }}">
                </a>
                <div class="small-box bg-orange"><a href="{{ route('dailyAttendance.dailyAttendance') }}">
                        <div class="inner">
                            <h3 class="text-white"> {{ $totalAttendance }}</h3>
                            <p>Today's Headcount</p>
                        </div>
                        <div class="icon" aria-hidden="true">
                            <i class="fa fa-address-book"></i>
                        </div>
                    </a><a href="{{ route('dailyAttendance.dailyAttendance') }}" class="small-box-footer">view all <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                </div>

            </div><!-- ./col -->

            <div class="col-lg-3 col-xs-6">
                <!-- small box -->

                <a href="{{ route('leave.report.onLeaveToday') }}">
                </a>
                <div class="small-box bg-purple">
                    <a href="{{ route('leave.report.onLeaveToday') }}">
                        <div class="inner">
                            <h3 class="text-white"> {{ $onLeaveTodayCount }}</h3>
                            <p>On Leave today</p>
                        </div>
                        <div class="icon" aria-hidden="true">
                            <i class="fa fa-anchor"></i>
                        </div>
                    </a><a href="{{ route('leave.report.onLeaveToday') }}" class="small-box-footer">view all <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                </div>
            </div><!-- ./col -->


        </div>

        <div class="row">
            <h3 class="col-lg-3 col-sm-6 col-xs-12 text-dark">Shortcuts</h3>
        </div>

        <div class="row">
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="white-box analytics-info">
                    <h3 class="box-title"> <a href="{{ route('employee.index') }}" target="_blank"> Manage Employees<i class="ti-arrow-top-right"></i></a></h3>

                </div>
            </div>
            @can('payrollIndex')
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="white-box analytics-info">
                        <h3 class="box-title"><a href="{{ route('payrollIndex') }}" target="_blank">Payroll<i class="ti-arrow-top-right"></i></a> </h3>

                    </div>
                </div>
            @endcan
            @can('dailyAttendance.dailyAttendance')
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="white-box analytics-info">
                        <h3 class="box-title"> <a href="{{ route('dailyAttendance.dailyAttendance') }}" target="_blank">Attendance<i class="ti-arrow-top-right"></i></a></h3>
                    </div>
                </div>
            @endcan
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="white-box analytics-info">
                    <h3 class="box-title"> <a href="{{ route('allLeaveApplications.allLeaveApplications') }}" target="_blank">Leave Management<i class="ti-arrow-top-right"></i></a></h3>

                </div>
            </div>
        </div>

       

       

        <div class="row">

            @if ($ip_attendance_status == 1 && employeeInfo())
                <!-- employe attendance  -->
                @php
                    $logged_user = employeeInfo();
                @endphp
                <div class="col-md-6">
                    <div class="white-box">
                        <h3 style="color: #0a0c0d" class="box-title">Hey {!! $logged_user->user_name !!} please Check in/out your
                            attendance</h3>
                        <hr>
                        <div class="noticeBord">
                            @if (session()->has('success'))
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                    </button>
                                    <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                </div>
                            @endif
                            @if (session()->has('error'))
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                    </button>
                                    <strong>{{ session()->get('error') }}</strong>
                                </div>
                            @endif
                            <form action="{{ route('ip.attendance') }}" method="POST">
                                {{ csrf_field() }}
                               
                                <input type="hidden" name="employee_id" value="{{ $logged_user->employee_id }}">

                                <input type="hidden" name="ip_check_status" value="{{ $ip_check_status }}">
                                <input type="hidden" name="national_id" value="{{ $logged_user->national_id }}">
                                <input type="hidden" name="department_id" value="{{ $logged_user->department_id }}">
                                @if ($count_user_login_present > 0)
                                    <button class="btn btn-info" type="button">
                                        <i class="fa fa-clock-o"> </i>
                                        Your attendance of today has been updated.
                                    </button>
                                    <input type="hidden" name="attendanceType" value="checkOut">
                                @elseif($count_user_login_today == 1)
                                    <button class="btn btn-danger">
                                        <i class="fa fa-clock-o"> </i>
                                        Check Out
                                    </button>
                                    <input type="hidden" name="attendanceType" value="checkOut">
                                @else
                                    <button class="btn btn-primary">
                                        <i class="fa fa-clock-o"> </i>
                                        Check In
                                    </button>
                                    <input type="hidden" name="attendanceType" value="checkIn">
                                @endif

                            </form>
                        </div>
                    </div>
                </div>
                

                <!-- end attendance  -->
            @endif



            @if (count($notice) > 0)
                <div class="col-md-6">
                    <div class="white-box">
                        <h3 class="box-title" style="color: #0a0c0d">@lang('dashboard.notice_board')</h3>
                        <hr>
                        <div class="noticeBord">
                            @foreach ($notice as $row)
                                @php
                                    $noticeDate = strtotime($row->publish_date);
                                @endphp
                                <div class="comment-center p-t-10">
                                    <div class="comment-body">

                                        <div class="user-img"><i style="font-size: 31px" class="fa fa-flag-checkered text-info"></i></div>


                                        <div class="mail-contnet">
                                            <h5 class="text-danger">{{ substr($row->title, 0, 70) }}..</h5><span class="time">Published Date: {{ date(' d M Y ', $noticeDate) }}</span>
                                            <br /><span class="mail-desc">
                                                @if ($row->createdBy?->first_name)
                                                    {{ $row->createdBy->first_name }}
                                                @endif
                                                @if ($row->createdBy?->last_name)
                                                    {{ $row->createdBy->last_name }}
                                                @endif
                                                @lang('notice.description'): {!! substr($row->description, 0, 80) !!}..
                                            </span>
                                            <a href="{{ url('notice/' . $row->notice_id) }}" class="btn m-r-5 btn-rounded btn-outline btn-info">@lang('common.read_more')</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            <div class="col-md-12 col-sm-12 col-lg-12">
            <div class="panel">
                <div class="panel-heading" style="text-transform: uppercase">
                    My {{date('F Y')}}, Attendance
                </div>
                <div class="table-responsive">
                    <table class="table table-hover manage-u-table">
                        <thead>
                            <tr>
                                <th class="text-center"> # </th>
                                <th> @lang('common.date') </th>
                                <th> @lang('dashboard.in_time') </th>
                                <th> @lang('dashboard.out_time')</th>

                                <th> @lang('common.status') </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($attendanceDataUser) > 0)
                            {{$dailyAttendanceSl =null }}
                            @foreach($attendanceDataUser as $dailyAttendance)
                            <tr>
                                <td class="text-center">{{ ++$dailyAttendanceSl }}</td>


                                <td>{{ date('Y-m-d', strtotime($dailyAttendance['date'])) }}</td>
                                <td>
                                    @if($dailyAttendance['in_time'] !='')
                                    {{ date('Y-m-d h:i a', strtotime($dailyAttendance['in_time']))}}
                                    @else
                                    {{ "--"}}
                                    @endif
                                </td>
                                <td>
                                    @php
                                    if($dailyAttendance['out_time'] !=''){
                                    echo date('Y-m-d h:i a', strtotime($dailyAttendance['out_time']));
                                    }else{
                                    echo "--";
                                    }
                                    @endphp
                                </td>


                                <td>
                                    @php
                                    if($dailyAttendance['action'] =='Absence'){
                                    echo "<span class='label label-danger'>Absence</span>";
                                    }elseif($dailyAttendance['action'] =='Leave'){
                                    echo "<span class='label label-info'>Leave</span></p>";
                                    }else{
                                    echo "<span class='label label-success'>Present</span>";
                                    }
                                    @endphp
                                </td>

                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td colspan="8">@lang('common.no_data_available')</td>
                            </tr>
                            @endif

                        </tbody>
                    </table>
                </div>
                <!--/.table-responsive -->
            </div>
        </div>

            @if (Auth::user()->hasRole('HR Administrator'))
            @if (count($upcoming_birtday) > 0)
                <div class="col-md-6">
                    <div class="white-box" style="color: #0a0c0d">
                        <span class="box-title">@lang('dashboard.upcoming_birthday')</span>
                        <hr>
                        <div class="leaveApplication">
                            @foreach ($upcoming_birtday as $employee_birthdate)
                                <div class="comment-center p-t-10">
                                    <div class="comment-body">
                                        @if (isset($employee_birthdate->photo))
                                            )
                                            <div class="user-img"><img src="{!! asset('uploads/employeePhoto/' . $employee_birthdate->photo) !!}" alt="user" class="img-circle"></div>
                                        @else
                                            <div class="user-img"><img src="{!! asset('admin_assets/img/default.png') !!}" alt="user" class="img-circle"></div>
                                        @endif
                                        <div class="mail-contnet">

                                            @php
                                                $date_of_birth = $employee_birthdate->date_of_birth;
                                                $separate_date = explode('-', $date_of_birth);

                                                $date_current_year = date('Y') . '-' . $separate_date[1] . '-' . $separate_date[2];

                                                $create_date = date_create($date_current_year);
                                            @endphp

                                            <p>{{ $employee_birthdate->first_name }} {{ $employee_birthdate->last_name }}
                                            </p>
                                            <span class="time">{{ date_format(date_create($employee_birthdate->date_of_birth), 'D dS F Y') }}</span>
                                            <br />

                                            <span class="mail-desc">
                                                @if ($date_current_year == date('Y-m-d'))
                                                    <b>Today is
                                                        @if ($employee_birthdate->gender == 'Male')
                                                            His
                                                        @else
                                                            Her
                                                        @endif
                                                        Birtday Wish
                                                        @if ($employee_birthdate->gender == 'Male')
                                                            Him
                                                        @else
                                                            Her
                                                        @endif
                                                    </b>
                                                @else
                                                    Wish
                                                    @if ($employee_birthdate->gender == 'Male')
                                                        Him
                                                    @else
                                                        Her
                                                    @endif
                                                    on {{ date_format($create_date, 'D dS F Y') }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            @endif

          
        </div>
    </div>

@endsection


@section('page_scripts')
    <link href="{!! asset('admin_assets/plugins/bower_components/news-Ticker-Plugin/css/site.css') !!}" rel="stylesheet" type="text/css" />
    <script src="{!! asset('admin_assets/plugins/bower_components/news-Ticker-Plugin/scripts/jquery.bootstrap.newsbox.min.js') !!}"></script>

    <script type="text/javascript">
        (function() {

            $(".demo1").bootstrapNews({
                newsPerPage: 2,
                autoplay: true,
                pauseOnHover: true,
                direction: 'up',
                newsTickerInterval: 4000,
                onToDo: function() {
                    //console.log(this);
                }
            });

        })();

        $(document).on('click', '.remarksForLeave', function() {

            var actionTo = "{{ route('leave.manage.approve_reject') }}";
            var leave_application_id = $(this).attr('data-leave_application_id');
            var status = $(this).attr('data-status');

            if (status == 2) {
                var statusText = "Are you want to approve leave application?";
                var btnColor = "#2cabe3";
            } else {
                var statusText = "Are you want to reject leave application?";
                var btnColor = "red";
            }

            swal({
                    title: "",
                    text: statusText,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: btnColor,
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                },
                function(isConfirm) {
                    var token = '{{ csrf_token() }}';
                    if (isConfirm) {
                        $.ajax({
                            type: 'POST',
                            url: actionTo,
                            data: {
                                leave_application_id: leave_application_id,
                                status: status,
                                _token: token
                            },
                            success: function(data) {
                                if (data == 'approve') {
                                    swal({
                                            title: "Approved!",
                                            text: "Leave application approved.",
                                            type: "success"
                                        },
                                        function(isConfirm) {
                                            if (isConfirm) {
                                                $('.' + leave_application_id).fadeOut();
                                            }
                                        });

                                } else {
                                    swal({
                                            title: "Rejected!",
                                            text: "Leave application rejected.",
                                            type: "success"
                                        },
                                        function(isConfirm) {
                                            if (isConfirm) {
                                                $('.' + leave_application_id).fadeOut();
                                            }
                                        });
                                }
                            }

                        });
                    } else {
                        swal("Cancelled", "Your data is safe .", "error");
                    }
                });
            return false;

        });
    </script>
@endsection
