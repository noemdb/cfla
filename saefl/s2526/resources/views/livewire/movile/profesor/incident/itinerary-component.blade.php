<div>

    <div class="p-1">

        <div class="m-1 small">
            <div class="text-start">
                <div><span class="fw-bold">Estudiante:</span> Buscar por nombre o cédula</div>
                <div>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-person-fill" style="font-size: 1rem"></i> </span>
                        {!! Form::text('search', $search, [
                            'class' => 'form-control',
                            'wire:model.debounce.500ms' => 'search',
                            'placeholder' => 'Buscar Nombre o Cédula',
                        ]) !!}
                    </div>
                </div>
            </div>
            {{-- @php $incidents_announcements =  $profesor->incidents_announcements @endphp --}}
            @include('livewire.movile.profesor.incident.partials.incident')
        </div>

    </div>

</div>
