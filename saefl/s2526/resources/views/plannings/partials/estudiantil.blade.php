<div class="card">

    <div class="card-header p-0 pl-1 alert-secondary px-1 py-2">
        <i class="{{$icon_menus['chartarea'] ?? ''}}" aria-hidden="true"></i>
        <b>Indicadores principales</b>
    </div>

    <div class="card-body p-1">

        <div class="container-fluid">
            @foreach ($pestudios as $pestudio)

            <div class="mb-4">

                <div class="card-title font-weight-bold mb-0 pb-0">                    
                    <div class="d-flex justify-content-start">
                        <div class="">
                            {{$pestudio->name}}
                        </div>
                        <div class="ml-2 text-muted">
                            [{{$pestudio->code}}]
                        </div>
                    </div>                   
                </div>

                <div class="row">
                    <div class="col p-1">
                        @component('evaluacions.elements.boxes.indicators')
                            @slot('title','INSCRITOS EN '.( !empty($pestudio->name) ? $pestudio->name : null) )
                            @slot('class',( !empty($pestudio->color) ? $pestudio->color : null) )
                            @slot('total',( !empty($pestudio->inscritos()) ) ? $pestudio->inscritos()->value : '' )
                        @endcomponent
                    </div>
                    <div class="col p-1">
                        @php $total = $pestudio->getEvaluacions()->count() @endphp
                        @component('evaluacions.elements.boxes.indicators')
                            @slot('title','EVALUACIONES REGISTRADAS' )
                            @slot('class','success')
                            @slot('icon',$icon_menus['evaluacion'])
                            @slot('total', $total )
                        @endcomponent
                    </div>
                    <div class="col p-1">
                        @php $total = $pestudio->getActivities()->count() @endphp
                        @component('evaluacions.elements.boxes.indicators')
                            @slot('title','ACTIVIDADES REGISTRADAS' )
                            @slot('class','dark')
                            @slot('icon',$icon_menus['activities'])
                            @slot('total', $total )
                        @endcomponent
                    </div>
                    <div class="col p-1">
                        @php $total = $pestudio->getProfesorEvaluacions()->count() @endphp
                        @component('evaluacions.elements.boxes.indicators')
                            @slot('title','PROFESORES CON CARGA ACADÉMICA ASIGNADA' )
                            @slot('class','warning')
                            @slot('icon',$icon_menus['profesor'])
                            @slot('total', $total )
                        @endcomponent
                    </div>
                </div>
            </div>

            <hr>
                
            @endforeach            
        </div>
    </div>
</div>
