<div class="container-fluid py-0 px-0">
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <!-- Encabezado -->
        <div
            class="card-header bg-white border-bottom border-light py-1 px-1">
            <div class="fw-medium text-primary m-2">
                {{-- <i class="fas fa-trophy me-1"></i> --}}
                Resultados preliminares
            </div>
            <div class="badge bg-light text-secondary border p-2">
                Últ. Actalización: <span class="fw-bold">{{ $date ?? 'N/A' }}</span>
            </div>
        </div>

        <div class="card-body p-2" wire:poll.5000ms="updateDateScoreBoard">
            @if ($competition)
                @php $peducativos = ($competition) ? $competition->peducativos : collect(); @endphp

                @forelse ($peducativos as $peducativo)
                
                    <div class="mb-2 bg-white rounded-3 border overflow-hidden">
                        <div class="bg-primary bg-opacity-10 py-2 px-3">
                            <div class="d-flex text-start justify-content-between gap-2">
                                {{-- <i class="fas fa-school text-primary"></i> --}}
                                <span class="fw-bold">{{ $peducativo->name }}</span>
                                <span class="fw-light small">Secciones</span>
                            </div>
                        </div>

                        @php $grados = $peducativo->grados; @endphp
                        <div class="p-2">
                            @forelse ($grados as $grado)
                                <div class="border-bottom py-1 mb-1 {{ $loop->last ? 'border-0' : '' }}">
                                    <div class="mb-1">
                                        <div class="d-flex align-items-center justify-content-between">

                                            <span class="badge bg-light text-dark fw-bolder rounded me-2 p-2">
                                                {{-- <i class="fas fa-graduation-cap me-1"></i> --}}
                                                {{ $grado->name }}
                                            </span>

                                            <div>
                                                @php $secciones = $grado->activeSeccions(); @endphp
                                                <div class="ms-3 mt-1 d-flex flex-wrap gap-2">
                                                    @forelse ($secciones as $seccion)
                                                        <div class="score-badge">
                                                            <span class="text-secondary fw-bold">{{ $seccion->name }}:</span>
                                                            <span
                                                                class="fw-bold text-primary">{{ $competition->getTotalScoreForSection($seccion->id) }}</span>
                                                        </div>
                                                    @empty
                                                        <div class="text-muted small">
                                                            <i class="fas fa-info-circle me-1"></i> Sin secciones
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>

                                    
                                </div>
                            @empty
                                <div class="alert alert-light text-center p-2 mb-0 small">
                                    <i class="fas fa-exclamation-circle text-muted me-1"></i>
                                    <span class="text-muted">Sin grados registrados</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="alert alert-warning rounded-3 p-3 mb-0" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span>No hay planes de estudio registrados</span>
                    </div>
                @endforelse
            @else
                <div class="alert alert-info rounded-3 p-3 mb-0 text-center" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <span class="fw-medium">No hay competiciones activas en este momento</span>
                </div>
            @endif
        </div>
    </div>
</div>

@section('stylesheets')
    @parent
    <style>
        /* Estilos mejorados para la tabla de resultados */
        :root {
            --primary-color: #4a6bff;
            --primary-dark: #2541b2;
            --secondary-color: #6c757d;
            --light-bg: #f8f9fa;
            --border-color: #dee2e6;
        }

        .card {
            transition: all 0.2s ease;
        }

        .card-header {
            font-size: 0.95rem;
        }

        .score-badge {
            display: inline-flex;
            align-items: center;
            background-color: white;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 0.25rem 0.75rem;
            font-size: 0.9rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .score-badge .fw-bold {
            margin-left: 0.25rem;
        }

        /* Eliminar espacios innecesarios */
        .card-body p {
            margin-bottom: 0;
        }

        /* Animación sutil para actualización de datos */
        [wire\:poll] {
            transition: background-color 0.3s ease;
        }

        /* Mejora visual para instituciones educativas */
        .bg-primary.bg-opacity-10 {
            border-left: 4px solid var(--primary-color);
        }
    </style>
@endsection
