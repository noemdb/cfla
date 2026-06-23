<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-3 fw-black text-success text-uppercase ls-wide">Resultado Final</h1>
        <div class="text-muted">Competición Académica - SAEFL</div>
    </div>

    <div class="row g-4 justify-content-center">
        @forelse ($groups as $item)
            <div class="col-12 col-md-6 col-lg-5">
                <div class="card h-100 rounded-4 border-0 shadow-lg p-3 transition-all hover-winner">
                    <div class="card-body text-center">
                        <div class="text-success fs-3 mb-2"><i class="bi bi-trophy-fill"></i></div>
                        <h2 class="card-title fw-bold text-uppercase mb-4">{{ $item->name }}</h2>
                        <div class="display-1 fw-black text-success mb-2">
                            {{ $competition->getTotalScoreForGroup($item->id) }}</div>
                        <div class="text-muted text-uppercase ls-wide small fw-bold">Puntos Obtenidos</div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center">
                <div class="alert alert-warning rounded-4 border-0 shadow-sm">
                    No se encontraron grupos registrados en esta competición.
                </div>
            </div>
        @endforelse
    </div>

    <div class="text-center mt-5">
        <a href="{{ url('/') }}" class="btn btn-outline-success rounded-pill px-5">
            <i class="bi bi-house-door me-2"></i> Volver al Inicio
        </a>
    </div>
</div>

<style>
    .hover-winner:hover {
        transform: translateY(-10px);
        box-shadow: 0 1.5rem 3rem rgba(var(--bs-success-rgb), 0.15) !important;
    }
</style>
</div>
