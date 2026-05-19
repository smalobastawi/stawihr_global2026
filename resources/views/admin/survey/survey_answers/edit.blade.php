@extends('admin.master')

@section('title', 'Survey Answers')

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
                <a href="{{ route('survey.answers.index') }}"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                        class="fa fa-list-ul" aria-hidden="true"></i> View Survey Answers</a>
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
                                <form action="{{ route('survey.answers.update', ) }}" method="POST" enctype="multipart/form-data" id="surveyForm">
@csrf
@method('PUT')

                            @else
                                <form method="POST">
							@csrf
                            @endif
                            @csrf
                            <div class="form-body">
                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="survey_question_id">Question<span class="validateRq">*</span></label>
                                            <select name="survey_question_id" id="survey_question_id" class="form-control">
                                                <option disabled selected>Choose Survey Question</option>
                                                @forelse ($surveyQuestions as $surveyQuestionTitle)
                                                    <option value="{{ $surveyQuestionTitle->id }}" @if(isset($editModeData) && $editModeData->survey_question_id == $surveyQuestionTitle->id)
                                                        selected
                                                    @endif>
                                                        {{ $surveyQuestionTitle->question_text }}
                                                    </option>
                                                @empty
                                                    <option disabled>No Active Survey added</option>
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="answer_text">Answer<span class="validateRq">*</span></label>
                                            @php
                                                $value = isset($editModeData)
                                                    ? $editModeData->answer_text
                                                    : Request::old('answer_text');
                                            @endphp
                                            <input type="text" name="answer_text" value="{{ $value }}" class="form-control required answer_text" id="answer_text" placeholder="Answer">
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

