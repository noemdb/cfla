{{-- @include('administracion.boletins.indicators.info') --}}

{{-- <hr class="py-2"> --}}

<ul class="nav nav-tabs nav-fill">
    @foreach($lapsos as $lapso)
        <li class="nav-item">
            <a href="#tab{{$lapso->id}}" data-toggle="tab" class="nav-item nav-link {{($loop->iteration==$lapso_active->id) ? 'active':''}}">
                <div class="font-weight-bold">{{$lapso->name}}</div>
                <div class="small">Inicio/Fin: {{f_date($lapso->finicial)}} / {{f_date($lapso->ffinal)}}</div>
                <div class="small">Corte de Notas: {{f_date($lapso->date_cutnote)}}</div>                
            </a>
        </li>
    @endforeach
</ul>

<div class="tab-content border border-top-0">
    @foreach($lapsos as $lapso)
        <div class="tab-pane fade {{($loop->iteration==$lapso_active->id) ? 'show active':''}}" id="tab{{$lapso->id}}">

            <div class="p-4">
                <ul class="nav nav-tabs nav-fill">
                    @foreach($pestudios as $pestudio)
                        @php $ieePROM = $pestudio->getProfesorsIEEsPROM($lapso->id) @endphp
                        <li class="nav-item">
                            <a class="nav-item nav-link {{($loop->iteration==1) ? 'active':''}}" href="#nested-{{$lapso->id}}-{{$pestudio->id}}" data-toggle="tab">
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
                        <div class="tab-pane {{($loop->iteration==1) ? 'active':''}}" id="nested-{{$lapso->id}}-{{$pestudio->id}}">
                            <div class="p-4">
                                @php $profesors = $pestudio->getProfesors(); @endphp
                                @include('plannings.partials.profesors')
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    @endforeach

</div>
