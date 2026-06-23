@php
    $uniqueId = 'grado-chart-' . uniqid();
    $total = $gradoIdTotals->sum();
    $sortedGrados = $gradoIdTotals->sortDesc();
@endphp

<div class="card shadow stat-chart h-100" id="{{$uniqueId}}">
    <div class="card-body p-0">
        <div class="d-flex flex-column h-100">
            <!-- Encabezado con diseño minimalista -->
            <div class="chart-header px-4 pt-4 pb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 chart-title">
                        <i class="bi bi-bar-chart-steps text-primary"></i>
                        <span class="ms-2">Distribución por Nivel</span>
                    </h6>
                    <div class="total-badge">
                        {{ number_format($total) }}
                    </div>
                </div>
            </div>
            
            <!-- Contenedor principal de gráficos -->
            <div class="chart-content flex-grow-1 px-4 py-3">
                @foreach ($sortedGrados as $key => $item)
                    @php
                        $indice = $total > 0 ? round(100 * ($item / $total), 1) : 0;
                        $colorIndex = $loop->index % 10;
                        $colorClass = "chart-color-" . ($colorIndex + 1);
                        $isHidden = $loop->index >= 4;
                    @endphp
                    <div class="chart-item {{ $isHidden ? 'd-none' : '' }}" data-grado="{{ $loop->index }}">
                        <div class="bar-container">
                            <!-- Etiqueta superior -->
                            <div class="bar-label">
                                <span>{{ $key }}</span>
                                <strong>{{ $item }}</strong>
                            </div>
                            
                            <!-- Barra horizontal -->
                            <div class="progress-container">
                                <div class="progress-bar {{ $colorClass }}" style="width: {{ $indice }}%;"></div>
                                <span class="value-label" style="left: {{ min(100, $indice + 2) }}%">{{ number_format($item) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pie de gráfico con botón para ver más -->
            @if ($sortedGrados->count() > 4)
                <div class="chart-footer px-4 py-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Mostrando {{ min(4, $sortedGrados->count()) }} de {{ $sortedGrados->count() }}</span>
                        <button class="chart-toggle toggle-grados">
                            <span class="show-text">Expandir</span>
                            <span class="hide-text d-none">Contraer</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>


@section('scripts')
    @parent
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const charts = document.querySelectorAll('.stat-chart');
            
            charts.forEach(chart => {
                const toggleBtn = chart.querySelector('.toggle-grados');
                if (!toggleBtn) return;
                
                const items = chart.querySelectorAll('.chart-item');
                const showText = toggleBtn.querySelector('.show-text');
                const hideText = toggleBtn.querySelector('.hide-text');
                
                toggleBtn.addEventListener('click', function() {
                    const isExpanded = hideText.classList.contains('d-none');
                    
                    // Transición para mostrar/ocultar elementos
                    items.forEach((item, index) => {
                        if (index >= 4) {
                            if (isExpanded) {
                                // Animación para mostrar
                                item.style.opacity = '0';
                                item.classList.remove('d-none');
                                setTimeout(() => {
                                    item.style.transition = 'opacity 0.3s ease';
                                    item.style.opacity = '1';
                                }, 10);
                            } else {
                                // Animación para ocultar
                                item.style.transition = 'opacity 0.3s ease';
                                item.style.opacity = '0';
                                setTimeout(() => {
                                    item.classList.add('d-none');
                                    item.style.transition = '';
                                }, 300);
                            }
                        }
                    });
                    
                    // Cambiar textos del botón
                    showText.classList.toggle('d-none');
                    hideText.classList.toggle('d-none');
                });
            });
            
            // Inicializar tooltips si están disponibles en Bootstrap
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        });
    </script>
@endsection