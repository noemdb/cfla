{{-- INI dashboard widget --}}
<div class="container">
    {{-- INI graphics --}}
    <div class="row">

        <div class="col-lg-6 col-md-12 col-sd-12 pl-0 p-1">

            @include('admin.users.chart.usersactive')

        </div>

        <div class="col-lg-6 col-md-12 col-sm-12 p-1">

            @include('admin.users.chart.usersmonths')

        </div>

        <div class="col-lg-6 col-md-12 col-sm-12">

            @include('admin.tasks.chart.tasksuserschart')

        </div>

        <div class="col-lg-6 col-md-12 col-sm-12">

            @include('admin.tasks.chart.tasksmonthchart')

        </div>

        <div class="col-lg-6 col-md-12 col-sm-12">

            @include('admin.alerts.chart.alertsuserschart')              

        </div>

        <div class="col-lg-6 col-md-12 col-sm-12">

            @include('admin.alerts.chart.alertsmonthchart')

        </div>

    </div>
    {{-- FIN graphics --}}
</div>
{{-- FIN dashboard widget --}}

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