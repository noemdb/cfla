<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        @foreach($indicadores as $indicador)
            <a class="nav-item nav-link {{($loop->iteration==$lapso_active->id) ? 'active':''}}" id="nav-header-tab-pevaluacion-{{$indicador['id']}}" data-toggle="tab" href="#nav-content-pevaluacion-{{$indicador['id']}}" role="tab" aria-controls="nav-home" aria-selected="true">{{$indicador['name']}}</a>
        @endforeach
    </div>
</nav>

<div class="tab-content" id="nav-tabContent">
    @foreach ($indicadores as $indicador)
        <div class="tab-pane fade {{($loop->iteration==$lapso_active->id) ? 'show active':''}}" id="nav-content-pevaluacion-{{$indicador['id']}}" role="tabpanel" aria-labelledby="nav-header-home-tab-{{$indicador['id'] ?? ''}}">
            <div class="border border-top-0 rounded-bottom pb-2">
                <div class="row">
                    <div class="col-sm-2 pr-0">
                        @component('academicos.elements.boxes.chart')
                            @slot('subtitle','PLANES DE EVALUACION ASIGNADOS')
                            @slot('class','primary')
                            @slot('icon',$icon_menus['pevaluacion'])
                            @slot('total',$indicador['count_pevaluacions'] )
                            @slot('class_total','font-size-lg')
                        @endcomponent
                    </div>
                    <div class="col-sm-2 pr-0">
                        @component('academicos.elements.boxes.chart')
                            @slot('subtitle','NÚMERO DE EVALUACIONES REGISTRADAS')
                            @slot('class','info')
                            @slot('icon',$icon_menus['evaluacion'])
                            @slot('total', $indicador['count_evaluacions'] )
                            @slot('class_total','font-size-md')
                        @endcomponent
                    </div>
                    <div class="col-sm-2 pr-0">
                        @component('academicos.elements.boxes.chart')
                            @slot('subtitle','PORCENTAJE DE NOTAS REGISTRADAS')
                            @slot('class','secondary')
                            @slot('icon',$icon_menus['notas'])
                            @slot('total',$indicador['porc_notas_load'].'%' )
                            @slot('class_total','font-size-md')
                        @endcomponent
                    </div>
                    {{-- <div class="col-sm-2 pr-0" style="opacity: 0.3;">
                        @component('academicos.elements.boxes.chart')
                            @slot('subtitle','INDICE DE APLAZADOS')
                            @slot('class','danger')
                            @slot('total',$indicador['porc_aplazados'] )
                            @slot('class_total','font-size-lg')
                        @endcomponent
                    </div> --}}
                    <div class="col-sm-2 pr-0">
                        @component('academicos.elements.boxes.chart')
                            @slot('subtitle','PROMEDIO GENERAL')
                            @slot('class','danger')
                            @slot('total',$indicador['promedio'] )
                            @slot('class_total','font-size-lg')
                        @endcomponent
                    </div>
                    <div class="col-sm-2 pr-0">
                        @component('academicos.elements.boxes.chart')
                            @slot('subtitle','INDICE DE APROBADOS')
                            @slot('class','success')
                            @slot('total',$indicador['porc_aprobados'] )
                            @slot('class_total','font-size-md')
                        @endcomponent
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>


