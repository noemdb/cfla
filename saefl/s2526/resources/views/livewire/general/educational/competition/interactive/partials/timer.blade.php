<div class="bg-body-tertiary rounded-4 p-4 overlay shadow-sm border border-light-subtle">

    <div class="text-center mb-3">
        <span class="badge bg-danger-subtle text-danger text-uppercase fw-bold px-3">Cronómetro</span>
    </div>

    <div id="cronometro" class="text-center">

        @php
            $timerClasses = 'font-monospace-digital display-1 fw-bold mb-3 ';
            if ($status_running_timer) {
                $timerClasses .= 'text-danger animate-pulse';
            } else {
                $timerClasses .= $countDonw == 0 ? 'text-muted' : 'text-body';
            }
        @endphp

        <div @if ($status_running_timer) wire:poll.1s="runningTimer" @endif class="{{ $timerClasses }}">
            {{ $countDonw ?? '00' }}
            <small class="fs-6 text-muted d-block mt-n2">segundos</small>
        </div>

        @if ($countDonw == 0 && !$status_running_timer)
            <div class="badge bg-warning text-dark mb-3">¡Tiempo agotado!</div>
        @endif

    </div>

    <div class="mt-2">
        @if ($question)
            <div class="d-grid gap-2">
                @if ($status_running_timer)
                    <button type="button" class="btn btn-warning rounded- pill shadow-sm fw-bold"
                        wire:click="pauseTimer">
                        <i class="bi bi-pause-fill me-1"></i> Pausar
                    </button>
                @else
                    <button type="button" {{ $question->status_time_elapsed ? 'disabled' : null }}
                        class="btn btn-danger rounded-pill shadow-sm fw-bold" wire:click="startTimer">
                        <i class="bi bi-play-fill me-1"></i> Comenzar
                    </button>
                @endif
                <button type="button" {{ $question->status_time_elapsed ? 'disabled' : null }}
                    class="btn btn-outline-success rounded-pill fw-bold" wire:click="finishTimer">
                    <i class="bi bi-check-all me-1"></i> Finalizar Pregunta
                </button>
            </div>
        @else
            <div class="text-center text-muted py-3">Esperando pregunta...</div>
        @endif
    </div>

</div>
