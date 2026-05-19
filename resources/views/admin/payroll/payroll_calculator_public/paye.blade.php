@php
$front_setting = getFrontData();
@endphp
@extends('front.master')

@section('title')
{{ $front_setting->company_title }}
@endsection

@section('meta')

<meta name="og:title" content="{{ $front_setting->company_title }}" />
<meta name="og:image" content="{{ asset('storage/uploads/front/'.$front_setting->logo) }}" />
<meta name="og:url" content="{{ url('/') }}" />
<meta name="og:description" content="{{ $front_setting->about_us_description }}" />
<meta name="description" content="{{ $front_setting->about_us_description }}" />

@endsection

@section('content')
    <!-- Start Home -->
    <div  class="" style="center center;">
        
        <div class="home-center">
            <div class="home-desc-center">
                <div class="container">
                    <div class="row justify-content-center">
                        
                        <div class="col-lg-12">
                            <div class="row">
                                <!-- panel -->
                                <div class="col-lg-3 col-xs-6">
                                    <a href="{{route('payrollcaculator_paye')}}">
                                        <!-- small box -->
                                    </a><div class="small-box bg-teal"><a href="#">
                                         

                                        </a><a href="{{ route('payrollcaculator_paye') }}" class="small-box-footer"><div class="btn waves-effect waves-light"> PAYE Calculator </div><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                                    </div>

                                </div><!-- ./col -->

                                <div class="col-lg-3 col-xs-6">
                                   
                                        <!-- small box -->
                                    </a><div class="small-box bg-maroon"><a href="{{route('payrollcaculator_nhif')}}">
                                           
                    
                                        </a><a href="{{route('payrollcaculator_nhif')}}" class="small-box-footer"><div class="btn waves-effect waves-light"> NHIF Calculator </div><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                                    </div>

                                </div><!-- ./col -->


                                <div class="col-lg-3 col-xs-6">
                                   <div class="small-box bg-orange"><a href="{{route('payrollcaculator_nssf')}}">
                                           
                                        </a>
                                        <a href="{{route('payrollcaculator_nssf')}}" class="small-box-footer"><div class="btn waves-effect waves-light"> NSSF Calculator </div><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                                    </div>

                                </div><!-- ./col -->

                                <div class="col-lg-3 col-xs-6">
                                    <div class="small-box bg-orange"><a href="{{route('payrollcaculator_ahl')}}">
                                            
                                         </a>
                                         <a href="{{route('payrollcaculator_ahl')}}" class="small-box-footer"><div class="btn waves-effect waves-light"> AHL Calculator </div><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                                     </div>
 
                                 </div><!-- ./col -->


                            </div>
                            <hr>
                            <div class="row">
                                <div class="p-6 row">
                                    <div class="col-md-4">
                                        <div class="flex items-center">
                                            <h2 class="ml-4 text-lg leading-7 font-semibold">Calculate Paye</h2>
                                        </div>

                                        <div class="ml-12">
                                            <div class="">

                                                <form class="" action="{{ route('payrollcaculator_paye') }}"
                                                    method="get">
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
                                                                <label for="" class="form-group">No of
                                                                    months</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="number" name="months" class="form-control"
                                                                    min="1"
                                                                    value="{{ isset($data['request']->months) ? $data['request']->months : '1' }}"
                                                                    required readonly>


                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <label for="" class="form-group">Select NSSF Rate
                                                                    to use</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select class="form-control" name="nssf_rate_type">
                                                                    
                                                                    <option value="2"
                                                                        {{ isset($data['request']->nssf_rate_type) && $data['request']->nssf_rate_type === '2' ? 'selected' : '' }}>
                                                                        Tier I & 2</option>
                                                                    <option value="3"
                                                                        {{ isset($data['request']->nssf_rate_type) && $data['request']->nssf_rate_type === '3' ? 'selected' : '' }}>
                                                                        Tier I only</option>
                                                                    <option value="4"
                                                                        {{ isset($data['request']->nssf_rate_type) && $data['request']->nssf_rate_type === '4' ? 'selected' : '' }}>
                                                                        No deduction</option>
                                                                </select>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <label for="" class="form-group">Amount
                                                                    Type</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select class="form-control" name="amount_type">
                                                                    <option value="gross"
                                                                        {{ isset($data['request']->amount_type) && $data['request']->amount_type === 'gross' ? 'selected' : '' }}>
                                                                        GROSS</option>
                                                                    <option value="taxable"
                                                                        {{ isset($data['request']->amount_type) && $data['request']->amount_type === 'taxable' ? 'selected' : '' }}>
                                                                        TAXABLE</option>
                                                                </select>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <label for="" class="form-group">Include AHL Relief</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input name="include_ahl" type="checkbox" class="form-check-input"
                                                                    id="include_ahl"
                                                                    {{ isset($data['request']->include_ahl) && $data['request']->include_ahl ? 'checked' : '' }}>
                                                                <label for="include_ahl">AHL Relief</label>
                                                            </div>
                                                            
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <label for="" class="form-group">Amount</label>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input name="amount" type="number" class="form-control"
                                                                    id="" placeholder="Enter amount"
                                                                    value="{{ isset($data['request']->amount) ? $data['request']->amount : '' }}"
                                                                    required>

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <button type="submit"
                                                                class="btn btn-primary">Calculate</button>
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
                                        <div
                                            class="p-6 border-t border-gray-200 dark:border-gray-700 md:border-t-0 md:border-l">
                                            <div class="flex items-center">
                                                <div class="ml-4 text-lg leading-7 font-semibold">Results:
                                                </div>
                                            </div>
                                            @if (isset($data['request']->amount))
                                                <div class="ml-12">
                                                    <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">

                                                        <div class="row">
                                                            <div class="col-md-6">Year:</div>

                                                            <div class="col-md-6 text-right">
                                                                <strong>{{ $data['request']->yearOfTax }} </strong></div>

                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">No of months</div>
                                                            <div class="col-md-6 text-right"> <strong>
                                                                    {{ $data['request']->months }} </strong> </div>

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
                                                                <div class="row">Tier 1: <strong>
                                                                        {{ number_format($data['nssf_rates']['nssf_tier1'], 2) }}
                                                                    </strong></div>
                                                                <div class="row">Tier 2: <strong>
                                                                        {{ number_format($data['nssf_rates']['nssf_tier2'], 2) }}
                                                                    </strong> </div>
                                                                <div class="row">Total: <strong>
                                                                        {{ number_format($data['nssf_rates']['total_nssf'], 2) }}
                                                                    </strong> </div>
                                                            </div>

                                                        </div>


                                                        <div class="row">
                                                            <div class="col-md-6">NHIF:</div>
                                                            <div class="col-md-6 text-right"> <strong>
                                                                    {{ number_format($data['nhif'], 2) }} </strong> </div>

                                                        </div>


                                                        <div class="row">
                                                            <div class="col-md-6">Taxable Pay:</div>
                                                            <div class="col-md-6 text-right"> <strong>
                                                                    {{ number_format($data['tax']['taxAbleIncome'], 2) }}
                                                                </strong> </div>

                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">Housing Levy:</div>
                                                            <div class="col-md-6 text-right"> <strong>
                                                                    {{ number_format($data['ahl_employee'], 2) }} </strong>
                                                            </div>

                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">Personal Relief:</div>
                                                            <div class="col-md-6 text-right"> <strong>
                                                                    {{ number_format($data['tax']['personalReflief'], 2) }}
                                                                </strong> </div>

                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">AHL Relief:</div>
                                                            <div class="col-md-6 text-right"> <strong>
                                                                    {{ number_format($data['tax']['ahl_relief'], 2) }}
                                                                </strong> </div>

                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">Insurance Relief:</div>
                                                            <div class="col-md-6 text-right"> <strong>
                                                                    {{ number_format($data['tax']['insuranceRelief'], 2) }}
                                                                </strong> </div>

                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">P.A.Y.E:</div>
                                                            <div class="col-md-6 text-right"> <strong>
                                                                    {{ number_format($data['tax']['monthlyTax'] * $data['request']->months, 2) }}
                                                                </strong> </div>

                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">Net Pay</div>
                                                            <div class="col-md-6 text-right"> <strong>
                                                                    {{ number_format($data['netSalary'] * $data['request']->months, 2) }}
                                                                </strong> </div>

                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 text-right"><a class="btn btn-primary"
                                                                href="{{ route('payrollcaculator_paye') }}"
                                                                style="color: white">New
                                                                calculation</a></div>

                                                    </div>

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

@push('javascript')
<script>
        $(function() {
            $('.data').on('click', '.pagination a', function (e) {
                getData($(this).attr('href').split('page=')[1]);
                e.preventDefault();
            });


        });

        function getData(page) {

            $.ajax({
                url : '?page=' + page,
                datatype: "html",
            }).done(function (data) {
                $('.data').html(data);
                $('html,body').animate({
        scrollTop: $(".career").offset().top},
        'slow');
            }).fail(function () {
                alert('No response from server');
            });
        }
    </script>
    
    <script>
       
       $(document).ready(function(){
         // Add smooth scrolling to all links
         $(".navigation-menu li a").on('click', function(event) {
       
           // Make sure this.hash has a value before overriding default behavior
           if (this.hash !== "") {
             // Prevent default anchor click behavior
             event.preventDefault();
       
             // Store hash
             var hash = this.hash;
       
             // Using jQuery's animate() method to add smooth page scroll
             // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
             $('html, body').animate({
               scrollTop: $(hash).offset().top
             }, 800, function(){
          
               // Add hash (#) to URL when done scrolling (default click behavior)
               window.location.hash = hash;
             });
           } // End if
         });
       });
       
           </script>
@endpush
