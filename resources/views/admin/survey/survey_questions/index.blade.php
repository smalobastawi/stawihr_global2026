@extends('admin.master')
@section('content')
@section('title', 'Survey Questions List')

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    @yield('title')
                </li>
            </ol>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <a href="{{ route('survey.questions.create') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> Add Survey Question
            </a>

        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-table fa-fw"></i> @yield('title')
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
                                        <th>Survey</th>
                                        <th>Question</th>
                                        <th>Answer Type</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!! $sl = null !!}
                                    @foreach ($data as $value)
                                        <tr class="{!! $value->id !!}">
                                            <td style="width: 100px;">{!! ++$sl !!}</td>
                                            <td>
                                                <a href="{{ route('survey.show', $value->survey->id) }}">
                                                    {{ $value->survey->title }}
                                                </a>
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

                                                @if (
                                                    $value->answer_type == \AnswerTypes::SINGLE_CHOICE ||
                                                        $value->answer_type == \AnswerTypes::MULTIPLE_CHOICE ||
                                                        $value->answer_type == \AnswerTypes::DROPDOWN)
                                                    <a href="{{ route('survey.questions.show', $value->id) }}"
                                                        class="btn btn-info btn-xs btnColor">
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
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
