 <!-- Bootstrap Core CSS -->
 {{--
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"> --}}
 {{--
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    --}}
 {{--
    <link rel="stylesheet" href="{{url('css/bootstrap.min.css')}}" /> --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
 <link rel="stylesheet" href="{!! asset('admin_assets/bootstrap/dist/css/bootstrap.min.css') !!}" />
 {{--
    <link rel="stylesheet" href="{{ url('css/dist/skins/skin-blue.css') }}"> --}}
 {{--
    <link href="{{url('css/css/dist/all.css')}}" rel="stylesheet"> --}}
 <link href="{{ url('css/css/dist/all-1.css') }}" rel="stylesheet">

 <!-- Menu CSS -->
 {{--
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" --}} {{--
        integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    --}}
 <link href="{!! asset('admin_assets/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') !!}" rel="stylesheet">
 <!-- toast CSS -->
 <link href="{!! asset('admin_assets/plugins/bower_components/toast-master/css/jquery.toast.css') !!}" rel="stylesheet">
 <!-- morris CSS -->
 <link href="{!! asset('admin_assets/plugins/bower_components/morrisjs/morris.css') !!}" rel="stylesheet">
 <!-- animation CSS -->
 <link href="{!! asset('admin_assets/css/animate.css') !!}" rel="stylesheet">
<!-- Custom CSS -->
<link href="{!! asset('admin_assets/css/style.css') !!}" rel="stylesheet">
<!-- Mobile Responsive CSS - Makes top bar scrollable on small screens -->
<link href="{!! asset('admin_assets/css/mobile-responsive-fix.css') !!}" rel="stylesheet">
<!-- color CSS -->
 <link href="{!! asset('admin_assets/css/colors/megna-dark.css') !!}" id="theme" rel="stylesheet">
 <!-- data table CSS -->
 <link href="{!! asset('admin_assets/plugins/bower_components/datatables/jquery.dataTables.min.css') !!}" rel="stylesheet" type="text/css" />

 {{--
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" --}} {{--
        type="text/css" /> --}}

 {{--
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" --}} {{--
        type="text/css" /> --}}

 <!-- Date Picker -->
 <link rel="stylesheet" href="{!! asset('admin_assets/plugins/bower_components/datepicker/datepicker3.css') !!}">
 <!-- Daterange picker -->
 <link rel="stylesheet" href="{!! asset('admin_assets/plugins/bower_components/daterangepicker/daterangepicker-bs3.css') !!}">
 <!-- time picker-->
 <link rel="stylesheet" href="{!! asset('admin_assets/plugins/bower_components/timepicker/bootstrap-timepicker.min.css') !!}">
 <!-- sweetalert-->
 <link rel="stylesheet" href="{!! asset('admin_assets/plugins/bower_components/sweetalert/sweetalert.css') !!}">
 <!-- select 2 -->
 <link rel="stylesheet" href="{!! asset('admin_assets/plugins/bower_components/select2/select2.min.css') !!}">
 <!-- toast CSS -->
 <link href="{!! asset('admin_assets/plugins/bower_components/toast-master/css/jquery.toast.css') !!}" rel="stylesheet">
 <!-- Star Ratings -->
 <link href="{!! asset('admin_assets/plugins/bower_components/rateyo/jquery.rateyo.min.css') !!}" rel="stylesheet">
 <link href="{!! asset('admin_assets/css/icons/material-design-iconic-font/css/materialdesignicons.min.css') !!}" rel="stylesheet">

 <link rel="stylesheet" href="{!! asset('admin_assets/plugins/bower_components/toastr/toastr.min.css') !!}">

 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.9.1/summernote-bs4.min.css" />

 @include('admin.components.libraries')

 
 <style>
    @media print {
    .navbar-default.sidebar, 
    .navbar-header,
    .breadcrumb,
    .navbar,
    .btn,
    .button, 
    .navbar-top-links,
    .sidebar-nav, 
    .user-profile,
    .nav#side-menu {
        display: none !important;
    }
    
    /* Adjust content area to use full width */
    .content-wrapper, .right-side {
        margin-left: 0 !important;
    }
}
     /*for yellow bg*/

     .navbar-header {
         background: #222a48;
     }

     #side-menu li a {
         color: #fff;
         border-left: 0px solid #2f323e;
     }

     .top-left-part .dark-logo {
         display: block;

     }

     .tiMenu {
         color: #fff;
     }

     .sidebar {
         background: #43436678;
         ;
         box-shadow: 1px 0px 20px rgba(0, 0, 0, 0.08);
     }

     .hideMenu {
         color: #fff;
     }

     #side-menu ul>li>a.active {
         color: #EDDF10;
         font-weight: 400;
     }

     #side-menu ul>li>a:hover {
         color: #fff;
     }

     /*for yellow bg*/

     .bg-title .breadcrumb {
         background: 0 0;
         margin-bottom: 0;
         float: none;
         padding: 0;
         margin-bottom: 9px;
         font-weight: 700;
         color: #777;
     }

     .select2-container .select2-selection--single .select2-selection__rendered {
         height: auto;
         margin-top: -6px;
         padding-left: 0;
         padding-right: 0;
     }

     .select2-container .select2-selection--single {
         box-sizing: border-box;
         cursor: pointer;
         display: block;
         height: 35px;
     }

     .select2-container--default .select2-selection--single,
     .select2-selection .select2-selection--single {
         border: 1px solid #d2d6de;
         border-radius: 0;
         padding: 8px 11px;
     }

     .select2-container--default .select2-selection--single .select2-selection__arrow {
         height: 26px;
         position: absolute;
         top: 4px;
         right: 1px;
         width: 20px;
     }

     .breadcrumbColor a {
         color: #41b3f9 !important;
     }

     tr td {
         color: black !important;
     }

     .tr_header {
         background-color: #EDF1F5;
     }

     table.dataTable thead th,
     table.dataTable thead td {
         padding: 10px 18px;
         border-bottom: 1px solid #e4e7ea;
     }

     .btnColor {
         color: #fff !important;
     }

     .validateRq {
         color: red;
     }

     .panel .panel-heading {
         border-radius: 0;
         font-weight: 500;
         /*font-size: 11px;*/
         padding: 10px 25px;
     }

     /* .btn_style {
         width: 106px;
     } */

     .error {
         color: red;
     }
 </style>
 <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
 <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
