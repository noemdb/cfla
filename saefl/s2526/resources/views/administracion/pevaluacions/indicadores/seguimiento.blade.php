<span class="p-2">
    <i class="{{ $icon_menus['pevaluacion'] ?? ''}} fa-1x text-info"></i>
    <b>SEGUIMIENTO DE LA EJECUCIÓN DE LOS PLANES DE EVALAUCIÓN - % DE NOTAS CARGADAS</b>
</span>

<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        @foreach($lapsos as $lapso)
            <a class="nav-item nav-link {{($loop->iteration==$lapso_active->id) ? 'active':''}}" id="nav-header-tab-seguimiento-{{$lapso->id}}" data-toggle="tab" href="#nav-content-seguimiento-{{$lapso->id}}" role="tab" aria-controls="nav-home" aria-selected="true"><b>{{$lapso->name ?? ''}}</b></a>
        @endforeach
    </div>
</nav>

<div class="tab-content border border-top-0" id="nav-tabContent">
    @foreach($lapsos as $lapso)
        <div class="tab-pane fade show {{($loop->iteration==$lapso_active->id) ? 'active':''}}" id="nav-content-seguimiento-{{$lapso->id}}" role="tabpanel" aria-labelledby="nav-header-home-tab-{{$lapso->id}}">
            <div class="p-2">
                <div class="row">
                    <div class="col">
                        @include('administracion.pevaluacions.indicadores.partials.grados')
                    </div>
                    {{-- <div class="col">
                        @include('administracion.pevaluacions.indicadores.partials.seccions')
                    </div>
                    <div class="col">
                        @include('administracion.pevaluacions.indicadores.partials.pensums')
                    </div> --}}
                    <div class="col">
                        @include('administracion.pevaluacions.indicadores.partials.profesors')
                    </div>
                </div>
            </div>
        </div>
    @endforeach

</div>
