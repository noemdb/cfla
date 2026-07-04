<div class="p-2">
    @php $profesor_trainings = $pensum->getProfesorTraining($seccion->id); @endphp
    
    <div class="border rounded">
        <h6 class="card-title alert table-success font-weight-bold"title="Profesores Asignados a Grupos Estables">Profesores Asignados</h6>
        <div class="p-1 shadow">
            <ul class="list-group list-group-flush">
                {{-- <li class="list-group-item list-group-item-success font-weight-bold" title="Profesores Asignados a Grupos Estables">Profesores Asignados a GE</li> --}}
                @forelse ($profesor_trainings as $profesor)
                    @php
                        // $grupo_estable = $profesor->grupo_estable ;
                        // $grupo_estable = $profesor->grupo_estable ;
                    @endphp
                    <li class="list-group-item small text-uppercase py-1" data-id="{{$profesor->id ?? ''}}">
                        
                        <span class="text-muted">{{$loop->iteration}}.- </span>
                        {{ ($profesor) ? $profesor->mdname: null }}
                        <div class="pl-2 text-muted small">{{ ($profesor) ? 'Sección: '.$profesor->seccion_name: null }} {{ ($profesor) ? $profesor->lapso_name: null }}</div>
                        {{-- <div class="small pl-2 font-weight-bold text-muted" title="Nombre del Grupo Estable"> {{ ($grupo_estable) ? $grupo_estable->name: null }} </div> --}}
                    </li>
                @empty
                    <div>
                        <span class="small text-muted float-right pr-2">
                            No hay Profesores asignados
                        </span>
                    </div>
                @endforelse
            </ul>
        </div>
    </div>
    

</div>