<div>

    <div class="container-fluid py-4">
        <!-- Título de la competición -->

        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="fw-black text-success text-uppercase ls-wide" id="competicion-title">{{ $competition->name }}
                </h2>
                <div class="d-flex justify-content-center align-items-center mt-2">
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">En
                        progreso</span>
                </div>
            </div>
        </div>

        @if ($debates->count())
            <!-- Debate y Pregunta Activa -->
            @include('livewire.general.educational.competition.interactive.debates')
        @else
            <div class="row mt-2">
                @include('livewire.general.educational.competition.interactive.partials.final')
            </div>
        @endif

    </div>

    <div class="text-muted fw-light small text-end">Responsable: Prof: {{ $profesor_name ?? null }}</div>

    <!-- Navbar fijo en la parte baja -->
    @include('livewire.general.educational.competition.interactive.partials.navbar')

    @if ($status_running_timer)
        <div class="overlay-background"></div>
    @endif


</div>
