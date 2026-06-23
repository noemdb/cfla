<nav>
    <div class="nav nav-tabs nav-fill font-weight-bold" id="nav-tab" role="tablist">
        @foreach($indicators as $indicador)
            <button class="nav-link p-1 {{($loop->iteration==$lapso_active->id) ? 'active':''}}" id="nav-profesor-tab-{{$indicador['id']}}" data-bs-toggle="tab" data-bs-target="#nav-profesor-{{$indicador['id']}}" type="button" role="tab" aria-controls="nav-profesor-{{$indicador['id']}}" aria-selected="true">{{Str::limit($indicador['name'],5,'.')}}</button>
        @endforeach
    </div>
</nav>

<div class="tab-content border border-top-0" id="nav-tabContent">
    @foreach ($indicators as $indicador)
        @php $lapso_id = $indicador['lapso_id']; @endphp
        <div class="tab-pane fade show {{($loop->iteration==$lapso_active->id) ? 'show active':''}} p-2" id="nav-profesor-{{$indicador['id']}}" role="tabpanel" aria-labelledby="nav-profesor-tab" tabindex="0">
            <div class="border border-0 rounded-bottom pb-2">
                <div class="row">
                    <div class="col-lg-4 pr-0">
                        @component('profesors.elements.boxes.chart')
                            @slot('subtitle','PLANES DE EVALUACION ASIGNADOS')
                            @slot('class','primary')
                            @slot('shadow','no')
                            @slot('icon',$icon_menus['pevaluacion'])
                            @slot('total',$indicador['count_pevaluacions'] )
                            @slot('class_total','font-size-lg')
                        @endcomponent
                    </div>
                    <div class="col-lg-4 pr-0">
                        @component('profesors.elements.boxes.chart')
                            @slot('subtitle','NÚMERO DE EVALUACIONES REGISTRADAS')
                            @slot('class','info')
                            @slot('shadow','no')
                            @slot('icon',$icon_menus['evaluacion'])
                            @slot('total', $indicador['count_evaluacions'] )
                            @slot('class_total','font-size-md')
                        @endcomponent
                    </div>
                    <div class="col-lg-4 pr-0">
                        @php /*fixNMDB*/ $indicador['porc_notas_load'] = ($indicador['porc_notas_load']>100) ? 100 : $indicador['porc_notas_load'] ; @endphp
                        @component('profesors.elements.boxes.chart')
                            @slot('subtitle','PORCENTAJE DE NOTAS REGISTRADAS')
                            @slot('class','secondary')
                            @slot('shadow','no')
                            @slot('icon',$icon_menus['notas'])
                            @slot('total',$indicador['porc_notas_load'].'%' )
                            @slot('class_total','font-size-md')
                        @endcomponent
                    </div>
                    <div class="col-lg-4 pr-0">
                        @component('profesors.elements.boxes.chart')
                            @slot('subtitle','PROMEDIO GENERAL')
                            @slot('class','danger')
                            @slot('shadow','no')
                            @slot('total',$indicador['promedio'] )
                            @slot('class_total','font-size-lg')
                        @endcomponent
                    </div>
                    <div class="col-lg-4 pr-0">
                        @component('profesors.elements.boxes.chart')
                            @slot('subtitle','INDICE DE APROBADOS')
                            @slot('class','success')
                            @slot('shadow','no')
                            @slot('total',$indicador['porc_aprobados'] )
                            @slot('class_total','font-size-md')
                        @endcomponent
                    </div>

                    <div class="col-lg-4 pr-0">
                        @php
                            $pestudio = $profesor->pestudio;
                            // $lapso = $lapso_active;
                            $ire = ($pestudio) ? $profesor->getProfesorIRE($pestudio->id,$lapso_id) : null;
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
                        {{-- @component('profesors.elements.boxes.chart')
                            @slot('subtitle','INDICE DE APROBADOS')
                            @slot('class','success')
                            @slot('shadow','no')
                            @slot('total',$indicador['porc_aprobados'] )
                            @slot('class_total','font-size-md')
                        @endcomponent --}}
                    </div>

                </div>
            </div>
        </div>
    @endforeach
</div>


@section('stylesheets')
	@parent
    <link href="{{ asset('css/docs.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/adminlte.css') }}" rel="stylesheet">
@endsection
