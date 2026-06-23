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
            <div class="border border-top-0 rounded-bottom pb-2 p-2">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-3 pr-0 py-2">
                            @component('profesors.elements.boxes.chart')
                                @slot('subtitle','PLANES DE EVALUACION ASIGNADOS')
                                @slot('class','primary')
                                @slot('icon',$icon_menus['pevaluacion'])
                                @slot('total',$indicador['count_pevaluacions'] )
                                @slot('class_total','font-size-lg')
                            @endcomponent
                        </div>
                        <div class="col-sm-3 pr-0 py-2">
                            @component('profesors.elements.boxes.chart')
                                @slot('subtitle','NÚMERO DE EVALUACIONES REGISTRADAS')
                                @slot('class','info')
                                @slot('icon',$icon_menus['evaluacion'])
                                @slot('total', $indicador['count_evaluacions'] )
                                @slot('class_total','font-size-md')
                            @endcomponent
                        </div>
                        <div class="col-sm-3 pr-0 py-2">
                            @php /*fixNMDB*/ $indicador['porc_notas_load'] = ($indicador['porc_notas_load']>100) ? 100 : $indicador['porc_notas_load'] ; @endphp
                            @component('profesors.elements.boxes.chart')
                                @slot('subtitle','PORCENTAJE DE NOTAS REGISTRADAS')
                                @slot('class','secondary')
                                @slot('icon',$icon_menus['notas'])
                                @slot('total',$indicador['porc_notas_load'].'%' )
                                @slot('class_total','font-size-md')
                            @endcomponent
                        </div>
                        <div class="col-sm-3 pr-0 py-2">
                            @component('profesors.elements.boxes.chart')
                                @slot('subtitle','PROMEDIO GENERAL')
                                @slot('class','danger')
                                @slot('total',$indicador['promedio'] )
                                @slot('class_total','font-size-lg')
                            @endcomponent
                        </div>
                        <div class="col-sm-3 pr-0 py-2">
                            @component('profesors.elements.boxes.chart')
                                @slot('subtitle','INDICE DE APROBADOS')
                                @slot('class','success')
                                @slot('total',$indicador['porc_aprobados'] )
                                @slot('class_total','font-size-md')
                            @endcomponent
                        </div>

                        @php $pestudio = $profesor->pestudio; @endphp
                        {{-- @if ($pestudio) --}}
                            <div class="col-sm-3 pr-0 py-2" title="Índice de Rendimiento en Evaluación (IRE)">
                                @php
                                    $lapso = $indicador['lapso'];
                                    $ire = ($pestudio) ? $profesor->getProfesorIRE($pestudio->id,$lapso->id) : null;
                                    $indice = round((100*$ire),1) .'%' ;
                                    $code = ($pestudio) ? $pestudio->code : null;
                                @endphp
                                @component('profesors.elements.boxes.chart')
                                    @slot('subtitle','IRE - '.$code)
                                    @slot('class','danger')
                                    @slot('total',$indice )
                                    {{-- @slot('total',$ire ) --}}
                                    {{-- @slot('total',$pestudio->id ) --}}
                                    @slot('icon',$icon_menus['queuing'])
                                    @slot('class_total','font-size-md')
                                @endcomponent
                            </div>

                            <div class="col-12">
                                <div class="small">
                                    <div class="">
                                        El cálculo del IRE se puede realizar de la siguiente manera: IRE = (IEE del profesor / Promedio del IEE de los demás profesores que comparten el mismo plan de estudios) x 100
                                    </div>
                                    <div class="text-muted py-2">
                                        Este indicador mide la eficiencia relativa del profesor en la aplicación de evaluaciones, corrección y carga, en comparación con el promedio de eficiencia de los demás
                                        profesores que comparten el mismo plan de estudios. El IRE tiene en cuenta tanto la cantidad de evaluaciones aplicadas como la corrección y carga de las mismas. <br>
                                        Un IRE alto, indica que el profesor tiene un mejor rendimiento en la administración de las evaluaciones que el promedio de los demás profesores,
                                        mientras que un IRE bajo indica que el profesor tiene un rendimiento inferior al promedio.
                                    </div>
                                </div>
                            </div>
                        {{-- @endif --}}
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>


