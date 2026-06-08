<div class="card rounded-4 shadow-sm border-0 bg-body mt-4">
    <div class="card-body p-4">
        <h6 class="card-title fw-black text-uppercase text-muted mb-3">
            <i class="bi bi-bar-chart-fill me-2 text-success"></i>Resultados Preliminares
        </h6>
        <div class="list-group list-group-flush rounded-3 overflow-hidden border">
            @forelse ($groups as $item)
                <div class="list-group-item bg-body-tertiary">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">{{ $item->name }}</span>
                        <span
                            class="badge bg-success rounded-pill px-3">{{ $competition->getTotalScoreForGroup($item->id) }}
                            pts</span>
                    </div>
                </div>
            @empty
                <div class="list-group-item text-center text-muted small">Sin datos</div>
            @endforelse
        </div>
    </div>
</div>
