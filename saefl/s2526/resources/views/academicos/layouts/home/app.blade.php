@extends('academicos.layouts.home')

@section('body')

    {{-- INI navbar top --}}

    @if (env('APP_NAME')=="SAEFL")
        @php ($color = "#004000")
    @endif
    @if (env('APP_NAME') =="SAEFL.DEV")
        @php ($color = "#FF0000")
    @endif

    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 navbar-expand-md" style="background-color:{{Session::get('pescolar_color') ?? '#004000'}} !important">

        @include('academicos.layouts.dashboard.navbar.app')

    </nav>

    {{-- FIN navbar top --}}

    {{-- INI page-wrappe --}}
    <div class="container-fluid">

        <div class="row">
            <div class="row">
                <div class="col-sm-2">
                    @includeif('academicos.card.director')
                </div>
                <div class="col-sm-10">
                    @yield('main')
                </div>
            </div>
        </div>

    </div>

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
