@extends('admin.layouts.app')

@section('body')

    {{-- @include('administracion.layouts.dashboard.navbar.app') --}}

    {{-- INI navbar --}}
    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 navbar-expand-md" style="background-color:#004000 !important">
    
        <a class="navbar-brand col-sm-3 col-md-2 m-0 p-0" href="{{ route('home') }}">
            <img class="{{-- mb-4 --}}" src="{{ asset('images/brand/48/1.png') }}" alt="" width="48" height="48">
            {{ config('app.name', 'Laravel') }}
        </a>

        {{-- <input class="form-control form-control-dark w-100" type="text" placeholder="Buscar" aria-label="Buscar"> --}}

        <ul class="navbar-nav px-3">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('home') }}">
                    <span data-feather="home"></span>
                    Inicio {{-- <span class="sr-only">(current)</span> --}}
                </a>
            </li>
            <li class="nav-item dropdown pl-1">
                @include('admin.layouts.dashboard.navbar.partials.sistema')
            </li>
            <li class="nav-item dropdown pl-1">
                @include('admin.layouts.dashboard.navbar.partials.administracion')
            </li>
            <li class="nav-item dropdown pl-1">
                @include('admin.layouts.dashboard.navbar.partials.cestudio')
            </li>
            <li class="nav-item dropdown pl-1">
                @include('admin.layouts.dashboard.navbar.partials.profesors')
            </li>
            <li class="nav-item dropdown pl-1">
                @include('admin.layouts.dashboard.navbar.partials.boletin')
            </li>
            <li class="nav-item dropdown pl-1">
                {{-- @include('admin.layouts.partials.navbar.common')      --}}
            </li>
            <li class="nav-item dropdown pl-1">
                @include('admin.layouts.dashboard.navbar.partials.user')
            </li>
        </ul>

        <button id="btnSidebarCollapse" class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

    </nav>
    {{-- FIN navbar --}}

    {{-- INI page-wrappe --}}
    <div class="container-fluid">

        <div class="row">

            {{-- INI sidebar --}}
            <nav id="sidebar" class="col-md-2 {{-- d-none --}} d-md-block bg-light p-0 m-0 show">

                @include('admin.layouts.dashboard.sidebar.app')

            </nav>

            @yield('main')

        </div>

    </div>

@endsection

@section('stylesheet')
     @parent

     <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">

     <link href="{{ asset('css/accordion.css') }}" rel="stylesheet">

     {{-- <link href="{{ asset('css/admin.css') }}" rel="stylesheet"> --}}

@endsection

@section('scripts')
     @parent

     <script src="{{ asset("js/accordion.js") }}"></script>

     <script type="text/javascript">
         $(document).ready(function () {
            $('#sidebar').collapse('hide');
         });
     </script>

@endsection