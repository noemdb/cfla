<div>
    <div class="p-1 border rounded shadow-lg">

        {{-- ✅ CASO 1: El profesor TIENE grados asignados como tutor --}}
        @if(isset($this->allGrados) && $this->allGrados->isNotEmpty())
            
            {{-- Alert informativa de grados --}}
            <div class="alert alert-info alert-dismissible fade show small py-2 mb-2" role="alert">
                <i class="fa fa-users me-1"></i> 
                <strong>Grados asignados como tutor:</strong> 
                <span class="ms-1">
                    @foreach($this->allGrados as $grado)
                        <span class="badge bg-secondary me-1">
                            {{ $grado->id }}{{ $grado->name ? ' - '.$grado->name : '' }}
                        </span>
                    @endforeach
                </span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            {{-- Alert de título --}}
            <div class="alert alert-primary alert-dismissible fade show" role="alert">
                <strong>Nueva actividad comunitaria</strong>            
                <button type="button" class="close" aria-label="Close" wire:click="close()">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Formulario HABILITADO --}}
            <form wire:submit.prevent="save" class="text-start p-2 m-2">
                @includeWhen($modeCreate,'livewire.profesor.social-actions.community-action.form.fields')  
                <button type="submit" class="btn btn-primary btn-block w-100">Guardar</button>
            </form>

        {{-- ❌ CASO 2: El profesor NO tiene grados asignados para el lapso actual --}}
        @else
            
            {{-- Alert de título deshabilitado --}}
            <div class="alert alert-secondary alert-dismissible fade show opacity-75" role="alert">
                <strong>Nueva actividad comunitaria</strong>            
                <button type="button" class="close" aria-label="Close" wire:click="close()" disabled>
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Alert de advertencia con lapso actual --}}
            @php
                $nombreLapso = $lapsoActual ? ($lapsoActual->name ?? $lapsoActual->description ?? 'Actual') : 'No definido';
            @endphp
            
            <div class="alert alert-warning border-warning mb-3" role="alert">
                <h5 class="alert-heading">
                    <i class="fa fa-exclamation-triangle me-2"></i>⚠️ Sin grado/año asignado
                </h5>
                <hr>
                <p class="mb-2 small">
                    No tienes un <strong>grado o año escolar</strong> asignado como tutor para el 
                    <strong>momento de evaluación actual: {{ $nombreLapso }}</strong>.
                </p>
                <p class="mb-2 small text-muted">
                    <i class="fa fa-calendar me-1"></i>
                    Para registrar una actividad comunitaria, debes tener al menos un grado asignado en este período.
                </p>
                <p class="mb-0 small text-muted">
                    <i class="fa fa-info-circle me-1"></i>
                    Contacta al coordinador académico para gestionar tu asignación de tutoría.
                </p>
            </div>

            {{-- Formulario DESHABILITADO visualmente --}}
            <div class="text-start p-2 m-2 opacity-50" style="pointer-events: none; user-select: none;">
                @includeWhen($modeCreate,'livewire.profesor.social-actions.community-action.form.fields')  
                <button type="button" class="btn btn-secondary btn-block w-100" disabled>
                    <i class="fa fa-lock me-1"></i> Formulario deshabilitado - Sin grado asignado
                </button>
            </div>

        @endif

    </div> 
</div>