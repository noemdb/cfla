@extends('layouts.app')

@section('body')
    {{-- INI navbar top --}}
    {{-- <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 navbar-expand-md" style="background-color:#004000 !important"> --}}
    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 navbar-expand-md"
        style="background-color:{{ Session::get('pescolar_color') ?? '#404000' }} !important">

        @include('administracion.layouts.dashboard.navbar.app')

    </nav>
    {{-- FIN navbar top --}}

    {{-- INI page-wrappe --}}
    <div class="container-fluid">

        <div class="row">

            {{-- INI sidebar --}}
            <nav id="sidebar" class="col-md-2 {{-- d-none --}} d-md-block bg-light p-0 m-0 show"
                style="background-color:#D1FED1 !important">

                @include('administracion.layouts.dashboard.sidebar.app')

            </nav>

            @yield('main')

        </div>

    </div>
@endsection

@section('stylesheet')
    @parent

    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">

    {{-- <link href="{{ asset('css/admin.css') }}" rel="stylesheet"> --}}
@endsection

@section('scripts')
    @parent

    <script src="{{ asset('js/accordion.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#sidebar').collapse('hide');
        });
    </script>
@endsection
