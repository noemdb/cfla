<div class="card">

    <div class="card-header p-0 pl-1 alert-secondary px-1 py-2">
        <i class="{{$icon_menus['estudiante'] ?? ''}}" aria-hidden="true"></i>
        <b>Inscripción Estudiantíl - Planes Benéficos - Profesores</b>
    </div>

    <div class="card-body p-1">

        <div class="container px-1">
            <div class="row">
                <div class="col p-1">
                    @component('controls.elements.boxes.indicators')
                        @slot('title','ESTUDIANTES INSCRITOS' )
                        @slot('class','secondary')
                        @slot('total',( !empty($estudiants->count()) ) ? $estudiants->count() : '' )
                    @endcomponent
                </div>
                @foreach ($pestudios as $pestudio)
                    <div class="col p-1">
                        @component('controls.elements.boxes.indicators')
                            @slot('title','EN '.( !empty($pestudio->name) ? $pestudio->name : null) )
                            @slot('class',( !empty($pestudio->color) ? $pestudio->color : null) )
                            @slot('total',( !empty($pestudio->inscritos()) ) ? $pestudio->inscritos()->value : '' )
                        @endcomponent
                    </div>
                @endforeach
                <div class="col p-1">
                    @component('controls.elements.boxes.indicators')
                        @slot('title','ESTUDIANTES RETIRADOS' )
                        @slot('class','danger')
                        @slot('total',( !empty($retiros->count()) ) ? $retiros->count() : '' )
                    @endcomponent
                </div>
                <div class="col p-1">
                    @component('controls.elements.boxes.indicators')
                        @slot('title','ESTUDIANTES CON PLANES BENÉFICOS' )
                        @slot('class','success')
                        @slot('total',( !empty($plan_beneficos->count()) ) ? $plan_beneficos->count() : '' )
                    @endcomponent
                </div>
                <div class="col p-1">
                    @component('controls.elements.boxes.indicators')
                        @slot('title','PROFESORES CON CARGA ACADÉMICA ASIGNADA' )
                        @slot('class','warning')
                        @slot('icon',$icon_menus['profesor'])
                        @slot('total',( !empty($profesors->count()) ) ? $profesors->count() : '' )
                    @endcomponent
                </div>
            </div>
        </div>
    </div>
</div>
