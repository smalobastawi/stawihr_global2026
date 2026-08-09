@extends('admin.master')

@section('title', 'Candidate List')

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
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <br>
                        <span class="h4 font-weight-bold text-danger bg-white p-3 rounded text-uppercase">
                            Job Position: {{ $job->job_title }}
                        </span>


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
                            {{-- filter form --}}
                            <div class="row">
                                <form action="{{ route('applicants.search', ['job_id' => $job_id]) }}" method="get">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="min_experience">Min experience</label>
                                                <select name="min_experience" class="form-control select2" id="min_experience">
                                                    <option value="">Any</option>
                                                    @for ($i = 0; $i <= 20; $i++)
                                                        <option value="{{ $i }}"
                                                            {{ ($filters['min_experience'] ?? '') !== '' && (int) ($filters['min_experience'] ?? -1) === $i ? 'selected' : '' }}>
                                                            ≥ {{ $i }} year(s)
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="qualification">Min qualification</label>
                                                <select name="qualification" class="form-control select2" id="qualification">
                                                    <option value="">Any</option>
                                                    @foreach (\App\Lib\Enumerations\QualificationLevel::options() as $level)
                                                        <option value="{{ $level }}"
                                                            {{ ($filters['qualification'] ?? '') == $level ? 'selected' : '' }}>
                                                            {{ $level }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="skill">Skill contains</label>
                                                <input type="text" name="skill" id="skill" class="form-control"
                                                    value="{{ $filters['skill'] ?? '' }}" placeholder="e.g. Laravel">
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <div>
                                                    <label class="checkbox-inline" style="margin-top: 7px;">
                                                        <input type="checkbox" name="meets_criteria" value="1"
                                                            {{ !empty($filters['meets_criteria']) ? 'checked' : '' }}>
                                                        Meets job criteria
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label for="">&nbsp;</label>
                                                <button type="submit" class="btn btn-success form-control"
                                                    style="color: white;">Search</button>
                                            </div>
                                        </div>

                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label for="">&nbsp;</label>
                                                <a href="{{ route('jobCandidate.applyCandidateList', $job->job_id) }}"
                                                    class="btn btn-danger form-control" style="color: white;">Reset</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            {{-- end filter form --}}
                            <hr>
                            <div class="table-responsive">
                                <table id="myTable23" class="table table-hover manage-u-table">
                                    <thead>
                                        <tr>
                                            <th>@lang('common.serial')</th>
                                            <th>Names</th>
                                            <th>Email</th>
                                            <th>Phone No</th>
                                            <th>Highest Qualification</th>
                                            <th>Yrs. Of Experience</th>
                                            <th>Skills</th>
                                            <th>Match</th>
                                            <th>Application Date</th>
                                            <th>Resume</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {!! $sl = null !!}
                                        @if (count($results) > 0)
                                            @foreach ($results as $value)
                                                @php
                                                    $score = (int) ($value->match_score ?? 0);
                                                    $scoreClass = $score >= 80 ? 'success' : ($score >= 50 ? 'warning' : 'danger');
                                                @endphp
                                                <tr class="{!! $value->job_id !!}">
                                                    <td style="width: 70px;">{!! ++$sl !!}</td>
                                                    <td>
                                                        <a href="#">{!! $value->applicant_name !!}</a>
                                                        @if(!empty($value->meets_criteria))
                                                            <br><span class="label label-success">Qualified</span>
                                                        @endif
                                                    </td>
                                                    <td>{!! $value->applicant_email !!}</td>
                                                    <td>{!! $value->phone !!}</td>
                                                    <td>{!! $value->highest_qualification !!}</td>
                                                    <td>{!! $value->years_of_experience !!}</td>
                                                    <td><small>{{ \Illuminate\Support\Str::limit($value->skills ?? '-', 40) }}</small></td>
                                                    <td><span class="label label-{{ $scoreClass }}">{{ $score }}%</span></td>
                                                    <td>{{ date('d M Y', strtotime($value->application_date)) }}</td>
                                                    <td>
                                                        {{-- {{ $value->cover_letter }} --}}
                                                        <a class="downloadResume" href="{{ route('view.CV', $value->job_applicant_id) }}" target="_blank">
                                                            <i class="fa fa-download"></i>Resume
                                                        </a>
                                                    </td>
                                                    <td>

                                                        @if ($value->status == \App\Lib\Enumerations\JobStatus::$SHORTLIST)
                                                            <span class="label label-info"> <strong>Short
                                                                    Listed</strong></span>
                                                        @elseif($value->status == \App\Lib\Enumerations\JobStatus::$REJECT)
                                                            <span class="label label-danger">
                                                                <strong>Rejected</strong></span>
                                                        @elseif($value->status == \App\Lib\Enumerations\JobStatus::$CALL_FOR_INTERVIEW)
                                                            <span class="label label-success"> <b>Called For
                                                                    Interview</b></span>
                                                        @elseif($value->status == \App\Lib\Enumerations\JobStatus::$HIRE)
                                                            <span class="label label-info"> <b>Hired</b></span>
                                                        @elseif($value->status == 0)
                                                            <span class="label label-primary"><b>Under Review</b></span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($value->status == \App\Lib\Enumerations\JobStatus::$SHORTLIST)
                                                            <p class="text-info"> <strong>Short Listed</strong></p>
                                                        @elseif($value->status == \App\Lib\Enumerations\JobStatus::$REJECT)
                                                            <p class="text-danger"> <strong>Rejected</strong></p>
                                                        @elseif($value->status == \App\Lib\Enumerations\JobStatus::$CALL_FOR_INTERVIEW)
                                                            <p class="text-info"> <b>Called For Interview</b></p>
                                                        @elseif($value->status == \App\Lib\Enumerations\JobStatus::$HIRE)
                                                            <p class="text-info"> <b>Hired</b></p>
                                                        @elseif($value->status == 0)
                                                            <a href="{{ route('applicant.shortlist', $value->job_applicant_id) }}"
                                                                onclick="return confirm('Are you sure you want to SHORTLIST this applicant?')">
                                                                <button type="submit" class="btn btn-success"><i
                                                                        class="fa fa-check"></i> Short List
                                                                </button></a>
                                                            <a href="{{ route('applicant.reject', $value->job_applicant_id) }}"
                                                                onclick="return confirm('Are you sure you want to REJECT this applicant?')">
                                                                <button type="submit" class="btn btn-danger"><i
                                                                        class="fa fa-eraser"></i> Reject
                                                                </button></a>
                                                        @endif
                                                    </td>

                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6">@lang('common.no_data_available')</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                <div class="text-center">
                                    {{ $results->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection
