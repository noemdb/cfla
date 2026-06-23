<div>
    <div class="card">
        <i class="bi bi-list-columns-reverse" style="font-size: 2rem"></i>
        <h5 class="card-title text-dark">Listado de estudiantes con incidencias registradas</h5>
        <div class="card-body px-2">
            <div class="text-start">
                <div class="text-start">
                    <div><span class="fw-bold">Estudiante:</span> Buscar por nombre o cédula</div>
                    <div>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="bi bi-person-fill" style="font-size: 1rem"></i>
                            </span>
                            {!! Form::text('search', $search, [
                                'class' => 'form-control',
                                'wire:model.debounce.500ms' => 'search',
                                'placeholder' => 'Buscar Nombre o Cédula',
                            ]) !!}
                        </div>
                    </div>
                </div>
                <div class="small">
                    @forelse ($estudiants as $estudiant)
                        @php
                            $representant = $estudiant->representant;
                            $now = Carbon\Carbon::now();
                        @endphp
                        <div class="" x-data="{ open: false }">
                            <div class="text-secondary text-center" @click="open = ! open" role="button">
                                <div class="accordion-header border rounded">
                                    <button type="button" class="accordion-button collapsed p-1">
                                        <div class="d-flex justify-content-between">
                                            <div class="ms-2 me-auto small">
                                                <div class="fw-bold">{{ $loop->remaining + 1 }}.
                                                    {{ $estudiant->fullname }}</div>
                                                {{-- <span class="small text-muted ms-2">{{ $estudiant->incident_created_at->format('j \d\e M \d\e Y') }}</span> --}}
                                                <span
                                                    class="small text-muted ms-2">{{ $estudiant->incident_created_at }}</span>
                                                <div class="small text-muted ms-2">Repr.
                                                    {{ $representant->name ?? null }}</div>
                                            </div>
                                        </div>
                                    </button>
                                </div>
                            </div>
                            <div x-show="open" @click.outside="open = false" x-transition>
                                <div class="card card-body px-2">
                                    <div class="text-muted pb-2">
                                        @php $events = $estudiant->incident_events; @endphp
                                        @include('livewire.movile.profesor.incident.partials.timeline')
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item disabled">No hay registros</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
