<div class="card-body">
    @foreach ($indicadores as $indicador)

    @php
        $evaluacions = $indicador['evaluacions'];
        $profesors = $indicador['profesors'];
        $pevaluacions = $indicador['pevaluacions'];
        $pensums = $indicador['pensums'];
    @endphp

        <div class=" border rounded pb-2 mb-2">

            <div class=" font-weight-bolder alert alert-secondary">
                <span> {{$indicador['name'] ?? ''}} </span>
            </div>

            <div class="row pb-2 text-center">
                <div class="col mx-2">
                    @component('controls.elements.boxes.indicators')
                        @slot('title','PROMEDIO GENERAL')
                        @slot('class','primary')
                        @slot('total',(!empty($indicador['promedio'])) ? $indicador['promedio']:null )
                        @slot('unidad',' pts')
                        @slot('subtitle',(!empty($indicador['escala_name'])) ? 'Escala de puntuación: '.$indicador['escala_name']:null )
                    @endcomponent
                </div>
                <div class="col mx-2">
                    @component('controls.elements.boxes.indicators')
                        @slot('title','INDICE DE APROBADOS')
                        @slot('class','info')
                        @slot('total',(!empty($indicador['porc_aprobados'])) ? $indicador['porc_aprobados']:null )
                        @slot('unidad','%')
                        @slot('bars','true')
                    @endcomponent
                </div>
                <div class="col mx-2">
                    @component('controls.elements.boxes.indicators')
                        @slot('title','PLANES DE EVALUACION ASIGNADOS')
                        @slot('subtitle', 'N. de Profesores: '.$profesors->count())
                        @slot('class','success')
                        @slot('icon',$icon_menus['pevaluacion'])
                        @slot('total',(!empty($indicador['porc_pevaluacions'])) ? $indicador['porc_pevaluacions']:null )
                        @slot('unidad','%')
                        @slot('bars','true')
                    @endcomponent
                </div>
                <div class="col mx-2">
                    @php $promedio = (!empty($pensums->count())) ? round( ( $indicador['count_evaluacions'] / $pensums->count()),0 ):null; @endphp
                    @component('controls.elements.boxes.indicators')
                        @slot('title','NÚMERO DE EVALUACIONES REGISTRADAS')
                        @slot('subtitle', 'En promedio '.$promedio.' evaluaciones por asignatura')
                        @slot('class','warning')
                        @slot('icon',$icon_menus['evaluacion'])
                        @slot('total',(!empty($indicador['count_evaluacions'])) ? $indicador['count_evaluacions']:null )
                    @endcomponent
                </div>
                <div class="col mx-2">
                    @component('controls.elements.boxes.indicators')
                        @slot('title','PORCENTAJE DE NOTAS REGISTRADAS')
                        @slot('class','danger')
                        @slot('icon',$icon_menus['notas'])
                        @slot('total',(!empty($indicador['porc_notas_load'])) ? $indicador['porc_notas_load']:null )
                        @slot('unidad','%')
                        @slot('bars','true')
                    @endcomponent
                </div>
            </div>

        </div>

    @endforeach
</div>
