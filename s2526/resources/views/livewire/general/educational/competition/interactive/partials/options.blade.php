<div class="text-uppercase text-muted small fw-bold mb-3">Opciones de Respuesta: </div>
<div class="container-fluid p-0">
    <div class="row g-3">
        @forelse ($options as $item)
            <div class="col-12 col-lg-6">
                @php
                    $status_show_correct =
                        $item->status_option_correct && $question->status_time_elapsed ? true : false;
                    $cardClasses = 'card h-100 rounded-4 border-3 transition-all ';
                    if ($status_show_correct) {
                        $cardClasses .= 'bg-success text-white border-success shadow';
                    } else {
                        $cardClasses .= 'bg-body border-light-subtle hover-shadow-sm';
                    }
                @endphp
                <div class="{{ $cardClasses }}" style="cursor: pointer; transition: all 0.2s ease;">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="badge rounded-circle {{ $status_show_correct ? 'bg-white text-success' : 'bg-success-subtle text-success' }} me-3 d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px; font-size: 1.2rem;">
                            {{ $loop->iteration }}
                        </div>
                        <h5 class="card-title mb-0 fw-bold">{{ $item->text }}</h5>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card rounded-4 border-dashed bg-body-tertiary">
                    <div class="card-body text-center py-4 text-muted">
                        <i class="bi bi-exclamation-circle fs-2 d-block mb-2"></i>
                        No hay opciones configuradas para esta pregunta.
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
    .hover-shadow-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
        border-color: var(--bs-success) !important;
    }

    .fw-black {
        font-weight: 900 !important;
    }

    .ls-wide {
        letter-spacing: 0.1em;
    }
</style>
