@extends('admin.master')

@section('title', 'Dashboard')

@section('content')
<style>
    .box {
        position: relative;
        background: #ffffff;
        width: 100%;
    }

    .box-body {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
        border-bottom-right-radius: 3px;
        border-bottom-left-radius: 3px;
        padding: 10px;
    }

    .profile-user-img {
        margin: 0 auto;
        width: 100px;
        padding: 3px;
        border: 3px solid #d2d6de;
    }

    @if(count($attendanceData) >=6) tbody {
        display: block;
        height: 320px;
        overflow: auto;
    }

    thead,
    tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
    }

    thead {
        width: calc(100% - 1em)
    }

    @endif @if(count($leaveApplication) >=1) .leaveApplication {
        overflow-x: hidden;
        height: 210px;
    }

    @endif @if(count($notice) >=1) .noticeBord {
        overflow-x: hidden;
        height: 210px;
    }

    @endif @if(count($warning) >=1) .warning {
        overflow-x: hidden;
        height: 210px;
    }

    @endif
</style>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="#">
                        <i class="fa fa-home"></i> Dashboard
                    </a>
                </li>
            </ol>
        </div>
    </div>
    <!--/.row -->

    <div class="row">
        <!-- panel -->
        @can('employee.show')
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <a href="{{route('employee.index')}}">
                <div class="small-box bg-teal">
                    <div class="inner">
                        <h3 class="text-white">{{$totalEmployee}}</h3>
                        <p>Active Employees</p>
                    </div>
                    <div class="icon" aria-hidden="true">
                        <i class="fa fa-user" aria-hidden="true"></i>
                    </div>
                </div>
            </a>

            <a href="{{route('employee.index')}}" class="small-box-footer">
                view all <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
            </a>
        </div>
        @endcan

        @can('department.index')
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-maroon">
                <a href="{{route('department.index')}}">
                    <div class="inner">
                        <h3 class="text-white">{{$totalDepartment}}</h3>
                        <p>Departments</p>
                    </div>
                    <div class="icon" aria-hidden="true">
                        <i class="fa fa-archive"></i>
                    </div>
                </a>
                <a href="{{route('department.index')}}" class="small-box-footer">
                    View all <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
                </a>
            </div>
            
        </div>
        @endcan

        @can('allLeaveApplications.allLeaveApplications')
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-orange">
                
                    <div class="inner">
                        <h3 class="text-white"> {{$totalAttendance}}</h3>
                        <p>Today's Headcount</p>
                    </div>
                    <div class="icon" aria-hidden="true">
                        <i class="fa fa-address-book"></i>
                    </div>
                    <a href="{{route('dailyAttendance.dailyAttendance')}}" class="small-box-footer">
                    view all <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
                </a>
                
               
            </div>
        </div>
        @endcan

        @can('leave.report.onLeaveToday')
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-purple">
                <a href="{{route('leave.report.onLeaveToday')}}">
                    <div class="inner">
                        <h3 class="text-white">{{ $onLeaveTodayCount }}</h3>
                        <p>On Leave today</p>
                    </div>
                    <div class="icon" aria-hidden="true">
                        <i class="fa fa-anchor"></i>
                    </div>
                </a>
                <a href="{{route('leave.report.onLeaveToday')}}" class="small-box-footer">
                    view all <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
        @endcan
    </div>
    <!--/.row -->

    {{-- Employee Self Service (ESS) Section --}}
    @if (Auth::user()->hasRole('Employee'))
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="fa fa-user-circle fa-fw"></i> Employee Self Service
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                    <div class="row">
                        @can('ess.leave.index')
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 m-b-10">
                            <a style="color:white;" href="{{ route('ess.leave.index') }}" class="btn btn-info btn-block" style="white-space: normal;">
                                <i class="fa fa-calendar"></i> My Leaves
                            </a>
                        </div>
                        @endcan
                        @can('ess.leave.form')
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 m-b-10">
                            <a style="color:white" href="{{ route('ess.leave.form') }}" class="btn btn-info btn-block" >
                                <i class="fa fa-calendar-plus-o"></i> Apply Leave
                            </a>
                        </div>
                        @endcan
                        @can('ess.payroll.index')
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 m-b-10">
                            <a style="color:white" href="{{ route('ess.payroll.index') }}" class="btn btn-success btn-block" style="white-space: normal;">
                                <i class="fa fa-money"></i> My Payroll
                            </a>
                        </div>
                        @endcan
                        @can('ess.loans.index')
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 m-b-10">
                            <a style="color:white" href="{{ route('ess.loans.index') }}" class="btn btn-success btn-block" style="white-space: normal;">
                                <i class="fa fa-university"></i> My Loans
                            </a>
                        </div>
                        @endcan
                        @can('ess.approval.index')
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 m-b-10">
                            <a style="color:white" href="{{ route('ess.approval.index') }}" class="btn btn-warning btn-block" style="white-space: normal;">
                                <i class="fa fa-check-square-o"></i> My Approvals
                            </a>
                        </div>
                        @endcan
                        @can('ess.diciplinary.index')
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 m-b-10">
                            <a style="color:white" href="{{ route('ess.diciplinary.index') }}" class="btn btn-danger btn-block" style="white-space: normal;">
                                <i class="fa fa-gavel"></i> Disciplinary
                            </a>
                        </div>
                        @endcan
                        @can('ess.shifts.index')
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 m-b-10">
                            <a style="color:white" href="{{ route('ess.shifts.index') }}" class="btn btn-primary btn-block" style="white-space: normal;">
                                <i class="fa fa-clock-o"></i> My Shifts
                            </a>
                        </div>
                        @endcan
                        @can('ess.trainings.index')
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 m-b-10">
                            <a style="color:white" href="{{ route('ess.trainings.index') }}" class="btn btn-primary btn-block" style="white-space: normal;">
                                <i class="fa fa-graduation-cap"></i> Trainings
                            </a>
                        </div>
                        @endcan
                        @can('ess.awards.index')
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 m-b-10">
                            <a style="color:white" href="{{ route('ess.awards.index') }}" class="btn btn-success btn-block" style="white-space: normal;">
                                <i class="fa fa-trophy"></i> My Awards
                            </a>
                        </div>
                        @endcan
                        @can('ess.notices.index')
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 m-b-10">
                            <a style="color:white" href="{{ route('ess.notices.index') }}" class="btn btn-info btn-block" style="white-space: normal;">
                                <i class="fa fa-bullhorn"></i> Notices
                            </a>
                        </div>
                        @endcan
                        @can('ess.documents.index')
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 m-b-10">
                            <a style="color:blue" href="{{ route('ess.documents.index') }}" class="btn btn-default btn-block" style="white-space: normal;">
                                <i class="fa fa-file-text"></i> Documents
                            </a>
                        </div>
                        @endcan
                        @can('ess.feedback.index')
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 m-b-10">
                            <a style="color:white" href="{{ route('ess.feedback.index') }}" class="btn btn-info btn-block" style="white-space: normal;">
                                <i class="fa fa-comments"></i> Feedback
                            </a>
                        </div>
                        @endcan
                        @can('ess.recruitment.job.posts')
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 m-b-10">
                            <a style="color:white" href="{{ route('ess.recruitment.job.posts') }}" class="btn btn-success btn-block" style="white-space: normal;">
                                <i class="fa fa-briefcase"></i> Jobs
                            </a>
                        </div>
                        @endcan
                        @can('ess.subordinates.index')
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 m-b-10">
                            <a style="color:white" href="{{ route('ess.subordinates.index') }}" class="btn btn-primary btn-block" style="white-space: normal;">
                                <i class="fa fa-sitemap"></i> My Team
                            </a>
                        </div>
                        @endcan
                        @can('ess.performance.myAppraisals')
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 m-b-10">
                            <a style="color:white" href="{{ route('ess.performance.myAppraisals') }}" class="btn btn-warning btn-block" style="white-space: normal;">
                                <i class="fa fa-star"></i> Performance
                            </a>
                        </div>
                        @endcan
                        @can('ess.pip.myPlans')
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 m-b-10">
                            <a style="color:white" href="{{ route('ess.pip.myPlans') }}" class="btn btn-danger btn-block" style="white-space: normal;">
                                <i class="fa fa-tasks"></i> My PIP
                            </a>
                        </div>
                        @endcan
                        
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        @if(Auth::user()->hasRole('Employee') && $ip_attendance_status == 1 && employeeInfo())
        <!-- employee attendance check-in/check-out for Employee role -->
        @php
        $logged_user = employeeInfo();
        @endphp
        <div class="col-md-6">
            <div class="white-box">
                <h3 style="color: #0a0c0d" class="box-title">
                    Hey {!! $employeeInfo->first_name !!} please Check in/out your attendance
                </h3>
                <hr>
                <div class="noticeBord">
                    @if(session()->has('success'))
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success')
                            }}</strong>
                    </div>
                    @endif
                    @if(session()->has('error'))
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <strong>{{ session()->get('error') }}</strong>
                    </div>
                    @endif
                    <form action="{{ route('ess.attendance.create') }}" method="POST">
                        {{ csrf_field() }}

                        <input type="hidden" name="employee_id" value="{{ $logged_user->employee_id }}">

                        <input type="hidden" name="ip_check_status" value="{{ $ip_check_status }}">
                        <input type="hidden" name="national_id" value="{{ $logged_user->national_id }}">
                        <input type="hidden" name="department_id" value="{{ $logged_user->department_id }}">
                        @if($count_user_login_present > 0)
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
                <!--/.noticeBord -->

            </div>
            <!--/.white-box -->
        </div>
        <!--/.col -->
        @endif

        @if($employeeInfo)
        <div class="col-md-6">
            <div class="panel">
                <div class="p-30">
                    <div class="row">
                        @if($employeeInfo->photo !='')
                        <div class="col-xs-4 col-sm-4">
                            <img src="{!! asset('uploads/employeePhoto/'.$employeeInfo->photo) !!}" alt="varun"
                                class="img-circle img-responsive">
                        </div>
                        @else
                        <div class="col-xs-4 col-sm-4">
                            <img src="{!! asset('admin_assets/img/profilePic.png') !!}" alt="varun"
                                class="img-circle img-responsive">
                        </div>
                        @endif
                        <div class="col-xs-12 col-sm-8">
                            <h2 class="m-b-0">
                                {{$employeeInfo->first_name}} {{$employeeInfo->last_name}}
                            </h2>
                            <h4>
                                {{$employeeInfo->designation->designation_name}}
                            </h4>
                            <a href="{{url('profile')}}" class="btn btn-rounded btn-success">
                                <i class="ti-user m-r-5"></i> PROFILE
                            </a>
                        </div>
                    </div>
                    <!--/.row -->

                    <div class="row text-center m-t-30">
                        <div class="col-xs-6 b-r">
                            <h2 style="color: black">
                                {{$employeeTotalLeave->totalNumberOfDays}}
                            </h2>
                            <h4 style="color: black">
                                LEAVES USED
                            </h4>
                        </div>
                        <div class="col-xs-6">
                            <h2 style="color: black">
                                {{$employeeTotalAward->totalAward}}
                            </h2>
                            <h4 style="color: black">
                                AWARD
                            </h4>
                        </div>

                    </div>
                    <!--/.row -->

                </div>
                <!--/.p-30 -->
                <hr class="m-t-10" />
            </div>
            <!--/.panel -->
        </div>
        <!--/.col -->
        @endif

        <div class="col-md-12 col-sm-12 col-lg-12">
            <div class="panel">
                <div class="panel-heading" style="text-transform: uppercase">
                    {{date('F Y')}}, Attendance
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
    </div>
    <!--/.row -->

   

   

    <!-- up comming birthday  -->
    @if (Auth::user()->hasRole('HR Administrator'))
    <div class="row">
        @if(count($upcoming_birtday) > 0)
        <div class="col-md-6 col-lg-6 col-sm-12">
            <div class="white-box">
                <h3 class="box-title text-dark">@lang('dashboard.upcoming_birthday')</h3>
                <hr>
                <div class="leaveApplication">
                    @foreach($upcoming_birtday as $employee_birthdate)
                    <div class="comment-center p-t-10">
                        <div class="comment-body">
                            @if($employee_birthdate->photo !='')
                            <div class="user-img"> <img
                                    src="{!! asset('uploads/employeePhoto/'.$employee_birthdate->photo) !!}" alt="user"
                                    class="img-circle"></div>
                            @else
                            <div class="user-img"> <img src="{!! asset('admin_assets/img/default.png') !!}" alt="user"
                                    class="img-circle"></div>
                            @endif
                            <div class="mail-contnet">

                                @php
                                $date_of_birth = $employee_birthdate->date_of_birth;
                                $separate_date = explode('-',$date_of_birth);

                                $date_current_year = date('Y').'-'.$separate_date[1].'-'.$separate_date[2];

                                $create_date = date_create($date_current_year);
                                @endphp

                                <h5>{{ $employee_birthdate->first_name }} {{$employee_birthdate->last_name}}</h5><span
                                    class="time">{{ date_format(date_create($employee_birthdate->date_of_birth),"D dS F
                                    Y") }}</span>
                                <br />

                                <span class="mail-desc">
                                    @if($date_current_year == date('Y-m-d'))
                                    <b>Today is
                                        @if($employee_birthdate->gender == 'Male')
                                        His @else
                                        Her
                                        @endif
                                        Birtday Wish
                                        @if($employee_birthdate->gender == 'Male')
                                        Him
                                        @else Her
                                        @endif</b>

                                    @else

                                    Wish
                                    @if($employee_birthdate->gender == 'Male')
                                    Him @else
                                    Her
                                    @endif
                                    on {{ date_format($create_date,"D dS F Y") }}



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
    </div>
    @endif

</div>
<!--/.container-fluid -->
@endsection

@section('page_scripts')
<link href="{!! asset('admin_assets/plugins/bower_components/news-Ticker-Plugin/css/site.css') !!}" rel="stylesheet"
    type="text/css" />
<script
    src="{!! asset('admin_assets/plugins/bower_components/news-Ticker-Plugin/scripts/jquery.bootstrap.newsbox.min.js') !!}">
</script>

<script type="text/javascript">
    (function() {

   $(".demo1").bootstrapNews({
       newsPerPage: 2,
       autoplay: true,
       pauseOnHover:true,
       direction: 'up',
       newsTickerInterval: 4000,
       onToDo: function () {
           //console.log(this);
       }
   });

})();


$(document).on('click', '.remarksForLeave', function () {

   var actionTo = "{{ route('leave.manage.approve_reject') }}";
   var leave_application_id = $(this).attr('data-leave_application_id');
   var status = $(this).attr('data-status');

   if(status == 2){
       var statusText = "Are you want to approve leave application?";
       var btnColor = "#2cabe3";
   }else{
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
       function (isConfirm) {
           var token = '{{ csrf_token() }}';
           if (isConfirm) {
               $.ajax({
                   type: 'POST',
                   url:actionTo,
                   data: {leave_application_id:leave_application_id,status:status,_token:token},
                   success: function (data) {
                       if (data == 'approve') {
                           swal({
                                   title: "Approved!",
                                   text: "Leave application approved.",
                                   type: "success"
                               },
                               function (isConfirm) {
                                   if (isConfirm) {
                                       $('.' + leave_application_id).fadeOut();
                                   }
                               });

                       }else{
                           swal({
                                   title: "Rejected!",
                                   text: "Leave application rejected.",
                                   type: "success"
                               },
                               function (isConfirm) {
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