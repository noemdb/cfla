@extends('admin.layouts.dashboard.app')

{{--
@section('page_heading')
    <div class="col-lg-12">
        <h1 class="page-header">
            Gráficas
        </h1>
    </div>
@endsection
--}}

@section('section')

    {{-- INI section--}}

    @component('elements.widgets.panel')
        @slot('panelTitle', 'Análisis Estadístico')
        @slot('class', 'info')
        @slot('panelBody')

            {{-- INI pill Chart --}}
            <div class="row">
                <div class="col-sm-12">

                    {{-- INI Menu pill --}}
                    <ul class="nav nav-tabs">
                      <li class="active"><a data-toggle="tab" href="#tabchart1">Gráficas 1</a></li>
                      {{-- <li><a data-toggle="tab" href="#tabchart2">Gráficas 2</a></li> --}}
                    </ul>
                    {{-- FIN Menu pill --}}

                    {{-- INI Content pill --}}
                    <div class="tab-content">

                        <div id="tabchart1" class="tab-pane fade in active">
                            {{-- <h3>Tab 1</h3> --}}

                            {{-- INI row chart1 --}}
                            <div class="row">

                                {{-- INI chart perfiles registrados por Mes --}}
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    @php ($chart = ['range'=>'Todos','id_chart'=>'charttasksmonth','urlapi'=>route('tasksmonth'),'tipo'=>'line','limit'=>8 ])
                                    @section('scripts')
                                        @parent
                                        {{-- Llamado a la funcion responsable de inicilizar el Chart --}}
                                        <script> requestData('{{ $chart['range'] }}','{{ $chart['id_chart'] }}','{{ $chart['urlapi'] }}','{{ $chart['tipo'] }}','{{ $chart['limit'] }}'); </script>
                                    @endsection
                                    @component('elements.widgets.panel')
                                        @slot('class', 'danger')
                                        @slot('panelConttasks', 'true')
                                        @slot('id', $chart['id_chart'] )
                                        @slot('panelTitle', 'N. Settings registrados por Mes')
                                        @slot('iconTitle', 'fa fa-line-chart fa-lg')
                                        @slot('panelBody')
                                            @component('elements.charts.widgets.canvas')
                                                @slot('class', 'borderRBL')
                                                @slot('ulpanel')
                                                    <ul class="nav nav-tabs ranges" data-canvas="{{ $chart['id_chart'] }}" data-urlapi="{{ $chart['urlapi'] }}" data-tipo="{{ $chart['tipo'] }}" data-limit="{{ $chart['limit'] }}">
                                                        <li class="active" title="Todo los datos"><a href="#" data-range='Todos'>Todos</a></li>
                                                        <li title="12 Meses"><a href="#" data-range='12'>12M</a></li>
                                                        <li title="9 Meses"><a href="#" data-range='9'>9M</a></li>
                                                        <li title="6 Meses"><a href="#" data-range='6'>6M</a></li>
                                                        <li title="3 Meses"><a href="#" data-range='3'>3M</a></li>
                                                    </ul>
                                                @endslot
                                                @slot('id', $chart['id_chart'])
                                            @endcomponent
                                        @endslot
                                    @endcomponent
                                </div>
                                {{-- FIN chart perfiles registrados por Mes --}}

                                {{-- INI chart Tipos de setting registrados --}}
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    @php ($chart = ['range'=>'Todos','id_chart'=>'chartTareastipos','urlapi'=>route('Tareastipos'),'tipo'=>'bar','limit'=>8 ])
                                    @section('scripts')
                                        @parent
                                        {{-- Llamado a la funcion responsable de inicilizar el Chart --}}
                                        <script> requestData('{{ $chart['range'] }}','{{ $chart['id_chart'] }}','{{ $chart['urlapi'] }}','{{ $chart['tipo'] }}','{{ $chart['limit'] }}'); </script>
                                    @endsection
                                    @component('elements.widgets.panel')
                                        @slot('class', 'success')
                                        @slot('panelConttasks', 'true')
                                        @slot('id', $chart['id_chart'] )
                                        @slot('panelTitle', 'Tipos de Settings')
                                        @slot('iconTitle', 'fa fa-bar-chart fa-lg')
                                        @slot('panelBody')
                                            @component('elements.charts.widgets.canvas')
                                                @slot('class', 'borderRBL')
                                                @slot('ulpanel')
                                                    <ul class="nav nav-tabs ranges" data-canvas="{{ $chart['id_chart'] }}" data-urlapi="{{ $chart['urlapi'] }}" data-tipo="{{ $chart['tipo'] }}" data-limit="{{ $chart['limit'] }}">
                                                        <li class="active" title="Todo los datos"><a href="#" data-range='Todos'>Todos</a></li>
                                                        <li title="12 Meses"><a href="#" data-range='12'>12M</a></li>
                                                        <li title="9 Meses"><a href="#" data-range='9'>9M</a></li>
                                                        <li title="6 Meses"><a href="#" data-range='6'>6M</a></li>
                                                        <li title="3 Meses"><a href="#" data-range='3'>3M</a></li>
                                                    </ul>
                                                @endslot
                                                @slot('id', $chart['id_chart'])
                                            @endcomponent
                                        @endslot
                                    @endcomponent
                                </div>
                                {{-- FIN chart Tipos de setting registrados --}}

                            </div>
                            {{-- FIN row chart1 --}}

                            <div class="row">
                                {{-- INI chart Tipos de ragos registrados --}}
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    @php ($chart = ['range'=>'Todos','id_chart'=>'chartrangostipos','urlapi'=>route('rangostipos'),'tipo'=>'bar','limit'=>8 ])
                                    @section('scripts')
                                        @parent
                                        {{-- Llamado a la funcion responsable de inicilizar el Chart --}}
                                        <script> requestData('{{ $chart['range'] }}','{{ $chart['id_chart'] }}','{{ $chart['urlapi'] }}','{{ $chart['tipo'] }}','{{ $chart['limit'] }}'); </script>
                                    @endsection
                                    @component('elements.widgets.panel')
                                        @slot('class', 'info')
                                        @slot('panelConttasks', 'true')
                                        @slot('id', $chart['id_chart'] )
                                        @slot('panelTitle', 'Tipos de Rangos')
                                        @slot('iconTitle', 'fa fa-bar-chart fa-lg')
                                        @slot('panelBody')
                                            @component('elements.charts.widgets.canvas')
                                                @slot('class', 'borderRBL')
                                                @slot('ulpanel')
                                                    <ul class="nav nav-tabs ranges" data-canvas="{{ $chart['id_chart'] }}" data-urlapi="{{ $chart['urlapi'] }}" data-tipo="{{ $chart['tipo'] }}" data-limit="{{ $chart['limit'] }}">
                                                        <li class="active" title="Todo los datos"><a href="#" data-range='Todos'>Todos</a></li>
                                                        <li title="12 Meses"><a href="#" data-range='12'>12M</a></li>
                                                        <li title="9 Meses"><a href="#" data-range='9'>9M</a></li>
                                                        <li title="6 Meses"><a href="#" data-range='6'>6M</a></li>
                                                        <li title="3 Meses"><a href="#" data-range='3'>3M</a></li>
                                                    </ul>
                                                @endslot
                                                @slot('id', $chart['id_chart'])
                                            @endcomponent
                                        @endslot
                                    @endcomponent
                                </div>
                                {{-- FIN chart Tipos de setting registrados --}}

                                <div class="col-lg-6 col-md-6 col-sm-12"></div>

                            </div>

                        </div>

                        {{-- <div id="tabchart2" class="tab-pane fade"> --}}
                            {{-- <h3>Tab 1</h3> --}}

                            {{-- INI row chart2 --}}
                            {{-- <div class="row"> --}}

                            {{-- </div> --}}
                            {{-- FIN row chart2 --}}

                        {{-- </div> --}}

                    </div>
                </div>
            </div>
            {{-- FIN pill Chart --}}

        @endslot
    @endcomponent

    {{-- FIN section--}}

@endsection

@section('stylesheet')

    {{-- <link rel="stylesheet" href="{{ asset('css/timeline.css') }}"> --}}

@endsection

@section('scripts')
    @parent
    <script src="{{ asset("js/Chart.js") }}"></script>
    {{-- <script src="{{ asset("js/utils.js") }}"></script> --}}
    <script src="{{ asset("js/ChartFunction.js") }}"></script>{{-- Funciones para generar los Chart --}}

    {{-- INI funciones para generar los Chart --}}
    <script>

        //Evento clic para el panel de tab nav-tabs (menu con las opciones)
        $('ul.ranges a').click(function(e){
            e.preventDefault();
            // Get the number of range from the data attribute
            var el = $(this);
            var range = $(this).data('range'); //setting(range);
            var ul = $(this).parents('ul');
            var canvas = ul.data('canvas'); //setting(canvas);
            var api = ul.data('urlapi'); //setting(urlapi);
            var tipo = ul.data('tipo'); //setting(tipo);
            var limit = ul.data('limit'); //setting(limit);

            // Request the data and render the chart using our handy function
            requestData(range,canvas,api,tipo,limit);
            // Make things pretty to show which button/tab the user clicked
            el.parent().addClass('active');
            el.parent().siblings().removeClass('active');

        });

    </script>
    {{-- FIN funciones para generar los Chart --}}

@endsection
