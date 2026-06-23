<span class="p-2">
    <i class="{{ $icon_menus['pevaluacion'] ?? ''}} fa-1x text-info"></i>
    <b>PLAN DE EVALAUCIÓN - ASIGNACIÓN DE CARGA ACADÉMICA</b>
</span>

<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        @foreach($lapsos as $lapso)
            <a class="nav-item nav-link {{($loop->iteration==$lapso_active->id) ? 'active':''}}" id="nav-header-tab-asignacion-{{$lapso->id}}" data-toggle="tab" href="#nav-content-asignacion-{{$lapso->id}}" role="tab" aria-controls="nav-home" aria-selected="true"><b>{{$lapso->name ?? ''}}</b></a>
        @endforeach
    </div>
</nav>

<div class="tab-content border border-top-0" id="nav-tabContent">
    @foreach($lapsos as $lapso)
        <div class="tab-pane fade {{($loop->iteration==$lapso_active->id) ? ' show active ':''}}" id="nav-content-asignacion-{{$lapso->id}}" role="tabpanel" aria-labelledby="nav-header-home-tab-{{$lapso->id}}">
            <div class="p-2">
                <div class="row">
                    <div class="col-sm-6">

                        @php $total_1 = (!empty($lapso->goal_asign_p_e)) ? round(($lapso->real_asign_p_e/$lapso->goal_asign_p_e) * 100,0) :0; @endphp
                        @php $total_2 = (!empty($lapso->goal_carga_p_e)) ? round(($lapso->real_carga_p_e/$lapso->goal_carga_p_e) * 100,0) :0; @endphp
                        @php $total_3 = (!empty($lapso->goal_notas_p_e)) ? round(($lapso->real_notas_p_e/$lapso->goal_notas_p_e) * 100,0) :0; @endphp

                        @includeif('administracion.pevaluacions.partials.info_box.pevaluacion')

                    </div>
                    <div class="col-sm-6">
                        {{-- @include('administracion.pevaluacions.indicadores.partials.profesors') --}}
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
