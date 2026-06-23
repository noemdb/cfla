<div class="text-center mt-4">
    <div class="text-muted small text-uppercase fw-bold mb-3">Adjudicar Punto a:</div>
    @php $status_answer = ($question) ? $question->status_answer($competition->id) : collect(); @endphp
    @php $status_time_elapsed = ($question) ? $question->status_time_elapsed : null ; @endphp

    <div class="d-flex flex-wrap justify-content-center gap-2" role="group">
        @forelse ($groups as $item)
            <button type="button" {{ !$status_time_elapsed || $status_answer ? 'disabled' : null }}
                class="btn btn-outline-success rounded-pill px-3 fw-bold shadow-sm"
                wire:click="score({{ $item->id }})">
                <i class="bi bi-person-check me-1"></i> {{ $item->name }}
            </button>
        @empty
            <span class="text-muted small">No hay grupos registrados</span>
        @endforelse
    </div>
</div>
