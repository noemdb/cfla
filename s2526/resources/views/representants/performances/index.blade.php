@extends('representants.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-info">

                <h3 class="mb-0 pb-0">
                    <i class="{{$icon_menus['control_estudio'] ?? ''}} text-info" aria-hidden="true"></i>
                    Indicadores de <span class=" font-weight-bolder">Control de Estudio</span>
                </h3>
            </div>

            <div class="card-body p-1 m-1">

                <div class="card">
                    <div class="card-header alert-primary font-weight-bolder" role="alert">
                        RENDIMIENTO ESTUDIANTIL POR PLANES DE ESTUDIO
                    </div>
                    <div class="card-body">
                        @foreach ($indicadores as $indicador)

                            <div class=" border rounded pb-2 mb-2">

                                <div class=" font-weight-bolder alert alert-secondary">
                                    <span> {{$indicador['name'] ?? ''}} </span>
                                </div>

                                <div class="row pb-2">
                                    <div class="col mx-2">
                                        @component('representants.elements.boxes.chart')
                                            @slot('title','PROMEDIO GENERAL')
                                            @slot('class','primary')
                                            @slot('total',(!empty($indicador['promedio'])) ? $indicador['promedio']:null )
                                            @slot('unidad','ptos')
                                            @slot('subtitle',(!empty($indicador['escala_name'])) ? 'Escala de puntuación: '.$indicador['escala_name']:null )
                                            @slot('class_total','font-size-lg')
                                        @endcomponent
                                    </div>
                                    <div class="col mx-2">
                                        @component('representants.elements.boxes.chart')
                                            @slot('title','INDICE DE APROBADOS')
                                            @slot('class','success')
                                            @slot('total',(!empty($indicador['porc_aprobados'])) ? $indicador['porc_aprobados']:null )
                                            @slot('unidad','%')
                                            @slot('bars','true')
                                            @slot('width',(!empty($indicador['porc_aprobados'])) ? $indicador['porc_aprobados']:null )
                                            @slot('class_total','font-size-md')
                                        @endcomponent
                                    </div>
                                    <div class="col mx-2">
                                        @component('representants.elements.boxes.chart')
                                            @slot('title','PLANES DE EVALUACION ASIGNADOS')
                                            @slot('class','primary')
                                            @slot('icon',$icon_menus['pevaluacion'])
                                            @slot('total',(!empty($indicador['porc_pevaluacions'])) ? $indicador['porc_pevaluacions']:null )
                                            @slot('unidad','%')
                                            @slot('bars','true')
                                            @slot('width',(!empty($indicador['porc_pevaluacions'])) ? $indicador['porc_pevaluacions']:null)
                                            @slot('class_total','font-size-lg')
                                        @endcomponent
                                    </div>
                                    <div class="col mx-2">
                                        @php $act_promedio = (!empty($indicador['count_evaluacions'])) ? round( (100 * ($profesors->count() / $indicador['count_evaluacions'])),0 ):null; @endphp
                                        @component('representants.elements.boxes.chart')
                                            @slot('title','NÚMERO DE EVALUACIONES REGISTRADAS')
                                            @slot('subtitle', $act_promedio.' evaluaciones en promedio  por profesor')
                                            @slot('class','info')
                                            @slot('icon',$icon_menus['evaluacion'])
                                            @slot('total',(!empty($indicador['count_evaluacions'])) ? $indicador['count_evaluacions']:null )
                                            @slot('class_total','font-size-md')
                                        @endcomponent
                                    </div>
                                    <div class="col mx-2">
                                        @component('representants.elements.boxes.chart')
                                            @slot('title','PORCENTAJE DE NOTAS REGISTRADAS')
                                            @slot('class','secondary')
                                            @slot('icon',$icon_menus['notas'])
                                            @slot('total',(!empty($indicador['porc_notas_load'])) ? $indicador['porc_notas_load']:null )
                                            @slot('unidad','%')
                                            @slot('bars','true')
                                            @slot('width',(!empty($indicador['porc_notas_load'])) ? $indicador['porc_notas_load']:null )
                                            @slot('class_total','font-size-md')
                                        @endcomponent
                                    </div>
                                </div>

                            </div>

                        @endforeach
                    </div>
                </div>

                <hr>

                <div class="card">
                    <div class="card-header alert-primary font-weight-bolder" role="alert">
                        RENDIMIENTO ESTUDIANTIL POR AREAS DE CONOCIMIENTOS
                        <span class=" float-right font-weight-normal">
                            {{-- modal --}}
                            @include('administracion.configuraciones.area_conocimientos.modals.legenda')
                        </span>
                    </div>
                    <div class="card-body p-3">
                        <div class="row p-1">
                            <div class="col-sm-12">
                                <div class=" border rounded">

                                    <div class=" font-weight-bolder alert-secondary p-1">
                                        <i class="{{$icon_menus['pestudio'] ?? ''}}" aria-hidden="true"></i>
                                        Promedio de notas por Área de Conocimientos
                                        {{-- {{ $pestudio->id }} {{ $pestudio->name ?? '' }} --}}
                                    </div>

                                    <div class="container-fluid">
                                        <div class="row">
                                            @foreach ($pestudios as $pestudio)

                                                <div class="col-6">

                                                    @include('administracion.configuraciones.area_conocimientos.chart.promedio_x_area')

                                                </div>

                                            @endforeach
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

@endsection

