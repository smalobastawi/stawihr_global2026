@extends('admin.master')

@section('title', 'Survey Questions List')

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
                <a href="{{ route('survey.questions.index') }}"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                        class="fa fa-list-ul" aria-hidden="true"></i> View Survey Questions</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i> @yield('title')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                            aria-hidden="true">×</span></button>
                                    @foreach ($errors->all() as $error)
                                        <strong>{!! $error !!}</strong><br>
                                    @endforeach
                                </div>
                            @endif
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
                            @if (isset($editModeData))
                                <form id="updateSurveyQuestionForm">
                                    @method('PUT')
                                @else
                                    <form id="addSurveyQuestionForm" method="POST">
                            @endif
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="survey_id">Survey<span class="validateRq">*</span></label>
                                            <select name="survey_id" id="survey_id" class="form-control">
                                                <option disabled selected>Choose Survey</option>
                                                @forelse ($surveyData as $surveyTitle)
                                                    <option value="{{ $surveyTitle->id }}"
                                                        @if (isset($editModeData) && $editModeData->survey_id == $surveyTitle->id) selected @endif>
                                                        {{ $surveyTitle->title }}
                                                    </option>
                                                @empty
                                                    <option disabled>No Active Survey added</option>
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="question_text">Question<span class="validateRq">*</span></label>
                                            @php
                                                $value = isset($editModeData)
                                                    ? $editModeData->question_text
                                                    : Request::old('question_text');
                                            @endphp
                                            <input type="text" name="question_text" value="{{ $value }}" class="form-control required question_text" id="question_text" placeholder="Question">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="answer_type">Answer Type<span class="validateRq">*</span></label>
                                            <select name="answer_type" id="answer_type" class="form-control" required>
                                                <option disabled selected>Choose Answer Type</option>
                                                @foreach (\AnswerTypes::toArray() as $key => $value)
                                                    <option value="{{ $key }}"
                                                        @if (isset($editModeData) && $editModeData->answer_type == $key) selected @endif>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-info btn_style"><i class="fa fa-pencil"></i>
                                            Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                            </form>
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
                            let viewSurveyPath = '{{ route('survey.show', ':id') }}';
                            viewSurveyPath = viewSurveyPath.replace(':id', data['survey_id']);
                            setTimeout(() => {
                                window.location.href = viewSurveyPath;
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

            @if (isset($editModeData))
                $('#updateSurveyQuestionForm').submit(function(e) {
                    e.preventDefault();
                    let form = $(this);
                    let formData = new FormData(form[0]);
                    let surveyQuestionID = '{{  $editModeData->id }}';
                    let path = '{{ route('survey.questions.update', ':surveyQuestion') }}';
                    path = path.replace(':surveyQuestion', surveyQuestionID);
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
                                let viewSurveyPath = '{{ route('survey.show', ':id') }}';
                                viewSurveyPath = viewSurveyPath.replace(':id', data[
                                    'survey_id']);
                                setTimeout(() => {
                                    window.location.href = viewSurveyPath;
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
            @endif
        });
    </script>

@endsection