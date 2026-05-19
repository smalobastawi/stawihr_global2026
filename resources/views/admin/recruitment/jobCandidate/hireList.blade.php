@extends('admin.master')

@section('title', 'Job Hires List')

@section('content')

    <style>
        .downloadResume {
            font-size: 15px;
            color: #777;
            font-weight: 500;
        }

        .post {
            font-weight: 500;
            font-size: 16px;
        }

        .applicationDate {
            font-size: 13px;
            color: #98a6ad;
        }

        .coverLater {
            margin-top: 5px;
        }

        .panel .panel-heading {
            border-radius: 0;
            font-weight: 500;
            font-size: 14px;
            padding: 10px 25px;
        }
    </style>
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                            Dashboard</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
            <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
                <a href="{{ route('jobCandidate.index') }}"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                    <i class="fa fa-list-ul" aria-hidden="true"></i> Job Candidates List
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-offset-2 col-md-7">
                <p class="box-title post">Job Name : {{ $job->job_title }}</p>
                <br>
                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                    </div>
                @endif
            </div>
            @if (count($results) > 0)
                @foreach ($results as $value)
                    <div class="col-md-offset-2 col-md-7 ">
                        <div class="panel panel-default">
                            <div class="panel-heading">{{ $value->applicant_name }} <span class="applicationDate">&nbsp;
                                    Hired for </span>{{ $job->post }} <span class="applicationDate">&nbsp;Position
                                </span></div>
                            <div class="panel-wrapper collapse in">
                                <div class="panel-body">

                                    <p class="coverLater">
                                        @if (isset($value->interviewInfo->comment))
                                            {!! $value->interviewInfo->comment !!}
                                        @endif
                                    </p>
                                    <a class="downloadResume" href="{{ route('view.CV', $value->job_applicant_id ) }}" download="" target="_blank">
                                        <i class="fa fa-download"></i> Download Resume
                                    </a>

                                    @if ($value->employee)
                                        <p>An account for the employee has already been created</p>
                                        <p>Employee Details include:</p>

                                        <p><b>Full Names: </b> {{ $value->employee->first_name }} {{ $value->employee->middle_name }}
                                            {{ $value->employee->last_name }}</p>
                                        <p><b>Email Address: </b> {{ $value->employee->email }}</p>
                                        <p><b>Phone Number: </b> {{ $value->employee->phone }}</p>
                                        <p><b>Hired Date: </b> {{ $value->employee->date_of_joining }}</p>

                                        <p>Click here to view profile</p>

                                        <p>
                                            <a href="{{ route('employee.show', $value->employee->employee_id) }}"
                                                class="btn btn-primary">
                                                View Profile
                                            </a>
                                        </p>
                                    @endif


                                </div>
                                <div class="panel-footer">
                                    <p>
                                        <b>Hire Date :</b>
                                        @if (isset($value->hire_date))
                                            {{ date(' d M Y ', strtotime($value->hire_date)) }}
                                        @endif&nbsp;
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="col-md-offset-2 col-md-7 text-center">
                    {{ $results->links() }}
                </div>
            @else
                <div class="col-md-offset-2 col-md-7 ">
                    <div style="background: #fff;padding: 2px 11px;">
                        <h4>You have no job interview candidate....</h4>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
