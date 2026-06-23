<div class="text-start">

    <ul class="list-group">

        <li class="list-group-item px-2 list-group-item-secondary fw-bold list-group-item-dark">

            <div class="ms-2 me-auto small">
                <div class="fw-bold">{{$lapso->name ?? null}}</div>
                <span class="fw-normal small text-muted ms-2">
                    Carga de los Referentes T.P.:
                    <span class="fw-bold">{{ ($lapso->full_date_preclosing) ? $lapso->full_date_preclosing->format('d-m-Y h:i a') : null }}</span>
                </span>
            </div>

        </li>

        <li class="list-group-item px-2">

            @if($lapso->status_preclosing)
                <div class="accordion" id="accordionLoadPevaluacion-{{$lapso->id ?? null}}">
                    @php $key = 'reference-pevaluacion-'.$profesor->id.'-'.$profesor->id; @endphp
                    <livewire:movile.profesor.reference.index-component :pevaluacions="$pevaluacions" :wire:key="$key"/>
                </div>
            @else
                <div class="alert alert-warning p-1 m-1 text-center" role="alert">
                    <strong>No disponible</strong>
                </div>
            @endif
            
        </li>        

    </ul>

</div>
