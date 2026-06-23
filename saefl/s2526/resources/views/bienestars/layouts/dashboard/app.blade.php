@extends('bienestars.layouts.app')

@section('body')

    {{-- INI navbar top --}}

    {{-- <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 navbar-expand-md" style="background-color:#004000 !important"> --}}
    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 navbar-expand-md" style="background-color:{{env('APP_NAV_COLOR','#004000')}} !important">

        @include('bienestars.layouts.dashboard.navbar.app')

    </nav>
    {{-- FIN navbar top --}}

    {{-- INI page-wrappe --}}
    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-12 col-md-2 h-100 d-md-block bg-light p-0 m-0 show  px-1" id="sidebar">
                @includeif('bienestars.card.user')
            </div>
            <div class="col-md-10 ml-sm-auto col-lg-10 px-1">
                @yield('main')
            </div>
        </div>

    </div>

@endsection

@section('footer')
    @include('bienestars.layouts.footer.dashboard')
@endsection


@section('stylesheet')
     @parent

     <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">

@endsection

@section('scripts')
     @parent

     <script src="{{ asset("js/accordion.js") }}"></script>

     <script type="text/javascript">
         $(document).ready(function () {
            $('#sidebar').collapse('hide');
            reloj();
         });
     </script>

    <script type="text/javascript">
        function reloj(){
            var d = new Date();
            var datestring = ("0" + d.getDate()).slice(-2) + "-" + ("0"+(d.getMonth()+1)).slice(-2) + "-" +d.getFullYear() + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2);
            document.getElementById('reloj').innerHTML=datestring;
            setTimeout('reloj()',60000);
        }
    </script>

@endsection

@section('scripts')
    @parent
    {{-- <script src="{{ asset("js/Chart.js") }}"></script> --}}
    <script src="{{ asset("js/Chart.bundle.js") }}"></script>
    {{-- <script src="{{ asset("js/utils.js") }}"></script> --}}
    <script src="{{ asset("js/ChartFunction.js") }}"></script>{{-- Funciones para generar los Chart --}}

    {{-- INI Evento clic para generar los Chart por rango--}}
    <script src="{{ asset("js/ChartEvent.js") }}"></script>{{-- Funciones para generar los Chart --}}
    {{-- FIN Evento clic para generar los Chart por rango --}}

@endsection

