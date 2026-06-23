
<ul class="nav nav-tabs nav-fill">
    @foreach($lapsos as $lapso)
        @php $id = "activities-tab-".$lapso->id; @endphp
        <li class="nav-item">
            <a href="#{{$id}}" data-toggle="tab" class="nav-item nav-link {{($loop->iteration==$lapso_active->id) ? 'active':''}}">
                <div class="font-weight-bold">{{$lapso->name}}</div>
                <div class="small">Inicio/Fin: {{f_date($lapso->finicial)}} / {{f_date($lapso->ffinal)}}</div>
                <div class="small">Corte de Notas: {{f_date($lapso->date_cutnote)}}</div>                
            </a>
        </li>
    @endforeach
</ul>

<div class="tab-content border border-top-0">
    @foreach($lapsos as $lapso)
        @php $id = "activities-tab-".$lapso->id; @endphp
        <div class="tab-pane fade {{($loop->iteration==$lapso_active->id) ? 'show active':''}}" id="{{$id}}">

            <div class="p-4">
                <ul class="nav nav-tabs nav-fill">
                    @foreach($pestudios as $pestudio)
                        @php $nested_id = "activities-tab-nested-".$lapso->id.'-'.$pestudio->id; @endphp                        
                        @php $ieePROM = $pestudio->getProfesorsIEEsPROM($lapso->id) @endphp
                        <li class="nav-item">
                            <a class="nav-item nav-link {{($loop->iteration==1) ? 'active':''}}" href="#{{$nested_id}}" data-toggle="tab">
                                {{$pestudio->name}} 
                                <div class="text-muted d-block small" title="Cantidad promédio de notas por profesor">
                                    Cant. Prom. de Notas[{{round($ieePROM,2)}}]
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="tab-content border border-top-0">
                    @foreach($pestudios as $pestudio)
                        @php $nested_id = "activities-tab-nested-".$lapso->id.'-'.$pestudio->id; @endphp 
                        <div class="tab-pane {{($loop->iteration==1) ? 'active':''}}" id="{{$nested_id}}">
                            <div class="p-4">
                                @php $activities = $pestudio->getActivities($lapso->id); @endphp
                                @include('plannings.partials.activity')
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    @endforeach

</div>
