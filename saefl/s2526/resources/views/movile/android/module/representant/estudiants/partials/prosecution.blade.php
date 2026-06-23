@php
    $hasPending = $estudiants->where('status_prosecution', false)->isNotEmpty();
@endphp

{{-- SECTION: SCHOOL CONTINUITY CONFIRMATION (GREEN) --}}
@if ($estudiants->isNotEmpty())
    <div class="mb-5 {{ $hasPending ? 'animate__animated animate__pulse animate__infinite animate__slow' : '' }}">
        <div class="bg-success bg-opacity-10 border border-success border-opacity-25 rounded-4 p-3 shadow-sm">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-success text-white rounded-3 d-flex justify-content-center align-items-center me-3 shadow-sm"
                    style="width: 40px; height: 40px;">
                    <i class="fas fa-calendar-check fa-lg"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-success">Continuidad Escolar 2026-2027</h6>
                    <small class="text-success opacity-75" style="font-size: 0.7rem;">Estado de permanencia
                        institucional</small>
                </div>
            </div>

            <div class="list-group list-group-flush rounded-3 overflow-hidden border">
                @foreach ($estudiants as $estudiant)
                    <div class="list-group-item d-flex justify-content-between align-items-center bg-white py-3">
                        <div class="d-flex align-items-center overflow-hidden">
                            <div class="bg-light rounded-circle d-flex justify-content-center align-items-center me-2 flex-shrink-0 ripple"
                                style="width: 32px; height: 32px;">
                                @if ($estudiant->status_prosecution)
                                    <i class="fas fa-check-circle text-success" style="font-size: 0.9rem;"></i>
                                @else
                                    <i class="fas fa-user text-muted" style="font-size: 0.8rem;"></i>
                                @endif
                            </div>
                            <div class="d-flex flex-column overflow-hidden">
                                <span class="fw-bold text-truncate"
                                    style="font-size: 0.85rem; color: #444;">{{ $estudiant->shortname }}</span>
                                @if ($estudiant->status_prosecution)
                                    <small class="text-success fw-bold" style="font-size: 0.6rem;">Confirmado el
                                        {{ Carbon\Carbon::parse($estudiant->date_prosecution)->format('d/m/Y') }}</small>
                                @endif
                            </div>
                        </div>

                        @if (!$estudiant->status_prosecution)
                            <button wire:click="confirmProsecution({{ $estudiant->id }})" wire:loading.attr="disabled"
                                class="btn btn-success btn-sm rounded-pill px-3 shadow-sm flex-shrink-0"
                                style="font-size: 0.7rem; font-weight: 700;">
                                <span wire:loading.remove
                                    wire:target="confirmProsecution({{ $estudiant->id }})">Confirmar</span>
                                <span wire:loading wire:target="confirmProsecution({{ $estudiant->id }})">
                                    <span class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>
                                </span>
                            </button>
                        @else
                            <div class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 shadow-none"
                                style="font-size: 0.65rem; font-weight: 800;">
                                LISTO
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            <p class="mt-2 mb-0 text-center text-muted italic" style="font-size: 0.65rem;">* Al confirmar, asegura el
                cupo administrativo para el próximo año.</p>
        </div>
    </div>
@endif
