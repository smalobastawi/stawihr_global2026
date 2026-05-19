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
                <a href="{{ route('survey.edit', $data->id) }}"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                    <i class="fa fa-list-ul" aria-hidden="true"></i> Edit Survey
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
        </div><!--/.row -->

        <div class="row">
            <div class="col-lg-12">
                <div class="white-box">
                    <h1 style="color: #0a0c0d; font-size:26px;" class="box-title">
                        {{ $data->title }}
                    </h1>
                    <p>
                        {{ $data->description }}
                    </p>
                    <p>
                        <b>Starting Date: </b> {{ Carbon::parse($data->start_date)->format('d F Y') }}
                    </p>
                    <p>
                        <b>Ending Date: </b> {{ Carbon::parse($data->end_date)->format('d F Y') }}
                    </p>
                </div><!--/.white-box -->
            </div><!--/.col -->
        </div><!--/.row -->

        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-table fa-fw"></i> {{ $data->title }} survey questions
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
                                            <th>Question</th>
                                            <th>Answer Type</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {!! $sl = null !!}
                                        @foreach ($data->surveyQuestion as $value)
                                            <tr class="{!! $value->id !!}">
                                                <td style="width: 100px;">
                                                    {!! ++$sl !!}
                                                </td>
                                                <td>
                                                    <a href="{{ route('survey.questions.show', $value->id) }}">
                                                        {{ $value->question_text }}
                                                    </a>
                                                </td>
                                                <td>
                                                    @if ($value->answer_type)
                                                        {{ \AnswerTypes::getName($value->answer_type) }}
                                                    @endif
                                                </td>
                                                <td style="width: 100px;">
                                                    <a href="{!! route('survey.questions.edit', $value->id) !!}"
                                                        class="btn btn-success btn-xs btnColor">
                                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                    </a>
                                                    <a href="{!! route('survey.questions.delete', $value->id) !!}"
                                                        class="delete btn btn-danger btn-xs deleteBtn btnColor"
                                                        data-token="{!! csrf_token() !!}"
                                                        data-id="{!! $value->id !!}">
                                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                    </a>
                                                    @if($value->answer_type == \AnswerTypes::SINGLE_CHOICE || $value->answer_type == \AnswerTypes::MULTIPLE_CHOICE || $value->answer_type == \AnswerTypes::DROPDOWN)
                                                        <a href="{{ route('survey.questions.show', $value->id) }}" class="btn btn-info btn-xs btnColor">
                                                            <i class="fa fa-check-circle" aria-hidden="true"></i>
                                                        </a>
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
    </div>

    <div class="modal fade" id="addSurveyQuestionModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="addSurveyQuestionForm" method="POST">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" value="{{ $data->id }}" name="survey_id">
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="form-group">
                                    <label for="addSurveyQuestionText">
                                        Question
                                    </label>
                                    <input type="text" name="question_text" class="form-control"
                                        id="addSurveyQuestionText" placeholder="Enter question text">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="form-group">
                                    <label for="addSurveyQuestionAnswerType">
                                        Answer Type
                                    </label>
                                    <select name="answer_type" id="addSurveyQuestionAnswerType" class="form-control">
                                        <option disabled selected>Choose Answer Type</option>
                                        @foreach (\AnswerTypes::toArray() as $key => $value)
                                            <option value="{{ $key }}">
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
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
            $(document).on('click', '.addSurveyQuestionBtn', function(e) {
                e.preventDefault();
                $('#addSurveyQuestionModal').modal('show');
                $('#addSurveyQuestionForm').trigger('reset');
            });

            $('#addSurveyQuestionForm').submit(function(e) {
                e.preventDefault();
                let form = $(this);
                let formData = new FormData(form[0]);
                let path = '{{ route('survey.questions.store') }}';
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
                            'Submit'
                        );
                        form.find('button[type=submit]').attr('disabled', false);
                    },
                    success: function(data) {
                        if (data['status']) {
                            // toastr.success(data['message']);
                            $('#addSurveyQuestionModal').modal('hide');
                            $('#addSurveyQuestionForm').trigger("reset");
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
        });
    </script>

@endsection
