<div class="card">
    <div class="card-header p-0 pl-1 alert-secondary">
        <i class="{{$icon_menus['estudiante'] ?? ''}}" aria-hidden="true"></i>
        <b>Estudiantíl</b>
    </div>
    <div class="card-body p-1">
        <div class="row">
            <div class="col-sm-2 pr-0">
                @component('administracion.elements.boxes.chart')
                    {{-- @slot('title','Total')                          --}}
                    @slot('subtitle','ESTUDIANTES INSCRITOS')                         
                    @slot('class','danger')                         
                    @slot('total',( !empty($estudiants->count()) ) ? $estudiants->count() : '' )                                                                            
                @endcomponent 
            </div>
            @foreach ($pestudios as $pestudio)
                <div class="col-sm-2 pr-0">
                    @component('administracion.elements.boxes.chart')
                        {{-- @slot('title','Estudiantes en ')                          --}}
                        @slot('class',( !empty($pestudio->color) ) ? $pestudio->color : '' )
                        @slot('subtitle','EN '.$pestudio->name)                         
                        @slot('total',( !empty($pestudio->inscritos()) ) ? $pestudio->inscritos()->value : '' )                                                                            
                    @endcomponent
                </div>                            
            @endforeach
            <div class="col-sm-2">
                @component('administracion.elements.boxes.chart')
                    @slot('class','success')
                    @slot('subtitle','ESTUDIANTES CON PLANES BENÉFICOS ACTUALMENTE')                         
                    @slot('total',( !empty($plan_beneficos) ) ? $plan_beneficos->count() : '' )                                                                            
                @endcomponent
            </div>
        </div>
    </div>
</div>