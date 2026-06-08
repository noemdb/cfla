<div class="p-4">
    <ul class="nav nav-tabs nav-fill">
        @foreach($area_conocimientos as $area_conocimiento)
            @php $ieePROM = $area_conocimiento->getProfesorsIEEsPROM($lapso->id) @endphp
            <li class="nav-item">
                <a class="nav-item nav-link {{($loop->iteration==1) ? 'active':''}}" href="#nested-{{$lapso->id}}-{{$area_conocimiento->id}}" data-toggle="tab">
                    {{$area_conocimiento->name}} 
                    <div class="text-muted d-block small" title="Cantidad promédio de notas por profesor">
                        Cant. Prom. de Notas[{{round($ieePROM,2)}}]
                    </div>
                </a>
            </li>
        @endforeach
    </ul>
    <div class="tab-content border border-top-0">
        @foreach($area_conocimientos as $area_conocimiento)
            <div class="tab-pane {{($loop->iteration==1) ? 'active':''}}" id="nested-{{$lapso->id}}-{{$area_conocimiento->id}}">
                <div class="p-4">
                    @php $profesors = $area_conocimiento->getProfesorEvaluacions(); @endphp
                    @include('leaders.home.indicators.profesors')
                </div>
            </div>
        @endforeach
    </div>
</div>