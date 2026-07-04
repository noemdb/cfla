@extends('profesors.layouts.app')

@section('body')

    {{-- INI navbar top --}}

    @if (env('APP_NAME')=="SAEFL")
        @php ($color = "#004000")
    @endif
    @if (env('APP_NAME') =="SAEFL.DEV")
        @php ($color = "#FF0000")
    @endif

    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 navbar-expand-md" style="background-color:{{Session::get('pescolar_color') ?? '#004000'}} !important">

        @include('profesors.layouts.dashboard.navbar.app')

    </nav>
    {{-- FIN navbar top --}}

    {{-- INI page-wrappe --}}
    <div class="container-fluid">

        <div class="row">
            @if (! Request::is('*competitions*') )
            <div class="col-md-2 d-md-block p-0 m-0 ">
                <div class="sidebar-sticky">
                    @includeif('profesors.card.profesor')
                </div>
            </div>
            @endif            
            <div class="col">
                @yield('main')
            </div>
        </div>

    </div>

@endsection

@section('footer')
    @include('profesors.layouts.footer.dashboard')
@endsection

@section('stylesheet')
     @parent

     <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">

     {{-- <link href="{{ asset('css/admin.css') }}" rel="stylesheet"> --}}

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
            // var fecha= new Date();
            // var horas= fecha.getHours();
            // var minutos = fecha.getMinutes();
            // var segundos = fecha.getSeconds();
            var d = new Date();
            var datestring = ("0" + d.getDate()).slice(-2) + "-" + ("0"+(d.getMonth()+1)).slice(-2) + "-" +d.getFullYear() + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2);

            document.getElementById('reloj').innerHTML=datestring;
            // document.getElementById('reloj').innerHTML=''+horas+':'+minutos+':'+segundos+'';
            setTimeout('reloj()',60000);
            // setTimeout('reloj()',1000);
        }
    </script>

@endsection
