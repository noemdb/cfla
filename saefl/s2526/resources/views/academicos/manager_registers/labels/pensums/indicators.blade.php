<div class="card-body p-1">
    @foreach ($pestudios as $pestudio)

    @php
        $seccions = $pestudio->getSeccions();
        $pevaluacions = $pestudio->getPevaluacions($lapso->id);
        $pevaluacions_pensums = $pestudio->getPevaluacionsPensums($lapso->id); //if ($pestudio->name=="EDUCACION MEDIA GENERAL") dd($pestudio,$lapso,$pevaluacions_pensums);
        $pevaluacion_components = $pestudio->getPevaluacionComponents($lapso->id);
        $evaluacions = $pestudio->getEvaluacions($lapso->id); //if ($pestudio->name=="EDUCACION MEDIA GENERAL") dd($pestudio,$lapso,$evaluacions);
        $pensums = $pestudio->getPensumsAllSeccion($lapso->id); //if ($pestudio->name=="EDUCACION MEDIA GENERAL") dd($pestudio,$lapso,$pevaluacions_pensums,$pensums);
        // $pensums = $pestudio->pensums;
        $profesors = $pestudio->getProfesors($lapso->id);
        $profesor_guias = $pestudio->getProfesorGuia($lapso->id);
    @endphp

        <div class=" border rounded pb-2 mb-2">

            <div class=" font-weight-bolder alert alert-secondary">

                <div class="d-flex justify-content-between">
                    <div>
                        <span> {{ $pestudio->name ?? ''}} </span>
                    </div>
                    {{-- <div> <span class="text-secondary small font-weight-light">{{$lapso->name ?? null}}</span> </div> --}}
                </div> 
            </div>

            <div class="container-fluid">
                <div class="row pb-2 text-center">
                    <div class="col px-2">
                        @component('academicos.elements.boxes.indicators')
                            @slot('title','Secciones')
                            @slot('class','primary')
                            @slot('icon',$icon_menus['seccions'])
                            @slot('total',$seccions->count() )
                            {{-- @slot('unidad',' pts') --}}
                            @slot('subtitle','Cantidad de secciones' )
                        @endcomponent
                    </div>
    
                    <div class="col px-2">
                        @component('academicos.elements.boxes.indicators')
                            @slot('title','Profesores')
                            @slot('class','secondary')
                            @slot('icon',$icon_menus['profesor'])
                            @slot('total',$profesors->count() )
                            {{-- @slot('unidad',' pts') --}}
                            @slot('subtitle','Cantidad de profesores con asignación académica' )
                        @endcomponent
                    </div>
    
                    <div class="col px-2">
                        @php
                            $count_profesor_guias = $profesor_guias->count(); //real
                            $count_seccions = $seccions->count(); //goal
                            $indice = ($count_seccions > 0) ? round((100 * $count_profesor_guias / $count_seccions),1)  : null ;
                        @endphp
                        @component('academicos.elements.boxes.indicators')
                            @slot('title','Asignación Profesor Guía')
                            @slot('class','secondary')
                            @slot('icon',$icon_menus['profesor'])
                            {{-- @slot('total',$count_profesor_guias.' - '.$count_seccions ) --}}
                            @slot('total', $indice )
                            @slot('bars',true )
                            @slot('unidad',' %')
                            @slot('subtitle','% de la asignación de guiaturas' )
                        @endcomponent
                    </div>
    
                    <div class="col px-2">
                        @component('academicos.elements.boxes.indicators')
                            @slot('title','Áreas de Formación')
                            @slot('class','info')
                            @slot('icon',$icon_menus['pevaluacion'])
                            @slot('total',$pensums->count() )
                            {{-- @slot('unidad',' pts') --}}
                            @slot('subtitle','Cantidad de Á. de Formación según malla curricular' )
                        @endcomponent
                    </div>
    
                    <div class="col px-2">
                        @php
                            $count_pevaluacions = $pevaluacions_pensums->count(); //real
                            $count_pensums = $pensums->count(); //goal
                            $indice = ($count_pensums > 0) ? round((100 * $count_pevaluacions/$count_pensums),2)  : null ;
                        @endphp
                        @component('academicos.elements.boxes.indicators')
                            @slot('title','Carga académica')
                            @slot('class','warning')
                            @slot('icon',$icon_menus['pevaluacion'])
                            @slot('total',$indice )
                            @slot('bars',true )
                            @slot('unidad',' %')
                            @slot('subtitle','% de la carga académica asignada' )
                        @endcomponent
                    </div>
                    <div class="col px-2">
                        @php $count = $pevaluacion_components->count(); @endphp
                        @component('academicos.elements.boxes.indicators')
                            @slot('title','Comp. de Formación')
                            @slot('class','success')
                            @slot('icon',$icon_menus['pevaluacion'])
                            @slot('total',$count )
                            @slot('subtitle','Cantidad total de Componentes de Formación' )
                        @endcomponent
                    </div>
    
                    
    
                    {{-- <div class="col">
                        @component('academicos.elements.boxes.indicators')
                            @slot('title','Evaluaciones registradas')
                            @slot('class','secondary')
                            @slot('icon',$icon_menus['evaluacion'])
                            @slot('total',$evaluacions->count() )
                            @slot('subtitle','Cantidad de evaluaciones registradas' )
                        @endcomponent
                    </div> --}}
    
                    <div class="col px-2">
                        @php
                            $count_evaluacions = $evaluacions->count() - $pevaluacion_components->count(); //real
                            $count_pevaluacions = $pevaluacions->count(); //real
                            $indice = ($count_pevaluacions > 0) ? round(($count_evaluacions/$count_pevaluacions),2)  : 0 ;
                        @endphp
                        @component('academicos.elements.boxes.indicators')
                            @slot('title','Prom. de Evaluaciones')
                            @slot('class','danger')
                            @slot('icon',$icon_menus['evaluacion'])
                            @slot('total',$indice )
                            {{-- @slot('bars',true ) --}}
                            {{-- @slot('unidad',' %') --}}
                            @slot('subtitle','Cant. Prom. de Evaluaciones por Á. de Formación' )
                        @endcomponent
                    </div>
    
                    <div class="col px-2">
                        @php
                            $real_notas_load = $pestudio->real_notas_load($lapso->id); //real
                            $goal_notas_load = $pestudio->goal_notas_load($lapso->id); //real
                            $indice = ($goal_notas_load > 0) ? round((100 * $real_notas_load/$goal_notas_load),1)  : 0 ;
                        @endphp
                        @component('academicos.elements.boxes.indicators')
                            @slot('title','Carga de Notas')
                            @slot('class','primary')
                            @slot('icon',$icon_menus['boletin'])
                            @slot('total',$indice )
                            @slot('bars',true )
                            @slot('unidad',' %')
                            @slot('subtitle','% total de Carga de Notas.<br>['.$real_notas_load.'/'.$goal_notas_load.']')
                        @endcomponent
                    </div>
    
                </div>
            </div>

            

        </div>

    @endforeach
</div>
