@extends('proyectos.layouts.app')

@section('body')

    {{-- INI navbar top --}}

    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 navbar-expand-md" style="background-color:#0c5460 !important">

        @include('proyectos.layouts.dashboard.navbar.app')

    </nav>
    {{-- FIN navbar top --}}

    {{-- INI page-wrappe --}}
    <div class="container-fluid">

        @if (Request::is('*home*'))
            <div class="row">
                <div class="col-md-2 d-md-block p-0 m-0 ">
                    @includeif('proyectos.card.user')
                </div>
                <div class="col-md-10">
                    @yield('main')
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-12">
                    @yield('main')
                </div>
            </div>
        @endif



    </div>

@endsection

@section('footer')
    @include('proyectos.layouts.footer.dashboard')
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


