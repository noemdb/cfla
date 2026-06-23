@extends('administracion.layouts.app')

@section('body')
    {{-- INI navbar top --}}

    <!-- <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 navbar-expand-md" style="background-color:{{ Session::get('pescolar_color') ?? '#004000' }} !important"> -->
    <!-- <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 navbar-expand-md" style="background-color:{{ Session::get('pescolar_color') ?? '#004000' }} !important"> -->
    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 navbar-expand-md"
        style="background-color:{{ env('APP_NAV_COLOR', '#004000') }} !important">

        @include('administracion.layouts.dashboard.navbar.app')

    </nav>
    {{-- FIN navbar top --}}

    {{-- INI page-wrappe --}}
    <div class="container-fluid">

        <div class="row">

            {{-- INI sidebar --}}
            <!-- <nav id="sidebar" class="col-md-2 {{-- d-none --}} d-md-block bg-light p-0 m-0 show" style="background-color:{{ env('APP_SIDE_COLOR', '#D1FED1') }} !important"> -->
            <nav id="sidebar" class="col-md-2 {{-- d-none --}} d-md-block bg-light p-0 m-0 show"
                style="background-color:{{ env('APP_SIDE_COLOR', '#D1FED1') }} !important">

                @include('administracion.layouts.dashboard.sidebar.app')

            </nav>

            @yield('main')

        </div>

    </div>
@endsection

@section('footer')
    @include('administracion.layouts.footer.dashboard')
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
            reloj();
        });
    </script>

    <script type="text/javascript">
        function reloj() {
            var d = new Date();
            var datestring = ("0" + d.getDate()).slice(-2) + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + d
                .getFullYear() + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2);
            document.getElementById('reloj').innerHTML = datestring;
            setTimeout('reloj()', 60000);
        }
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $(".deck-card").hover(
                function() {
                    $(this).addClass('shadow');
                },
                function() {
                    $(this).removeClass('shadow');
                }
            );
        });
    </script>

    <script language=Javascript>
        $(document).ready(function() {
            $('input.float').on('input', function() {
                this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
            });
        });
    </script>
@endsection
