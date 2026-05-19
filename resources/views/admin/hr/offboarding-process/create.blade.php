@extends('admin.master')
@section('content')
@section('title')
@lang('offboarding.add_off_boarding_process_item')
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="">
            <ol class="breadcrumb
            float-sm-right">
                <li class="breadcrumb
                -item"><a href="{{ url('/') }}">Home</a></li>
                @foreach (urlTree() as $item)
                    <li class="breadcrumb
                    -item text-primary"><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
                @endforeach
            </ol>
        </div>
        @can("offboarding-process.create")
            <div class="col-lg-12 col-sm-8 col-md-6 col-xs-6">
                <a href="{{ route('offboarding-process.create') }}" class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"> <i class="fa fa-plus-circle" aria-hidden="true"></i> @lang('offboarding.add_off_boarding_process_item')</a>
            </div>
        @endcan
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body
                    ">
                    @if(session()->has('success'))
                        <div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                        </div>
                    @endif
                    @if(session()->has('error'))
                        <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                        </div>
                    @endif

                    <form action="{{ route('offboarding-process.store') }}" method="post" class="form-horizontal">
                        @csrf
                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="checklist_name" name="checklist_name" placeholder="@lang('offboarding.checklist_name')" required>
                            </div>
                        </div>
                        
                        
                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="text-area" class="form-control" id="description" name="description" placeholder="@lang('offboarding.description')" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <div id="checklist-items">
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="checklist_items[]" placeholder="@lang('offboarding.enter_item')" required>
                                        &nbsp;&nbsp;<br>
                                        <button type="button" class="btn btn-danger remove-item"><i class="glyphicon glyphicon-remove"></i></button>
                                    </div>
                                    &nbsp;&nbsp;<br>
                                </div>
                                <button type="button" id="add-item" class="btn btn-primary">@lang('offboarding.add_item')</button>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-success">@lang('common.save')</button>
                            </div>
                        </div>
                    </form>
                    
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            document.getElementById("add-item").addEventListener("click", function () {
                                let container = document.getElementById("checklist-items");
                                let newItem = document.createElement("div");
                                newItem.classList.add("input-group", "mb-2");
                                newItem.innerHTML = `
                                    <input type="text" class="form-control" name="checklist_items[]" placeholder="@lang('offboarding.enter_item')" required>
                                    <button type="button" class="btn btn-danger remove-item">&times;</button>
                                `;
                                container.appendChild(newItem);
                            });
                    
                            document.getElementById("checklist-items").addEventListener("click", function (event) {
                                if (event.target.classList.contains("remove-item")) {
                                    event.target.parentElement.remove();
                                }
                            });
                        });
                    </script>
                    
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

                    

