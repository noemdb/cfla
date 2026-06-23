<div class="container-fluid py-1 px-0">

    <div wire:poll.5000ms="updateDate">

        @if ($status_active_competition)

            {{-- <div class="text-muted small fw-light">Última actualización: <strong>{{ $date ?? null }}</strong></div> --}}

            <div class="card-header bg-white border-bottom border-light py-1 px-1">
                <div class="fw-medium text-primary m-2">
                    Competición Activa
                </div>
                <div class="badge bg-light text-secondary border p-2">
                    Últ. Actalización: <span class="fw-bold">{{ $date ?? 'N/A' }}</span>
                </div>
            </div>  
            
            <hr class="m-0 p-0">
        
            {{-- <strong>Competición Activa</strong> --}}

            @include('livewire.movile.competition.partials.main')
        @else
            <div class="text-muted fw-bold">No hay competiciones activas</div>
        @endif
    </div>    
    
</div>
