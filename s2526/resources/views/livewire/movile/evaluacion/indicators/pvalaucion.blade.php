<div>

    <nav>
        <div class="nav nav-tabs nav-fill font-weight-bold" id="nav-tab" role="tablist">
            @foreach($lapsos as $lapso)
            <button class="nav-link p-1 {{($loop->iteration==$lapso_active->id) ? 'active':''}}"
                id="nav-profesor-tab-{{$lapso->id}}" data-bs-toggle="tab" data-bs-target="#nav-profesor-{{$lapso->id}}"
                type="button" role="tab" aria-controls="nav-profesor-{{$lapso->id}}"
                aria-selected="true">{{$lapso->name}}</button>
            @endforeach
        </div>
    </nav>

    <div class="tab-content border border-top-0 bg-white" id="nav-tabContent">
        @foreach ($lapsos as $lapso)
        @php $lapso_id = $lapso['lapso_id']; @endphp
        <div class="tab-pane fade show {{($loop->iteration==$lapso_active->id) ? 'show active':''}} p-2"
            id="nav-profesor-{{$lapso->id}}" role="tabpanel" aria-labelledby="nav-profesor-tab" tabindex="0">
            <div class="p-2">
                {{$lapso->name}}
                <div class="accordion" id="accordionLoadPevaluacion-{{$lapso->id ?? null}}">
                    @forelse ($peducativos as $peducativo)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingLoad-{{$peducativo->id}}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse-body-{{$peducativo->id}}" aria-expanded="false"
                                aria-controls="collapseHeading-{{$peducativo->id}}">
                                <div class="ms-2 me-auto small">
                                    <div class="fw-bold text-muted">{{$peducativo->name}}</div>
                                    {{-- <span class="small text-muted ms-2">{{$peducativo->description}}</span> --}}
                                    {{-- <span class="small text-muted">[{{$peducativos->count()}}]</span> --}}
                                </div>
                            </button>
                        </h2>
                        <div id="collapse-body-{{$peducativo->id}}" class="accordion-collapse collapse"
                            aria-labelledby="headingLoad-{{$peducativo->id}}"
                            data-bs-parent="#accordionLoadPevaluacion-{{$lapso->id ?? null}}">
                            <div class="accordion-body px-2 pt-1">
                                {{--
                                <livewire:movile.profesor.evaluacion.index-component :peducativos="$peducativos"
                                    :wire:key="'notas-peducativo-'.$peducativo->id" /> --}}
                            </div>
                        </div>
                    </div>
                    @empty

                    <div>No hay planes de Planes Educativos.</div>

                    @endforelse
                </div>


            </div>
        </div>
        @endforeach
    </div>

</div>