<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Modern admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities with bitcoin dashboard.">
    <meta name="keywords" content="admin template, modern admin template, dashboard template, flat admin template, responsive admin template, web app, crypto dashboard, bitcoin dashboard">
    <meta name="author" content="PIXINVENT">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bienvenidos | Carpio SAC</title>
   
    <link rel="apple-touch-icon" href="{{asset('/images/logo_carpio.png')}}">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('/images/logo_carpio.png')}}">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i%7CQuicksand:300,400,500,700" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('/css/all.css')}}">
    <link rel="stylesheet" href="{{asset('/vue-select/dist/vue-select.css')}}">
    
    
    <!-- <link rel="stylesheet" href="{{asset('/@fullcalendar/core/main.css')}}"> -->
    <link rel="stylesheet" href="{{asset('/@fullcalendar/daygrid/main.css')}}">
    <link rel="stylesheet" href="{{asset('/@fullcalendar/timegrid/main.css')}}">

    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('/vue-form-wizard/dist/vue-form-wizard.min.css') }}">
 
    <style>
        /* .modal-backdrop {
           z-index: -1;
        } */
    </style>
</head>
<body class="horizontal-layout horizontal-menu horizontal-menu-padding 2-columns" data-open="click" data-menu="horizontal-menu" data-col="2-columns">

    <div id="app">
        <App estado = "{{Auth::check()}}" 
        principal= "{{ json_encode(Session::get('menuP')) }}" usuario="{{Auth::check()?Auth::user()->usuario:null}}"></App>
    </div>

    <script type="text/javascript" src="{{asset('/js/init.js')}}"></script>   
    <script type="text/javascript" src="{{asset('/js/assets/vendors.min.js')}}"></script>
    <!-- <script type="text/javascript" src="{{asset('/js/assets/ui/jquery.sticky.js')}}"></script> SI SE COLOCA SE NOTA FEO-->
     
    <script type="text/javascript" src="{{asset('/js/assets/vendors/js/forms/icheck/icheck.min.js')}}"></script>
  
    <!-- <script type="text/javascript" src="{{asset('/js/assets/charts/chartist.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/js/assets/charts/chartist-plugin-tooltip.min.js')}}"></script> -->

    <!-- <script type="text/javascript" src="{{asset('/js/assets/charts/raphael-min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/js/assets/charts/morris.min.js')}}"></script> -->
 
    <!-- {{-- <script type="text/javascript" src="{{asset('/js/assets/timeline/horizontal-timeline.js')}}"></script> --}} -->

    <script type="text/javascript" src="{{asset('/js/assets/core/app-menu.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/js/assets/core/app.min.js')}}"></script>

    <script type="text/javascript" src="{{asset('/js/assets/scripts/customizer.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/js/assets/scripts/footer.min.js')}}"></script>
    <!-- <script type="text/javascript" src="{{asset('/js/assets/scripts/pages/dashboard-ecommerce.min.js')}}"></script> -->
     
    <script type="text/javascript" src="{{asset('/js/assets/scripts/sweetalert2/dist/sweetalert2.all.min.js')}}"></script>
    
    <!-- <script type="text/javascript" src="{{asset('/js/assets/vendors/js/forms/select/selectivity-full.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/js/assets/scripts/forms/select/form-selectivity.min.js')}}"></script> -->
 
    <!-- <script type="text/javascript" src="{{asset('/js/assets/scripts/forms/checkbox-radio.min.js')}}"></script> -->

</body>
</html>