<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-layout-style="default" data-topbar="light" data-sidebar="dark" data-sidebar-size="sm-hover" data-layout-width="fluid">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>UASD Carreras</title>

        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('velzon/assets/images/favicon.ico') }}">

        <!-- Layout config Js -->
        <script src="{{ asset('velzon/assets/js/layout.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        {{-- Velzon CSS --}}
        <link href="{{ asset('velzon/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('velzon/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('velzon/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('velzon/assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
        @stack('styles')
    </head>
    
    <body>
        <!-- Begin page -->
        <div id="layout-wrapper">
            
            {{-- Header / Topbar --}}
            @auth
                @include('layouts.partials.header')
            @endauth

            {{-- Sidebar --}}
            @auth
                @include('layouts.partials.sidebar')
            @endauth

            <!-- Vertical Overlay-->
            <div class="vertical-overlay"></div>

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">
                <div class="page-content">
                    <div class="container-fluid">
                        @isset($slot)
                            {{ $slot }}
                        @endisset
                        
                        @yield('content')
                    </div>
                </div>

                {{-- Footer --}}
                @include('layouts.partials.footer')
            </div>
            <!-- end main content-->

        </div>
        <!-- END layout-wrapper -->

        {{-- Velzon JS --}}
        <script src="{{ asset('velzon/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('velzon/assets/libs/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('velzon/assets/libs/node-waves/waves.min.js') }}"></script>
        <script src="{{ asset('velzon/assets/libs/feather-icons/feather.min.js') }}"></script>
        <script src="{{ asset('velzon/assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
        <script src="{{ asset('velzon/assets/js/plugins.js') }}"></script>
        <script src="{{ asset('velzon/assets/js/app.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script> 


        @stack('scripts')
    </body>
</html>