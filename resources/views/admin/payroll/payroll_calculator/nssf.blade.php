@extends('admin.master')

@section('title', getPageTitle() . ' | ' . config('app.name'))
@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-md-6">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                            @lang('dashboard.dashboard')</a></li>
                    @foreach (breadCrumbs() as $item)
                        <li class="breadcrumb-item"><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
                    @endforeach
                </ol>
            </div>
            <div class="">
                {{--				<a href="{{route('calculatePaye')}}" class="btn btn-primary pull-right">Calculate Paye</a> --}}
                <a href="{{ route('generateSalarySheet.create') }}"
                    class="btn btn-success pull-right m-l-20  waves-effect waves-light"> <i class="fa fa-plus-circle"
                        aria-hidden="true"></i> @lang('salary_sheet.generate_salary')</a>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">

                    <div class="panel-wrapper collapse in" aria-expanded="true">
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
                                    <strong>{{ session()->get('error') }}</strong>
                                </div>
                            @endif
                            @include('admin.payroll.payroll_calculator.header-links')
                            <hr>
                            <div class="row">
                                <div class="p-6 row">
                                    <div class="col-md-4">
                                        <div class="flex items-center">
                                            <div class="ml-4 text-lg leading-7 font-semibold">
                                                <h2>
                                                NSSF Calculator</div> 
                                                </h2>
                                                <hr>
                                        </div>
                    
                                        <div class="ml-12">
                                            <div class="">
                    
                                                <form class="" action="{{route('payrollcaculator.nssf')}}" method="get">
                                                    @csrf
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <label for="" class="form-group">Year</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select class="form-control select2" name="yearOfTax">
                                                                    <option value="2024">2024</option>
                                                                   
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                    
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <label for="" class="form-group">No of months</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="number" name="months" class="form-control" min="1" value="{{ isset($data['request']->months) ? $data['request']->months : '1' }}" required readonly>

                                                                
                                                            </div>
                                                        </div>
                                                      
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <label for="" class="form-group">Select NSSF Rate to use</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select class="form-control" name="nssf_rate_type">
                                                                    <option value="2" {{ isset($data['request']->nssf_rate_type) && $data['request']->nssf_rate_type === '2' ? 'selected' : 'selected' }}>Tier I & 2</option>
                                                                    <option value="3" {{ isset($data['request']->nssf_rate_type) && $data['request']->nssf_rate_type === '3' ? 'selected' : '' }}>Tier I only</option>
                                                                    <option value="4" {{ isset($data['request']->nssf_rate_type) && $data['request']->nssf_rate_type === '4' ? 'selected' : '' }}>No deduction</option>
                                                                   

                                                                </select>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                    
                                                
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <label for="" class="form-group">Amount Type</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select class="form-control" name="amount_type">
                                                                    <option value="gross" {{ isset($data['request']->amount_type) && $data['request']->amount_type === 'gross' ? 'selected' : '' }}>GROSS</option>
                                                                    <option value="taxable" {{ isset($data['request']->amount_type) && $data['request']->amount_type === 'taxable' ? 'selected' : '' }}>TAXABLE</option>
                                                                </select>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                    
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <label for="" class="form-group">Amount</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input name="amount" type="number" class="form-control" id=""  placeholder="Enter amount" value="{{ isset($data['request']->amount) ? $data['request']->amount : '' }}" required>

                                                            </div>
                                                        </div>
                                                    </div>
                    
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <button type="submit" class="btn btn-primary">Calculate</button>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <button type="reset" class="btn btn-primary">Clear</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-6 border-t border-gray-200 dark:border-gray-700 md:border-t-0 md:border-l">
                                            <div class="flex items-center">
                                                <div class="ml-4 text-lg leading-7 font-semibold">Results:
                                                </div>
                                            </div>
                                            @if(isset($data['request']->amount))
                                                <div class="ml-12">
                                                    <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                                        
                                                        <div class="row">
                                                            <div class="col-md-6">Year:</div>
                                            
                                                            <div class="col-md-6 text-right"> <strong >{{$data['request']->yearOfTax}} </strong></div>
                                                        
                                                        </div>
                                                        
                                                        <div class="row">
                                                            <div class="col-md-6">No of months</div>
                                                            <div class="col-md-6 text-right"> <strong > {{$data['request']->months}} </strong > </div>
                                                            
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">GROSS Amount</div>
                                                            <div class="col-md-6 text-right"> <strong>
                                                                    {{ number_format($data['gross_amount'], 2) }}</strong>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">TAXABLE Amount</div>
                                                            <div class="col-md-6 text-right"> <strong>
                                                                    {{ number_format($data['taxable_amount'], 2) }}</strong>
                                                            </div>
                                                        </div>
                                                       
                                                      <div class="row">
                                                            <div class="col-md-6">NSSF
                                                            </div>
                                                            <div class="col-md-6 text-right">
                                                                <div class="row">Tier 1: <strong > {{number_format($data['nssf_rates']['nssf_tier1'],2)}} </strong></div>
                                                                <div class="row">Tier 2:   <strong > {{number_format($data['nssf_rates']['nssf_tier2'],2)}} </strong > </div>
                                                                <div class="row">Total:   <strong > {{number_format($data['nssf_rates']['total_nssf'],2)}} </strong > </div>
                                                            </div>
                                                
                                                        </div>
                                                     
                                                    
                                                        <div class="row">
                                                            <div class="col-md-6">Insurance Relief:</div>
                                                            <div class="col-md-6 text-right"> <strong > {{number_format($data['insuranceRelief'],2)}} </strong > </div>
                                                           
                                                        </div>
                                                       
                                                      
                                                    </div>
                                                </div>
                                                <hr>
                                                    <div class="row">
                                                        <div class="col-md-6 text-right"><a class="btn btn-primary" href="{{route('payrollcaculator.nssf')}}"
                                                                                 style="color: white">New
                                                                calculation</a></div>
                                                        
                                                    </div>
                    
                                                
                                            @else
                                                <div class="ml-12">
                                                    No results yet. Enter details and click calculate to see the results.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        @include('admin.payroll.payroll_calculator.about_calculator')
                                    </div>
                    
                                </div>
                            </div>

                            <br>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page_scripts')
    <script></script>
@endsection
