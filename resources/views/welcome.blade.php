<!DOCTYPE html>
<html dir="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Modern admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities with bitcoin dashboard.">
    <meta name="keywords" content="admin template, modern admin template, dashboard template, flat admin template, responsive admin template, web app, crypto dashboard, bitcoin dashboard">
    <meta name="author" content="PIXINVENT">
    <!-- <meta name="csrf-token" content="{{ csrf_token() }}"> -->
    <title>Bienvenidos | Carpio SAC</title>
   
    <link rel="apple-touch-icon" href="{{asset('/images/logo_carpio.png')}}">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('/images/logo_carpio.png')}}">
    <!-- <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i%7CQuicksand:300,400,500,700" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
    <!-- <link rel="stylesheet" href="{{asset('/css/c3.min.css')}}"> -->
    <link rel="stylesheet" href="{{asset('/css/all.css')}}">
    <!-- <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css"> -->
    <link rel="stylesheet" href="{{asset('/vue-select/dist/vue-select.css')}}">
    
    <!-- <link type="text/css" href="{{ asset('/css/fonts/line-awesome2/css/line-awesome.min.css') }}">     -->
    <!-- <link rel="stylesheet" href="{{asset('/@fullcalendar/core/main.css')}}"> -->
    <!-- <link rel="stylesheet" href="{{asset('/@fullcalendar/daygrid/main.css')}}">
    <link rel="stylesheet" href="{{asset('/@fullcalendar/timegrid/main.css')}}"> -->

    <!-- <link rel="stylesheet" href="{{ asset('/vue-form-wizard/dist/vue-form-wizard.min.css') }}"> -->

    <!-- <style>
        .m-4px{
            margin-top: 4px;
        }
        .col-form-label {
            font-weight:600;
            /* text-align:right; */
        }
        .icon-size {
            font-size: 13px !important;
            padding-top: 1px;
        }
        .table-responsive {
            overflow:auto;
            padding:0 1rem;
            scrollbar-color: #4469f4 rgba(0, 0, 0, 0);
            scrollbar-width: thin;
        }

        .vertical-overlay-menu .main-menu .navigation li.has-sub > a:not(.mm-next)::after {
            font-family: 'Line Awesome Free' !important;
            font-weight: 900 !important;
            content: "\f107" !important;
            font-size: 1rem !important;
            display: inline-block !important;
            position: absolute !important;
            top: 13px !important;
            transform: rotate(0) !important;
            transition: -webkit-transform .2s ease-in-out !important;
            right: 34px !important;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            box-shadow: inset 0 0 5px grey;
            border-radius: 6px;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #1f344b;
            border-radius: 6px;
        }
        

        /* .modal-backdrop {
           z-index: -1;
        } */    
    </style> -->
    <style>
        .table-responsive {
            overflow:auto;
            padding:0 1rem;
            scrollbar-color: #4469f4 rgba(0, 0, 0, 0);
            scrollbar-width: thin;
        }
        ::-webkit-scrollbar {
            width: 6px !important;
            height: 6px !important;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            box-shadow: inset 0 0 5px grey !important;
            border-radius: 6px !important;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #1f344b !important;
            border-radius: 6px !important;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #555 !important;
        }
        .card-footer, .card-header {
            background-color: #fff !important;
        }
        .breadcrumb {
            padding: .25rem 0 !important;
            margin-bottom: 1rem !important;
        }
        .no-resize {
            resize: none;
        }

        .custom-select-sm {
            /* height: calc(1.875rem + 2px); */
            /* padding-top: .5rem;
            padding-bottom: .5rem; */
            padding-left: .75rem;
            /* font-size: .375rem; */
        }
        .custom-select {
            display: inline-block;
            width: 100%;
            /* height: calc(1.25em + 1.5rem + 2px); */
            /* padding: .75rem 2rem .75rem 1rem;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem; */
            padding-left: 1rem;
            /* font-size: 1rem; */
            line-height: 1.25;
            color: #4e5154;
            vertical-align: middle;
            background: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'%3e%3cpath fill='%23464855' d='M2 0L0 2h4zm0 5L0 3h4z'/%3e%3c/svg%3e") right 1rem center/8px 10px no-repeat #fff;
            border: 1px solid #babfc7;
            border-radius: .25rem;
            appearance: none;
        }   
        .modal-xl {
            max-width: 94% !important;
            margin-left: 3% !important;
            margin-right: 3% !important;
        }
        @media (min-width: 1200px) {
            .modal-xl {
                max-width: 1140px;
            }
        }
        @media (min-width: 992px) {
            .modal-lg, .modal-xl {
                max-width: 800px;
            }
        }
    </style>
</head>
<body>
    <input type="hidden" name="tokenApp" id="tokenApp" value="{{ csrf_token() }}" />
    <div id="app"></div>

    <!-- <aside class="customizer">
        <a href="javascript:void(0)" class="service-panel-toggle"><i class="fa fa-spin fa-cog"></i></a>
        <div class="customizer-body">
            <ul class="nav customizer-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true"><i class="mdi mdi-wrench font-20"></i></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#chat" role="tab" aria-controls="chat" aria-selected="false"><i class="mdi mdi-message-reply font-20"></i></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false"><i class="mdi mdi-star-circle font-20"></i></a>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                    <div class="p-15 border-bottom">
                        
                        <h5 class="font-medium m-b-10 m-t-10">Layout Settings</h5>
                        <div class="custom-control custom-checkbox m-t-10">
                            <input type="checkbox" class="custom-control-input" name="theme-view" id="theme-view">
                            <label class="custom-control-label" for="theme-view">Dark Theme</label>
                        </div>
                        <div class="custom-control custom-checkbox m-t-10">
                            <input type="checkbox" class="custom-control-input sidebartoggler" name="collapssidebar" id="collapssidebar">
                            <label class="custom-control-label" for="collapssidebar">Collapse Sidebar</label>
                        </div>
                        <div class="custom-control custom-checkbox m-t-10">
                            <input type="checkbox" class="custom-control-input" name="sidebar-position" id="sidebar-position">
                            <label class="custom-control-label" for="sidebar-position">Fixed Sidebar</label>
                        </div>
                        <div class="custom-control custom-checkbox m-t-10">
                            <input type="checkbox" class="custom-control-input" name="header-position" id="header-position">
                            <label class="custom-control-label" for="header-position">Fixed Header</label>
                        </div>
                        <div class="custom-control custom-checkbox m-t-10">
                            <input type="checkbox" class="custom-control-input" name="boxed-layout" id="boxed-layout">
                            <label class="custom-control-label" for="boxed-layout">Boxed Layout</label>
                        </div>
                    </div>
                    <div class="p-15 border-bottom">
                        
                        <h5 class="font-medium m-b-10 m-t-10">Logo Backgrounds</h5>
                        <ul class="theme-color">
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-logobg="skin1"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-logobg="skin2"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-logobg="skin3"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-logobg="skin4"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-logobg="skin5"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-logobg="skin6"></a></li>
                        </ul>
                        
                    </div>
                    <div class="p-15 border-bottom">
                        
                        <h5 class="font-medium m-b-10 m-t-10">Navbar Backgrounds</h5>
                        <ul class="theme-color">
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-navbarbg="skin1"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-navbarbg="skin2"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-navbarbg="skin3"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-navbarbg="skin4"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-navbarbg="skin5"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-navbarbg="skin6"></a></li>
                        </ul>
                        
                    </div>
                    <div class="p-15 border-bottom">
                        
                        <h5 class="font-medium m-b-10 m-t-10">Sidebar Backgrounds</h5>
                        <ul class="theme-color">
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin1"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin2"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin3"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin4"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin5"></a></li>
                            <li class="theme-item"><a href="javascript:void(0)" class="theme-link" data-sidebarbg="skin6"></a></li>
                        </ul>
                        
                    </div>
                </div>
                
                
                <div class="tab-pane fade" id="chat" role="tabpanel" aria-labelledby="pills-profile-tab">
                    <ul class="mailbox list-style-none m-t-20">
                        <li>
                            <div class="message-center chat-scroll">
                                <a href="javascript:void(0)" class="message-item" id='chat_user_1' data-user-id='1'>
                                    <span class="user-img"> <img src="/images/users/1.jpg" alt="user" class="rounded-circle"> <span class="profile-status online pull-right"></span> </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Pavan kumar</h5> <span class="mail-desc">Just see the my admin!</span> <span class="time">9:30 AM</span> </div>
                                </a>
                                
                                <a href="javascript:void(0)" class="message-item" id='chat_user_2' data-user-id='2'>
                                    <span class="user-img"> <img src="/images/users/2.jpg" alt="user" class="rounded-circle"> <span class="profile-status busy pull-right"></span> </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Sonu Nigam</h5> <span class="mail-desc">I've sung a song! See you at</span> <span class="time">9:10 AM</span> </div>
                                </a>
                                
                                <a href="javascript:void(0)" class="message-item" id='chat_user_3' data-user-id='3'>
                                    <span class="user-img"> <img src="/images/users/3.jpg" alt="user" class="rounded-circle"> <span class="profile-status away pull-right"></span> </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Arijit Sinh</h5> <span class="mail-desc">I am a singer!</span> <span class="time">9:08 AM</span> </div>
                                </a>
                                
                                <a href="javascript:void(0)" class="message-item" id='chat_user_4' data-user-id='4'>
                                    <span class="user-img"> <img src="/images/users/4.jpg" alt="user" class="rounded-circle"> <span class="profile-status offline pull-right"></span> </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Nirav Joshi</h5> <span class="mail-desc">Just see the my admin!</span> <span class="time">9:02 AM</span> </div>
                                </a>
                                
                                
                                <a href="javascript:void(0)" class="message-item" id='chat_user_5' data-user-id='5'>
                                    <span class="user-img"> <img src="/images/users/5.jpg" alt="user" class="rounded-circle"> <span class="profile-status offline pull-right"></span> </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Sunil Joshi</h5> <span class="mail-desc">Just see the my admin!</span> <span class="time">9:02 AM</span> </div>
                                </a>
                                
                                
                                <a href="javascript:void(0)" class="message-item" id='chat_user_6' data-user-id='6'>
                                    <span class="user-img"> <img src="/images/users/6.jpg" alt="user" class="rounded-circle"> <span class="profile-status offline pull-right"></span> </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Akshay Kumar</h5> <span class="mail-desc">Just see the my admin!</span> <span class="time">9:02 AM</span> </div>
                                </a>
                                
                                
                                <a href="javascript:void(0)" class="message-item" id='chat_user_7' data-user-id='7'>
                                    <span class="user-img"> <img src="/images/users/7.jpg" alt="user" class="rounded-circle"> <span class="profile-status offline pull-right"></span> </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Pavan kumar</h5> <span class="mail-desc">Just see the my admin!</span> <span class="time">9:02 AM</span> </div>
                                </a>
                                
                                
                                <a href="javascript:void(0)" class="message-item" id='chat_user_8' data-user-id='8'>
                                    <span class="user-img"> <img src="/images/users/8.jpg" alt="user" class="rounded-circle"> <span class="profile-status offline pull-right"></span> </span>
                                    <div class="mail-contnet">
                                        <h5 class="message-title">Varun Dhavan</h5> <span class="mail-desc">Just see the my admin!</span> <span class="time">9:02 AM</span> </div>
                                </a>
                                
                            </div>
                        </li>
                    </ul>
                </div>
                
                
                <div class="tab-pane fade p-15" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                    <h6 class="m-t-20 m-b-20">Activity Timeline</h6>
                    <div class="steamline">
                        <div class="sl-item">
                            <div class="sl-left bg-success"> <i class="ti-user"></i></div>
                            <div class="sl-right">
                                <div class="font-medium">Meeting today <span class="sl-date"> 5pm</span></div>
                                <div class="desc">you can write anything </div>
                            </div>
                        </div>
                        <div class="sl-item">
                            <div class="sl-left bg-info"><i class="fas fa-image"></i></div>
                            <div class="sl-right">
                                <div class="font-medium">Send documents to Clark</div>
                                <div class="desc">Lorem Ipsum is simply </div>
                            </div>
                        </div>
                        <div class="sl-item">
                            <div class="sl-left"> <img class="rounded-circle" alt="user" src="/images/users/2.jpg"> </div>
                            <div class="sl-right">
                                <div class="font-medium">Go to the Doctor <span class="sl-date">5 minutes ago</span></div>
                                <div class="desc">Contrary to popular belief</div>
                            </div>
                        </div>
                        <div class="sl-item">
                            <div class="sl-left"> <img class="rounded-circle" alt="user" src="/images/users/1.jpg"> </div>
                            <div class="sl-right">
                                <div><a href="javascript:void(0)">Stephen</a> <span class="sl-date">5 minutes ago</span></div>
                                <div class="desc">Approve meeting with tiger</div>
                            </div>
                        </div>
                        <div class="sl-item">
                            <div class="sl-left bg-primary"> <i class="ti-user"></i></div>
                            <div class="sl-right">
                                <div class="font-medium">Meeting today <span class="sl-date"> 5pm</span></div>
                                <div class="desc">you can write anything </div>
                            </div>
                        </div>
                        <div class="sl-item">
                            <div class="sl-left bg-info"><i class="fas fa-image"></i></div>
                            <div class="sl-right">
                                <div class="font-medium">Send documents to Clark</div>
                                <div class="desc">Lorem Ipsum is simply </div>
                            </div>
                        </div>
                        <div class="sl-item">
                            <div class="sl-left"> <img class="rounded-circle" alt="user" src="/images/users/4.jpg"> </div>
                            <div class="sl-right">
                                <div class="font-medium">Go to the Doctor <span class="sl-date">5 minutes ago</span></div>
                                <div class="desc">Contrary to popular belief</div>
                            </div>
                        </div>
                        <div class="sl-item">
                            <div class="sl-left"> <img class="rounded-circle" alt="user" src="/images/users/6.jpg"> </div>
                            <div class="sl-right">
                                <div><a href="javascript:void(0)">Stephen</a> <span class="sl-date">5 minutes ago</span></div>
                                <div class="desc">Approve meeting with tiger</div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </aside> -->
    
    <script type="text/javascript" src ="{{asset('/js/libs/jquery.min.js')}}"></script>
    <script type="text/javascript" src ="{{asset('/js/libs/popper.min.js')}}"></script>
    <script type="text/javascript" src ="{{asset('/js/libs/bootstrap.min.js')}}"></script>
    
    <!-- SI VA -->
    <script type="text/javascript" src ="{{asset('/js/libs/perfect-scrollbar.jquery.min.js')}}"></script>
    <script type="text/javascript" src ="{{asset('/js/assets/template/app.min.js')}}"></script>


    <!--<script type="text/javascript" src ="{{asset('/js/libs/sparkline.js')}}"></script>
    <script type="text/javascript" src ="{{asset('/js/assets/template/app-style-switcher.js')}}"></script>
    <script type="text/javascript" src ="{{asset('/js/assets/template/sidebarmenu.js')}}"></script> -->
    <!-- <script type="text/javascript" src ="{{asset('/js/assets/template/custom.min.js')}}"></script>-->

    <!-- SI VA  -->
    <script type="text/javascript" src ="{{asset('/js/assets/template/app.init.light-sidebar.js')}}"></script>
     
    
    <!-- <script type="text/javascript" src="{{asset('/js/assets/vendors.min.js')}}"></script> -->
    <!-- <script type="text/javascript" src="{{asset('/js/assets/ui/jquery.sticky.js')}}"></script> SI SE COLOCA SE NOTA FEO-->
     
    <!-- <script type="text/javascript" src="{{asset('/js/assets/vendors/js/forms/icheck/icheck.min.js')}}"></script> -->
  
    <!-- <script type="text/javascript" src="{{asset('/js/assets/core/app-menu.min.js')}}"></script> -->
    
    <!-- <script type="text/javascript" src="{{asset('/js/assets/charts/chartist.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/js/assets/charts/chartist-plugin-tooltip.min.js')}}"></script> -->

    <!-- <script type="text/javascript" src="{{asset('/js/assets/charts/raphael-min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/js/assets/charts/morris.min.js')}}"></script> -->
 
    <!-- {{-- <script type="text/javascript" src="{{asset('/js/assets/timeline/horizontal-timeline.js')}}"></script> --}} -->

    <!-- <script type="text/javascript" src="{{asset('/js/assets/core/app-menu.min.js')}}"></script>
    <script type="text/javascript" id="script-reload" src="{{asset('/js/assets/core/app.min.js')}}"></script> -->

    <!-- <script type="text/javascript" src="{{asset('/js/assets/scripts/customizer.min.js')}}"></script> -->
    <!-- <script type="text/javascript" src="{{asset('/js/assets/scripts/footer.min.js')}}"></script> -->
    <!-- <script type="text/javascript" src="{{asset('/js/assets/scripts/pages/dashboard-ecommerce.min.js')}}"></script> -->
     
    <script type="text/javascript" src="{{asset('/js/assets/scripts/sweetalert2/dist/sweetalert2.all.min.js')}}"></script>
    
    <script type="text/javascript" src="{{asset('/js/init.js')}}"></script> 
   
    <!-- <script type="text/javascript" src="{{asset('/js/assets/vendors/js/forms/select/selectivity-full.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/js/assets/scripts/forms/select/form-selectivity.min.js')}}"></script> -->
 
    <!-- <script type="text/javascript" src="{{asset('/js/assets/scripts/forms/checkbox-radio.min.js')}}"></script> -->

</body>
</html>