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
                <a href="{{ route('survey.responses.index') }}"
                    class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                        class="fa fa-list-ul" aria-hidden="true"></i> Survey List</a>
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
                            <form action="{{ route('survey.responses.store', $data->id) }}" method="POST">
                                @csrf
                                <div class="form-body">
                                    @foreach ($data->surveyQuestion as $question)
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="exampleInput">
                                                        {{ $loop->iteration }}. {{ $question->question_text }}<span class="validateRq">*</span>
                                                    </label>

                                                    @php
                                                        $existingResponse = $existingResponses[$question->id] ?? null;
                                                    @endphp

                                                    @switch($question->answer_type)
                                                        @case(\App\Lib\Enumerations\AnswerTypes::SINGLE_CHOICE)
                                                            <select class="form-control" name="answers[{{ $question->id }}]">
                                                                @foreach ($question->surveyAnswer as $answerOpt)
                                                                    {{-- Assuming $question->options contains choices --}}
                                                                    <option value="{{ $answerOpt->answer_text }}"
                                                                        {{ $existingResponse == $answerOpt->answer_text ? 'selected' : '' }}>
                                                                        {{ $answerOpt->answer_text }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        @break

                                                        @case(\App\Lib\Enumerations\AnswerTypes::MULTIPLE_CHOICE)
                                                            @foreach ($question->surveyAnswer as $multipleChoicesAnswers)
                                                                <div>
                                                                    <input type="checkbox" name="answers[{{ $question->id }}][]"
                                                                        value="{{ $multipleChoicesAnswers->answer_text }}"
                                                                        {{ isset($existingResponse) && in_array($multipleChoicesAnswers->answer_text, json_decode($existingResponse, true) ?? []) ? 'checked' : '' }}>
                                                                    {{ $multipleChoicesAnswers->answer_text }}
                                                                </div>
                                                            @endforeach
                                                        @break

                                                        @case(\App\Lib\Enumerations\AnswerTypes::TEXT)
                                                            <input type="text" class="form-control"
                                                                name="answers[{{ $question->id }}]"
                                                                value="{{ $existingResponse }}">
                                                        @break

                                                        @case(\App\Lib\Enumerations\AnswerTypes::TEXTAREA)
                                                            <textarea class="form-control" name="answers[{{ $question->id }}]">{{ $existingResponse }}</textarea>
                                                        @break

                                                        @case(\App\Lib\Enumerations\AnswerTypes::DROPDOWN)
                                                            <select class="form-control" name="answers[{{ $question->id }}]">
                                                                <option disabled selected>Choose Answer</option>
                                                                @forelse ($question->surveyAnswer as $dropdownChoiceAnswers)
                                                                    <option value="{{ $dropdownChoiceAnswers->answer_text }}"
                                                                        {{ $existingResponse == $dropdownChoiceAnswers->answer_text ? 'selected' : '' }}>
                                                                        {{ $dropdownChoiceAnswers->answer_text }}
                                                                    </option>
                                                                @empty
                                                                @endforelse


                                                            </select>
                                                        @break

                                                        @case(\App\Lib\Enumerations\AnswerTypes::NUMBER)
                                                            <input type="number" class="form-control"
                                                                name="answers[{{ $question->id }}]"
                                                                value="{{ $existingResponse }}">
                                                        @break

                                                        @case(\App\Lib\Enumerations\AnswerTypes::DATEPICKER)
                                                            <input type="text" class="form-control datepicker"
                                                                name="answers[{{ $question->id }}]"
                                                                value="{{ $existingResponse }}">
                                                        @break

                                                        @case(\App\Lib\Enumerations\AnswerTypes::RATING_SCALE)
                                                            <select class="form-control" name="answers[{{ $question->id }}]">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    {{-- Example rating from 1 to 5 --}}
                                                                    <option value="{{ $i }}"
                                                                        {{ $existingResponse == $i ? 'selected' : '' }}>
                                                                        {{ $i }}
                                                                    </option>
                                                                @endfor
                                                            </select>
                                                        @break

                                                        @case(\App\Lib\Enumerations\AnswerTypes::LIKERT_SCALE)
                                                            <div>
                                                                @foreach (['Strongly Disagree', 'Disagree', 'Neutral', 'Agree', 'Strongly Agree'] as $scale)
                                                                    <label>
                                                                        <input type="radio" name="answers[{{ $question->id }}]"
                                                                            value="{{ $scale }}"
                                                                            {{ $existingResponse == $scale ? 'checked' : '' }}>
                                                                        {{ $scale }}
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        @break

                                                        @case(\App\Lib\Enumerations\AnswerTypes::FILE_UPLOAD)
                                                            <input type="file" class="form-control"
                                                                name="answers[{{ $question->id }}]">
                                                            @if ($existingResponse)
                                                                <p>Uploaded File: <a
                                                                        href="{{ asset('storage/' . $existingResponse) }}"
                                                                        target="_blank">View File</a></p>
                                                            @endif
                                                        @break

                                                        @case(\App\Lib\Enumerations\AnswerTypes::YES_NO)
                                                            <div>
                                                                <label>
                                                                    <input type="radio" name="answers[{{ $question->id }}]"
                                                                        value="yes" {{ $existingResponse == 'yes' ? 'checked' : '' }}>
                                                                    Yes
                                                                </label>
                                                                <label>
                                                                    <input type="radio" name="answers[{{ $question->id }}]"
                                                                        value="no" {{ $existingResponse == 'no' ? 'checked' : '' }}>
                                                                    No
                                                                </label>
                                                            </div>
                                                        @break

                                                        @default
                                                            <input type="text" class="form-control"
                                                                name="answers[{{ $question->id }}]" value="{{ $existingResponse }}">
                                                    @endswitch

                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <br>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button type="submit" class="btn btn-info btn_style"><i
                                                    class="fa fa-pencil"></i>
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
            $(document).on("focus", ".datepicker", function() {
                $(this).datepicker({
                    format: 'yyyy-mm-dd',
                    todayHighlight: true,
                    clearBtn: true,
                    // startDate: new Date(),
                }).on('changeDate', function(e) {
                    $(this).datepicker('hide');
                });
            });
        });
    </script>
@endsection
