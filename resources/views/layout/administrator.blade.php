<!DOCTYPE html>
<html lang="en" dir="">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />

    <title>SIM Notaris PPAT</title>

    <link rel="apple-touch-icon" href="favicon.ico">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}" />
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet" />

    <!-- Assest Lib -->
    <link href="{{ asset('assets/css/plugins/datatables.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/plugins/toastr.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/plugins/sweetalert2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/plugins/select2.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/plugins/select2-bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/plugins/perfect-scrollbar.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/fonts/fontawesome/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('assets/css/themes/lite-purple.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/summernote/summernote.min.css') }}">

    <!-- Custom CSS -->
    <link href="{{ asset('assets/css/customize/custom.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/customize/responsive.css') }}" rel="stylesheet" />

    <style>
        .layout-horizontal-bar .main-header .logo img {
            width: 75px;
            height: 50px;
        }

        .topnav ul.menu li.active>div>div {
            border-bottom: 2px solid rebeccapurple !important;
        }

        .layout-horizontal-bar .main-content-wrap {
            min-height: calc(100vh - 140px);
        }

        .datepicker-dropdown {
            padding: 0.5rem;
        }
    </style>

    @yield('css')
</head>

<body class="text-left">
    <div class="app-admin-wrap layout-horizontal-bar">

        <!-- Navbar -->
        <div class="main-header">
            <div class="logo">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('assets/images/logo-simnotaris.png') }}" alt="">
                </a>
            </div>
            <div class="menu-toggle">
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div class="d-flex align-items-center">
                <h3 class="m-0 d-none d-md-block">{{ env('WEB_NAME') }}</h3>
            </div>
            <div style="margin: auto"></div>
            <div class="header-part-right">
                <p class="mt-3 mr-2 text-uppercase">{{ System::getProfile('group_nama', session()->get('uid')) }}</p>
                <i class="i-Full-Screen header-icon d-none d-sm-inline-block" data-fullscreen></i>
                <div class="dropdown">
                    <div class="user col align-self-end">
                        <img src="{{ asset('uploads/profile') }}/{{ System::getProfile('image', session()->get('uid')) ?: 'no-image.png' }}"
                            id="userDropdown" alt="" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                            <div class="dropdown-header">
                                <i class="i-Lock-User mr-1"></i>
                                {{ System::getProfile('nama', session()->get('uid')) }}
                            </div>
                            <a href="{{ route('profile') }}" class="dropdown-item">Profile</a>
                            <a class="dropdown-item" href="{{ route('logout') }}">Keluar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Navbar -->

        <!-- header top menu end-->
        @php
            $sidebars = System::getAccessibleSidebars();
        @endphp
        <div class="horizontal-bar-wrap">
            <div class="header-topnav">
                <div class="container-fluid">
                    <div class="topnav rtl-ps-none justify-content-center" id="" data-perfect-scrollbar=""
                        data-suppress-scroll-x="true">
                        <ul class="menu float-left">
                            <li id="dashboard">
                                <div>
                                    <div>
                                        <a href="{{ route('dashboard') }}">
                                            <i class="nav-icon mr-2 fas fa-house-damage"></i>
                                            Dashboard
                                        </a>
                                    </div>
                                </div>
                            </li>

                            @foreach ($sidebars as $sidebarsKey => $sidebar)
                                @if (isset($sidebar['childs']) && isset($sidebar['sidebar_kode']))
                                    <li id="{{ $sidebar['sidebar_kode'] }}">
                                        <div>
                                            <div>
                                                <a href="javascript:;">
                                                    <i class="nav-icon mr-2 {{ $sidebar['sidebar_icon'] }}"></i>
                                                    {{ $sidebar['sidebar_nama'] }}
                                                </a>
                                                <ul>
                                                    @foreach ($sidebar['childs'] as $childKey => $child)
                                                        <li class="nav-item" id="{{ $child['sidebar_kode'] }}"
                                                            data-parent="{{ $sidebar['sidebar_kode'] }}">
                                                            <a href="{{ url($child['sidebar_route']) }}"
                                                                title="{{ $child['sidebar_nama'] }}">
                                                                <i
                                                                    class="nav-icon mr-2 {{ $child['sidebar_icon'] }}"></i>
                                                                <span
                                                                    class="item-name">{{ $child['sidebar_nama'] }}</span>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                @elseif(isset($sidebar['sidebar_kode']))
                                    <li id="{{ $sidebar['sidebar_kode'] }}">
                                        <div>
                                            <div>
                                                <a href="{{ url($sidebar['sidebar_route']) }}">
                                                    <i class="nav-icon mr-2 {{ $sidebar['sidebar_icon'] }}"></i>
                                                    {{ $sidebar['sidebar_nama'] }}
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                @else
                                    {{-- code in here --}}
                                @endif
                            @endforeach

                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="main-content-wrap d-flex flex-column">
            <!-- Content -->
            <div class="main-content">
                @yield('content')
            </div>
            <!-- End Content -->

            <!-- Footer -->
            <div class="flex-grow-1"></div>
            <div class="app-footer">
                <div class="footer-bottom d-flex flex-column flex-sm-row align-items-center mt-2">
                    <h5><i class="fas fa-th-large mr-2"></i>{{ env('WEB_NAME') }}</h5>
                    <span class="flex-grow-1"></span>
                    <div class="d-flex align-items-center">
                        <img class="logo" src="{{ asset('assets/images/logo.png') }}" alt="">
                        <div class="ml-2">
                            <p class="m-0">&copy; Copyright Blitaris Tekno</p>
                            <p class="m-0 font-weight-bold">All rights reserved</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Footer -->
        </div>
    </div>

    <script type="text/javascript" src="{{ asset('assets/js/plugins/jquery-3.3.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/plugins/bootstrap.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/plugins/datatables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/plugins/loadingoverlay.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/plugins/select2.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/plugins/toastr.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/plugins/sweetalert2.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/daterangepicker/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script type="text/javascript"
        src="{{ asset('assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>

    <script type="text/javascript" src="{{ asset('assets/js/scripts/script.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/scripts/sidebar-horizontal.script.min.js') }}"></script>

    <script type="text/javascript" src="{{ asset('assets/plugins/summernote/summernote.js') }}"></script>


    <!-- Initialize Custom JS -->
    <script type="text/javascript">
        $.fn.datepicker.defaults.autoclose = true;
        $.fn.datepicker.defaults.orientation = "bottom";
        $.fn.datepicker.defaults.format = 'dd/mm/yyyy';
        $.fn.select2.defaults.set("theme", "bootstrap4");

        function baseUrl(url, prefix = "/administrator") {
            return "{{ url('/') }}" + prefix + url;
        }

        function assetsUrl(url) {
            return "{{ asset('/') }}" + url;
        }

        function redirect(url) {
            document.location.href = "{{ url('/') }}" + url;
        }

        // Menu Active
        $(document).ready(function() {
            let url = window.location.href;
            url = url.replace(/(^\w+:|^)\/\//, '');

            const segment = url.split('/')[2];
            const parent = $(`.topnav ul.menu li#${segment}`).data('parent');

            $(`.topnav ul.menu li#${!parent?segment:parent}`).addClass('active');
        });
    </script>
    <script src="{{ asset('assets/scripts/utils/script.js') }}"></script>

    @yield('javascript')

    @stack('scripts')
</body>

</html>
