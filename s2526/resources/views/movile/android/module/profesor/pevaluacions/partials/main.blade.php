<div class="text-start">
    <ul class="list-group px-1">
        <li class="list-group-item px-2 list-group-item-secondary fw-bold list-group-item-dark">

            <div class="ms-2 me-auto small">
                <div class="fw-bold">{{$lapso->name ?? null}}</div>
                <span class="fw-normal small text-muted ms-2">
                    Cargas de notas habilitada hasta la fecha de cierre:
                    <span class="fw-bold">{{ ($lapso->full_date_preclosing) ? $lapso->full_date_preclosing->format('d-m-Y h:i a') : null }}</span>
                </span>
            </div>

        </li>

        <li class="list-group-item px-2">
            <div class="accordion" id="accordionLoadPevaluacion-{{$lapso->id ?? null}}">
                @forelse ($pevaluacions as $pevaluacion)
                    @php $evaluacions = $pevaluacion->evaluacions; @endphp
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingLoad-{{$pevaluacion->id}}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-body-{{$pevaluacion->id}}" aria-expanded="false" aria-controls="collapseHeading-{{$pevaluacion->id}}">
                                <div class="ms-2 me-auto small">
                                    <div class="fw-bold text-muted">{{$pevaluacion->asignatura->name}} {{$pevaluacion->full_seccion}}</div>
                                    <span class="small text-muted ms-2">{{$pevaluacion->description}}</span>
                                    <span class="small text-muted">[{{$evaluacions->count()}}]</span>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse-body-{{$pevaluacion->id}}" class="accordion-collapse collapse" aria-labelledby="headingLoad-{{$pevaluacion->id}}" data-bs-parent="#accordionLoadPevaluacion-{{$lapso->id ?? null}}">
                            <div class="accordion-body px-2 pt-1">
                                <livewire:movile.profesor.evaluacion.index-component :evaluacions="$evaluacions" :wire:key="'notas-pevaluacion-'.$pevaluacion->id"/>
                            </div>
                        </div>
                    </div>
                @empty

                <div>No hay planes de evalución.</div>

                @endforelse
            </div>
        </li>

    </ul>
</div>
