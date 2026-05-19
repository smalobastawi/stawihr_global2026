@extends('admin.master')

@section('title', $data->title)

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor">
                        <a href="{{ url('dashboard') }}">
                            <i class="fa fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li>@yield('title')</li>

                </ol>
            </div>
            <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
                @if (
            $data->answer_type == AnswerTypes::SINGLE_CHOICE ||
                $data->answer_type == AnswerTypes::MULTIPLE_CHOICE ||
                $data->answer_type == AnswerTypes::DROPDOWN)
                <a href="javascript:void(0)"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light addAnswerOptionBtn">
                    <i class="fa fa-plus" aria-hidden="true"></i> Add Answer Option
                </a>
                @endif
                <a href="{{ route('survey.questions.index') }}"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                    <i class="fa fa-list-ul" aria-hidden="true"></i> Survey Questions List
                </a>

                <a href="{{ route('survey.show', $data->survey->id) }}"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                    <i class="fa fa-eye" aria-hidden="true"></i> View Survey
                </a>
            </div>
        </div><!--/.row -->

        <div class="row">
            <div class="col-lg-12">
                <div class="white-box">
                    <h1 style="color: #0a0c0d; font-size:26px;" class="box-title">
                        {{ $data->question_text }}
                    </h1>
                </div><!--/.white-box -->
            </div><!--/.col -->
        </div><!--/.row -->

        @if (count($responses) > 0)
            <div class="row">
                <div class="col-12 col-md-12">

                    <div class="panel panel-info">
                        <div class="panel-wrapper collapse in" aria-expanded="true">
                            <div class="panel-body">
                                <canvas id="chartContainer"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>

                </div><!--/.col -->
            </div><!--/.row -->
        @endif



        @if (
            $data->answer_type == AnswerTypes::SINGLE_CHOICE ||
                $data->answer_type == AnswerTypes::MULTIPLE_CHOICE ||
                $data->answer_type == AnswerTypes::DROPDOWN)
            <div class="row">
                <div class="col-12 col-lg-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <i class="mdi mdi-table fa-fw"></i> {{ $data->question_text }} survey answers
                        </div>
                        <div class="panel-wrapper collapse in" aria-expanded="true">
                            <div class="panel-body">
                                @if (session()->has('success'))
                                    <div class="alert alert-success alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                        </button>
                                        <i
                                            class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                    </div>
                                @endif
                                @if (session()->has('error'))
                                    <div class="alert alert-danger alert-dismissable">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                        </button>
                                        <i
                                            class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                    </div>
                                @endif
                                <div class="table-responsive">
                                    <table id="myTable" class="table table-bordered">
                                        <thead class="tr_header">
                                            <tr>
                                                <th>S/L</th>
                                                <th>Answer</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {!! $sl = null !!}
                                            @foreach ($data->surveyAnswer as $value)
                                                <tr class="{!! $value->id !!}">
                                                    <td style="width: 100px;">{!! ++$sl !!}</td>
                                                    <td>{{ $value->answer_text }}</td>
                                                    <td style="width: 100px;">
                                                        <a href="{!! route('survey.answers.edit', $value->id) !!}"
                                                            class="btn btn-success btn-xs btnColor">
                                                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                        </a>
                                                        <a href="{!! route('survey.answers.delete', $value->id) !!}"
                                                            class="delete btn btn-danger btn-xs deleteBtn btnColor"
                                                            data-token="{!! csrf_token() !!}"
                                                            data-id="{!! $value->id !!}"><i class="fa fa-trash-o"
                                                                aria-hidden="true"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div><!--/.panel-wrapper -->

                    </div><!--/.panel -->
                </div>
            </div><!--/.row -->
        @endif

        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-table fa-fw"></i> {{ $data->question_text }} survey answers
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if (session()->has('success'))
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                    </button>
                                    <i
                                        class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                </div>
                            @endif
                            @if (session()->has('error'))
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                    </button>
                                    <i
                                        class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table id="myTable" class="table table-bordered">
                                    <thead class="tr_header">
                                        <tr>
                                            <th>S/L</th>
                                            <th>Response</th>
                                            <th>Employee</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {!! $sl = null !!}
                                        @foreach ($responses as $responseValue)
                                            <tr class="{!! $responseValue->id !!}">
                                                <td style="width: 100px;">{!! ++$sl !!}</td>
                                                <td>{{ $responseValue->response }}</td>
                                                <td>
                                                    @if ($responseValue->employee)
                                                        {{ $responseValue->employee->first_name }}
                                                        {{ $responseValue->employee->last_name }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div><!--/.panel-wrapper -->

                </div><!--/.panel -->
            </div>
        </div><!--/.row -->


    </div><!--/.container-fluid -->

    <div class="modal fade" id="addAnswerOptionModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="addAnswerOptionForm" method="POST">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" value="{{ $data->id }}" name="survey_question_id">
                        <div id="answerFields">
                            <div class="row">
                                <div class="col-12 col-md-12">
                                    <div class="form-group">
                                        <label for="addAnswerOptionText">
                                            Answer
                                        </label>
                                        <input type="text" name="answer_text[]" class="form-control"
                                            id="addAnswerOptionText" placeholder="Enter answer text">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group" style="padding-left: 1rem !important;">
                                    <button type="button" class="btn btn-primary" id="addMoreAnswers">
                                        <i class="fa fa-plus"></i> Add More Answers
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                            Save
                        </button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
@endsection

@section('page_scripts')

    <script>
        $(document).ready(function() {

            $(document).on('click', '.addAnswerOptionBtn', function(e) {
                e.preventDefault();
                $('#addAnswerOptionModal').modal('show');
            });

            $("#addMoreAnswers").click(function() {
                let newAnswerField = `
                <div class="answer-group">
                    <div class="row">
                        <div class="col-md-12 col-12">
                            <div class="form-group">
                                <label for="answer_text">Answer<span class="validateRq">*</span></label>
                                <input type="text" name="answer_text[]" class="form-control required answer_text" placeholder="Answer">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-12 d-flex align-items-center">
                            <button type="button" class="btn btn-danger remove-answer">
                                <i class="fa fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                    <br/>
                </div>
               `;
                $("#answerFields").append(newAnswerField);
            });

            // Remove answer field
            $(document).on("click", ".remove-answer", function() {
                $(this).closest(".answer-group").remove();
            });

            // submit answers
            $('#addAnswerOptionForm').submit(function(e) {
                e.preventDefault();
                let form = $(this);
                let formData = new FormData(form[0]);
                let path = '{{ route('survey.answers.store.ajax') }}';
                $.ajax({
                    type: "POST",
                    url: path,
                    data: formData,
                    dataType: "json",
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        form.find('button[type=submit]').html(
                            '<i class="fa fa-spinner fa-spin"></i>'
                        );
                        form.find('button[type=submit]').attr('disabled', true);
                    },
                    complete: function() {
                        form.find('button[type=submit]').html(
                            'Save'
                        );
                        form.find('button[type=submit]').attr('disabled', false);
                    },
                    success: function(data) {
                        if (data['status']) {
                            // toastr.success(data['message']);
                            $('#addAnswerOptionModal').modal('hide');
                            $('#addAnswerOptionForm').trigger("reset");
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                    },
                    error: function(data) {
                        var errors = data.responseJSON;
                        var errorsHtml = '<ul>';
                        $.each(errors['errors'], function(key, value) {
                            errorsHtml += '<li>' + value + '</li>';
                        });
                        errorsHtml += '</ul>';
                        toastr.error(errorsHtml);
                    }
                });
            });

            //-------------
            //- DONUT CH    ART -
            //-------------
            // Get context with jQuery - using jQuery's .get() method.

            var questionId = '{{ $data->id }}'; // Get question ID dynamically
            let path = '{{ route('question.chart.responses', ':id') }}';
            path = path.replace(':id', questionId);

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
                            labels: response.labels, // Labels now only show responses
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
                                    text: '{{ $data->question_text }}' // Show the question text as the title
                                }
                            }
                        }
                    });
                }
            });




        });
    </script>

@endsection
