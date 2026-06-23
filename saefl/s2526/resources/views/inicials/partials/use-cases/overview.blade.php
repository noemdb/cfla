<div class="tab-pane fade show active" id="overview" role="tabpanel">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Casos de Uso Principales</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Selecciona cualquier tarjeta para ver el diagrama detallado del caso de uso correspondiente.
                    </p>
                    <div class="row">
                        @foreach($useCases as $useCase)
                        <div class="col-lg-6 col-xl-4 mb-4">
                            <div class="card use-case-card h-100 border-{{ $useCase['color'] }}"
                                data-target="#{{ $useCase['id'] }}">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="{{ $useCase['icon'] }} fa-3x text-{{ $useCase['color'] }}"></i>
                                    </div>
                                    <h5 class="card-title text-{{ $useCase['color'] }}">
                                        {{ $useCase['title'] }}
                                    </h5>
                                    <p class="card-text text-muted small">
                                        {{ $useCase['description'] }}
                                    </p>
                                    <div class="mt-auto">
                                        <span class="badge badge-{{ $useCase['color'] }}">Ver Diagrama</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
