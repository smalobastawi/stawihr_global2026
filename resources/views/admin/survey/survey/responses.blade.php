@extends('admin.master')

@section('title', $data->title)

@section('content')
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
                <a href="{{ route('survey.show', $data->id) }}"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                    <i class="fa fa-list-ul" aria-hidden="true"></i> View Survey
                </a>
            </div>
        </div><!--/.row -->

        <div class="row">
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-teal">
                    <a href="#">
                        <div class="inner">
                            <h3 class="text-white">
                                {{ $data->surveyQuestion->count() }}
                            </h3>
                            <p>Survey Questions</p>
                        </div>
                        <div class="icon" aria-hidden="true">
                            <i class="fa fa-question-circle" aria-hidden="true"></i>
                        </div>
                    </a>
                    <a href="javascript:void(0)" class="small-box-footer addSurveyQuestionBtn">
                        Add Survey Question <i class="fa fa-plus-circle" aria-hidden="true"></i>
                    </a>
                </div>
            </div><!-- ./col -->

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-info">
                    <a href="{{ route('survey.responses', $data->id) }}">
                        <div class="inner">
                            <h3 class="text-white">
                                {{ $data->employeeSurveyResponse->count() }}
                            </h3>
                            <p>Survey Responses</p>
                        </div>
                        <div class="icon" aria-hidden="true">
                            <i class="fa fa-comments" aria-hidden="true"></i>
                        </div>
                    </a>
                    <a href="{{ route('survey.responses', $data->id) }}" class="small-box-footer">
                        View Responses <i class="fa fa-arrow-circle-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div><!-- ./col -->
        </div>

        @if (count($responses) > 0)
            <div class="row">
                <div class="col-12  ">
                    <div class="panel panel-info">
                        <div class="panel-wrapper collapse in" aria-expanded="true">
                            <div class="panel-body">
                                <canvas id="chartContainer"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif


        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-table fa-fw"></i> {{ $data->title }} survey questions responses
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table id="myTable" class="table table-bordered">
                                    <thead class="tr_header">
                                        <tr>
                                            <th>S/L</th>
                                            <th>Question</th>
                                            <th>Answer Type</th>
                                            <th>Employee</th>
                                            <th>Response</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {!! $sl = null !!}
                                        @foreach ($responses as $value)
                                            <tr class="{!! $value->id !!}">
                                                <td style="width: 100px;">
                                                    {!! ++$sl !!}
                                                </td>
                                                <td>
                                                    {{ $value->surveyQuestion->question_text }}
                                                </td>
                                                <td>
                                                    @if ($value->surveyQuestion->answer_type)
                                                        {{ \AnswerTypes::getName($value->surveyQuestion->answer_type) }}
                                                    @endif
                                                </td>

                                                <td>
                                                    @if ($value->employee)
                                                        {{ $value->employee->first_name }}
                                                        {{ $value->employee->last_name }}
                                                    @endif
                                                </td>

                                                <td>
                                                    {{ $value->response }}
                                                </td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div><!--/.panel-body -->
                    </div><!--/.panel-wrapper -->
                </div><!--/.panel panel-info -->

            </div>
        </div>
    </div><!--/.container-fluid -->
@endsection


@section('page_scripts')

    <script>
        $(document).ready(function() {
            //-------------
            //- DONUT CH    ART -
            //-------------
            // Get context with jQuery - using jQuery's .get() method.
            var surveyId = '{{ $data->id }}';
            let path = '{{ route('survey.chart.responses', ':id') }}';
            path = path.replace(':id', surveyId);

            $.ajax({
                url: path,
                method: 'GET',
                success: function(response) {
                    if ($.isEmptyObject(response)) {
                        console.log("No responses available.");
                        return;
                    }

                    // Get chart canvas context
                    let ctx = document.getElementById('chartContainer').getContext('2d');

                    // Create Chart.js Donut Chart
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: response.labels, // Labels now include Question + Response
                            datasets: [{
                                data: response.data,
                                backgroundColor: response.backgroundColor
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: true,
                                    text: 'Survey Questions & Responses'
                                }
                            }
                        }
                    });
                }
            });

        });
    </script>

@endsection
