<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" xmlns="http://www.w3.org/1999/html">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ShopLister</title>
        <!-- Google Font: Source Sans Pro -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">
        <script src="https://kit.fontawesome.com/affc452592.js" crossorigin="anonymous"></script>
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <!-- Tempusdominus Bootstrap 4 -->
        <link rel="stylesheet" href="{{asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
        <!-- iCheck -->
        <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
        <!-- JQVMap -->
        <link rel="stylesheet" href="{{asset('plugins/jqvmap/jqvmap.min.css')}}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
        <!-- Daterange picker -->
        <link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <!-- jQuery -->
        <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
        <!-- jQuery UI 1.11.4 -->
        <script src="{{asset('plugins/jquery-ui/jquery-ui.min.js')}}"></script>
        <!-- Bootstrap 4 -->
        <script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <!-- ChartJS -->
        <script src="{{asset('plugins/chart.js/Chart.min.js')}}"></script>
        <!-- Sparkline -->
        <script src="{{asset('plugins/sparklines/sparkline.js')}}"></script>
        <!-- daterangepicker -->
        <script src="{{asset('plugins/moment/moment.min.js')}}"></script>
        <script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
        <!-- Tempusdominus Bootstrap 4 -->
        <script src="{{asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
        <!-- AdminLTE App -->
        <script src="{{asset('dist/js/adminlte.js')}}"></script>
        {{--CSS--}}
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        {{--JS--}}
        <script src="{{asset('js/main.js')}}"></script>
        @yield('header')
    </head>
    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="wrapper">
            @if(\Illuminate\Support\Facades\Auth::user())
            <!-- Navbar -->
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <!-- Sidebar expander -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                    </li>
                </ul>
                {{-- Shopping Cart Selection --}}
                <div class="float-right text-right">
                    @php
                        $auth_user = \Illuminate\Support\Facades\Auth::user();
                        $carts = \App\ShoppingCart::where('owner_id', $auth_user->id)->pluck('name', 'id');
                    @endphp
                    {{Form::select('active_cart', $carts, $_COOKIE['selected_cart_id'] ?? null, ['class' => 'form-control active-cart-dd'] )}}
                </div>
            </nav>

            <!-- Main Sidebar Container -->
            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <a href="{{route('home.index')}}" class="brand-link text-center">
                    <span class="brand-text font-weight-light">ShopLister</span>
                </a>
                <!-- Sidebar -->
                <div class="sidebar">
                    <!-- Sidebar user panel (optional) -->
                    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                        <div class="info">
                            <a href="#" class="d-block">{{Auth::user()->name ?? ''}}</a>
                        </div>
                    </div>
                    <!-- Sidebar Menu -->
                    <nav class="mt-2">
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                            @php
                                $auth_user =  \Illuminate\Support\Facades\Auth::user();
                                $perms = $auth_user->permissions->groupBy('menu_name')->map(function($item){
                                    return $item->keyBy('slug');
                                });
                            @endphp
                            @forelse(config('generate_menu') as $menu_item_name => $menu_item_data)
                                @if(isset($menu_item_data['show_all']) || isset($perms[$menu_item_name]) || $auth_user->can('superadmin'))
                                    <li class="nav-item">
                                        <a href="{{isset($menu_item_data['route']) ? route($menu_item_data['route']) : '#'}}" class="nav-link">
                                            <i class="nav-icon fas {{$menu_item_data['icon']}}"></i>
                                            <p>
                                                {{$menu_item_name}}
                                                @if($menu_item_data['type'] == 'group')
                                                    <i class="right fas fa-angle-left"></i>
                                                @endif
                                            </p>
                                        </a>
                                        @endif
                                        <!-- Generate menu sub-items if provided -->
                                        @if(isset($menu_item_data['list_items']))
                                            <ul class="nav nav-treeview">
                                                @foreach($menu_item_data['list_items'] as $submenu_item_name => $submenu_item_data)
                                                    @if(!isset($submenu_item_data['permission_required']) || $auth_user->can($submenu_item_data['permission_required']))
                                                        <li class="nav-item">
                                                            <a href="{{route($submenu_item_data['route'])}}" class="nav-link">
                                                                <p>{{$submenu_item_name}}</p>
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @empty
                                @endforelse
                        </ul>
                    </nav>
                </div>
            </aside>
            @endif
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <div class="content-header">
                    <div class="container-fluid">
                       @yield('content-header')
                    </div>
                </div>
                <!-- Main content -->
                <section class="content">
                    @include('layouts.add-product-modal')
                    <div class="container-fluid">
                        @if (\Session::has('success'))
                            <div class="alert alert-success">
                                <li>{!! \Session::get('success') !!}</li>
                            </div>
                        @endif
                        @if (\Session::has('error'))
                            <div class="alert alert-danger">
                                <li>{!! \Session::get('error') !!}</li>
                            </div>
                        @endif
                        @yield('content')
                    </div>
                </section>
            </div>
            <!-- /.content-wrapper -->
            <footer class="main-footer">
                <strong>ShopLister by Boris Vidakovic</a>.</strong>
                <div class="float-right d-none d-sm-inline-block">
                    <b>Version</b> 1.0
                </div>
            </footer>
        </div>
    </body>
</html>
