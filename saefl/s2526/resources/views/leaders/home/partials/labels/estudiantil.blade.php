<div class="">

    <div class="card-body p-1">

        <div class="container-fluid">
            @foreach ($area_conocimientos as $area_conocimiento)

            <div class="p-2">

                <div class="card-title font-weight-bold mb-0 pb-2">                    
                    <div class="d-flex justify-content-start text-muted">
                        <div class="">
                            {{$area_conocimiento->name}}
                        </div>
                        <div class="ml-2 font-weight-lighter">
                            [{{$area_conocimiento->code}}]
                        </div>
                    </div>                   
                </div>

                <div class="row">                                    
                    <div class="col p-1">
                        @php $estudiants =  $area_conocimiento->estudiants($lapso->id); @endphp
                        @component('leaders.elements.boxes.indicators')
                            @slot('title','ESTUDIANTES INSCRITOS' )
                            @slot('class',( !empty($area_conocimiento->color) ? $area_conocimiento->color : null) )
                            @slot('total',$estudiants->count() )
                        @endcomponent
                    </div>
                    <div class="col p-1">
                        @php $total = $area_conocimiento->campo_conocimientos->count() @endphp
                        @component('leaders.elements.boxes.indicators')
                            @slot('title','AREAS DE FORMACIÓN' )
                            @slot('class',( !empty($area_conocimiento->color) ? $area_conocimiento->color : null) )
                            @slot('icon',$icon_menus['aconocimiento'])
                            @slot('total',$total )
                        @endcomponent
                    </div>                
                    <div class="col p-1">
                        @php $total = $area_conocimiento->getEvaluacions($lapso->id)->count() @endphp
                        @component('leaders.elements.boxes.indicators')
                            @slot('title','EVALUACIONES REGISTRADAS' )
                            @slot('class','success')
                            @slot('icon',$icon_menus['evaluacion'])
                            @slot('total', $total )
                        @endcomponent
                    </div>
                    <div class="col p-1">
                        @php $total = $area_conocimiento->getProfesorEvaluacions($lapso->id)->count() @endphp
                        @component('leaders.elements.boxes.indicators')
                            @slot('title','PROFESORES CON CARGA ACADÉMICA ASIGNADA' )
                            @slot('class','warning')
                            @slot('icon',$icon_menus['profesor'])
                            @slot('total', $total )
                        @endcomponent
                    </div>

                    <div class="col p-1">
                        @php $total = $area_conocimiento->getBoletins($lapso->id)->count() @endphp
                        @component('leaders.elements.boxes.indicators')
                            @slot('title','NOTAS CARGADAS' )
                            @slot('class','warning')
                            @slot('icon',$icon_menus['boletin'])
                            @slot('total', $total )
                        @endcomponent
                    </div>
                    {{-- 
                    <div class="col p-1">
                        @php $total = $area_conocimiento->getEvaluacions()->count() @endphp
                        @component('evaluacions.elements.boxes.indicators')
                            @slot('title','EVALUACIONES REGISTRADAS' )
                            @slot('class','success')
                            @slot('total', $total )
                        @endcomponent
                    </div>
                    <div class="col p-1">
                        @php $total = $area_conocimiento->getProfesorEvaluacions()->count() @endphp
                        @component('evaluacions.elements.boxes.indicators')
                            @slot('title','PROFESORES CON CARGA ACADÉMICA ASIGNADA' )
                            @slot('class','warning')
                            @slot('icon',$icon_menus['profesor'])
                            @slot('total', $total )
                        @endcomponent
                    </div> --}}
                </div>
            </div>

            <hr>
                
            @endforeach            
        </div>
    </div>
</div>
