<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="{!! asset('assets/js/cdnsj/html5shiv.min.js') !!}"></script>
    <script src="{!! asset('assets/js/cdnsj/respond.min.js') !!}"></script>

    <![endif]-->

    <script src="{!! asset('admin_assets/plugins/bower_components/jquery/dist/jquery.min.js') !!}"></script>
    {{--
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" --}} {{-- rel="stylesheet"> --}}

    {{-- <link rel="stylesheet" href="{!! asset('fonts/Garuda-Bold.ttf') !!}">
    <link rel="stylesheet" href="{!! asset('fonts/Garuda-BoldOblique.ttf') !!}">
    <link rel="stylesheet" href="{!! asset('fonts/Garuda.ttf') !!}">
    <link rel="stylesheet" href="{!! asset('assets/fonts/nucleo/nucleo-icons.eot') !!}">
    <link rel="stylesheet" href="{!! asset('assets/fonts/nucleo/nucleo-icons.svg') !!}">
    <link rel="stylesheet" href="{!! asset('assets/fonts/nucleo/nucleo-icons.woff') !!}">
    <link rel="stylesheet" href="{!! asset('assets/fonts/nucleo/nucleo-icons.woff2') !!}"> --}}
<style>
/* For Garuda Fonts */
@font-face {
    font-family: 'Garuda';
    src: url('/fonts/Garuda.ttf') format('truetype');
    font-weight: normal;
    font-style: normal;
}

@font-face {
    font-family: 'Garuda';
    src: url('/fonts/Garuda-Bold.ttf') format('truetype');
    font-weight: bold;
    font-style: normal;
}

@font-face {
    font-family: 'Garuda';
    src: url('/fonts/Garuda-BoldOblique.ttf') format('truetype');
    font-weight: bold;
    font-style: italic;
}

/* For Nucleo Icons (if needed) */
@font-face {
    font-family: 'Nucleo';
    src: url('/assets/fonts/nucleo/nucleo-icons.eot');
    src: url('/assets/fonts/nucleo/nucleo-icons.eot?#iefix') format('embedded-opentype'),
         url('/assets/fonts/nucleo/nucleo-icons.woff2') format('woff2'),
         url('/assets/fonts/nucleo/nucleo-icons.woff') format('woff'),
         url('/assets/fonts/nucleo/nucleo-icons.svg') format('svg');
    font-weight: normal;
    font-style: normal;
}

</style>
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" /> --}}

    {{-- <script src="{!! asset('assets/js/cdnsj/jquery.min.js') !!}"></script> --}}
    <script href="{!! asset('admin_assets/plugins/bower_components/toastr/toastr.min.js')  !!}"></script>
    <script type="text/javascript">
        var base_url = "{{ url('/') . '/' }}";
    </script>
       {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script> --}}