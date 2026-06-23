<div class="container-fluid py-1 px-0">
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <!-- Encabezado con gradiente sutil pero más compacto -->
        <div class="card-header border-0 py-2">
            <h6 class="mb-0 text-secondary fw-light">
                {{-- <i class="fas fa-trophy me-1"></i> --}}
                {{ $competitionTitle }}
            </h6>
        </div>

        @if ($status_active_competition)
            <div class="card-body p-2">
                <!-- Sección de debate compacta -->
                <div class="mb-2 p-2 bg-light rounded-3 border-start border-primary border-3">
                    <h5 class="text-primary mb-1 fs-6">
                        <i class="fas fa-comments me-1"></i>Debate
                    </h5>
                    <div class="d-flex align-items-center flex-wrap">
                        <span class="fw-bold">{{ $debateTitle }}</span>
                        <span class="badge bg-success text-white ms-2 p-1 rounded small">
                            {{-- <i class="fas fa-graduation-cap me-1"></i> --}}
                            {{ $gradoName }}
                        </span>
                    </div>
                </div>

                <!-- Sección de pregunta compacta -->
                <div class="mb-2 p-2 bg-light rounded-3 border-start border-danger border-3">
                    <h5 class="text-danger mb-1 fs-6">
                        <i class="fas fa-question-circle me-1"></i>Pregunta
                    </h5>
                    <p class="mb-0 small">{{ $activeQuestion }}</p>
                </div>

                <!-- Sección de opciones compacta -->
                <div class="p-2 bg-light rounded-3 border-start border-success border-3">
                    <h5 class="text-success mb-1 fs-6">
                        <i class="fas fa-list-ul me-1"></i>Opciones
                    </h5>
                    <div class="list-group">
                        @forelse ($options as $index => $option)
                            @php
                                $status_correct = ($option->status_option_correct && $status_time_elapsed && $status_answer) ? true : false;
                                $status_wrong_answer = ($option->status_wrong_answer) ? true : false;
                            @endphp
                            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $status_correct ? 'list-group-item-success' : '' }} border rounded-2 mb-1 py-1 px-2 small {{ $status_wrong_answer ? 'bg-danger' : null }}">
                                <div class="d-flex align-items-center">
                                    <span
                                        class="option-letter me-2 px-2 {{ $status_correct ? 'bg-success' : 'bg-primary' }} text-white rounded">
                                        {{ chr(65 + $index) }}
                                    </span>
                                    <span class="{{ $status_correct ? 'fw-bold' : '' }}">{{ $option->text }}</span>
                                </div>
                                @if ($status_correct)
                                    <span class="badge bg-success text-white p-1 rounded small">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                @endif
                                @if ($status_wrong_answer)
                                    <span class="badge bg-success text-white p-1 rounded small">
                                        <i class="{{ $icon_menus['incorrect'] }}"></i>
                                    </span>
                                @endif
                            </div>
                        @empty
                            <div class="list-group-item border rounded-2 p-2 text-center small">
                                <i class="fas fa-exclamation-circle me-1 text-muted"></i>
                                <span class="text-muted">No hay opciones disponibles</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @else
            <div class="card-body p-3 text-center">
                <div class="alert alert-warning rounded-3 p-2" role="alert">
                    <i class="fas fa-exclamation-triangle me-1 text-warning"></i>
                    <span class="fw-bold">Sin competición activa</span>
                    <p class="mb-0 small">No hay una competición activa en este momento.</p>
                </div>
            </div>
        @endif
    </div>
</div>





@section('stylesheets')
    @parent
    <style>
        /* Estilos personalizados para mejorar la apariencia */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #4a6bff 0%, #2541b2 100%);
        }

        .option-letter {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            font-weight: bold;
            font-size: 0.8rem;
        }

        /* Animaciones sutiles para interacciones */
        .list-group-item {
            transition: all 0.2s ease;
        }

        .list-group-item:hover {
            transform: translateX(3px);
        }

        /* Ajustes para vista compacta */
        .card-body .small {
            font-size: 0.85rem;
        }
    </style>
@endsection
