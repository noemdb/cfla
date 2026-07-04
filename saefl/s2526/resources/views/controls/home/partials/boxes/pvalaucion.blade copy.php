
@foreach ($indicadores as $indicador)
    <div class="border border-bottom-0 rounded-top">
        <span class="pl-1 pt-1"><b>{{$indicador['name'] ?? ''}}</b></span>
    </div>
    <div class="border border-top-0 rounded-bottom pb-2">
        <div class="row">
            <div class="col-sm-2 pr-0">
                @component('controls.elements.boxes.chart')
                    @slot('subtitle','PLANES DE EVALUACION ASIGNADOS')
                    @slot('class','primary')
                    @slot('icon',$icon_menus['pevaluacion'])
                    @slot('total',$indicador['count_pevaluacions'] )
                    @slot('class_total','font-size-lg')
                @endcomponent
            </div>
            <div class="col-sm-2 pr-0">
                @component('controls.elements.boxes.chart')
                    @slot('subtitle','NÚMERO DE EVALUACIONES REGISTRADAS')
                    @slot('class','info')
                    @slot('icon',$icon_menus['evaluacion'])
                    @slot('total', $indicador['count_evaluacions'] )
                    @slot('class_total','font-size-md')
                @endcomponent
            </div>
            <div class="col-sm-2 pr-0">
                @component('controls.elements.boxes.chart')
                    @slot('subtitle','PORCENTAJE DE NOTAS REGISTRADAS')
                    @slot('class','secondary')
                    @slot('icon',$icon_menus['notas'])
                    @slot('total',$indicador['porc_notas_load'].'%' )
                    @slot('class_total','font-size-md')
                @endcomponent
            </div>
            <div class="col-sm-2 pr-0" style="opacity: 0.3;">
                @component('controls.elements.boxes.chart')
                    @slot('subtitle','INDICE DE APLAZADOS')
                    @slot('class','danger')
                    @slot('total',$indicador['porc_aplazados'] )
                    @slot('class_total','font-size-lg')
                @endcomponent
            </div>
            <div class="col-sm-2 pr-0" style="opacity: 0.3;">
                @component('controls.elements.boxes.chart')
                    @slot('subtitle','INDICE DE APROBADOS')
                    @slot('class','success')
                    @slot('total',$indicador['porc_aprobados'] )
                    @slot('class_total','font-size-md')
                @endcomponent
            </div>
        </div>
    </div>
@endforeach



