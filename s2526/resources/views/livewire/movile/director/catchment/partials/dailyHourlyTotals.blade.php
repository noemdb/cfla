@php $uniqueId = 'daily-chart-' . uniqid(); $total = $dailyHourlyTotals->sum('total'); $sortedDaily = collect($dailyHourlyTotals)->sortByDesc('total'); @endphp

<div class="card shadow stat-chart h-100" id="{{$uniqueId}}">
    <div class="card-body p-0">
        <div class="d-flex flex-column h-100">
            <!-- Encabezado -->
            <div class="chart-header px-4 pt-4 pb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 chart-title">
                        <i class="bi bi-calendar-week text-primary"></i>
                        <span class="ms-2">Distribución de registros por día</span>
                    </h6>
                    <div class="total-badge">{{ number_format($total) }}</div>
                </div>
            </div><!-- Contenedor del gráfico -->
        <div class="chart-content flex-grow-1 px-4 py-3">
            @foreach ($sortedDaily as $index => $item)
                @php
                    $indice = $total > 0 ? round(100 * ($item->total / $total), 1) : 0;
                    $colorIndex = $loop->index % 10;
                    $colorClass = "chart-color-" . ($colorIndex + 1);
                    $isHidden = $loop->index >= 4;
                @endphp
                <div class="chart-item {{ $isHidden ? 'd-none' : '' }}" data-day="{{ $loop->index }}">
                    <div class="bar-container">
                        <!-- Etiqueta superior -->
                        <div class="bar-label">
                            <span>{{ f_date($item->date) }}</span>
                            <span>{{ $item->total ?? null }}</span>
                        </div>
                        
                        <!-- Barra horizontal -->
                        <div class="progress-container">
                            <div class="progress-bar {{ $colorClass }}" style="width: {{ $indice }}%;"></div>
                            <span class="value-label" style="left: {{ min(100, $indice + 2) }}%">{{ number_format($item->total) }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pie de gráfico con botón para ver más -->
        @if ($sortedDaily->count() > 4)
            <div class="chart-footer px-4 py-3 border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Mostrando {{ min(4, $sortedDaily->count()) }} de {{ $sortedDaily->count() }}</span>
                    <button class="chart-toggle toggle-days">
                        <span class="show-text">Expandir</span>
                        <span class="hide-text d-none">Contraer</span>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

</div>@section('scripts') @parent <script> document.addEventListener('DOMContentLoaded', function () { const charts = document.querySelectorAll('.stat-chart');

charts.forEach(chart => {
            const toggleBtn = chart.querySelector('.toggle-days');
            if (!toggleBtn) return;
            
            const items = chart.querySelectorAll('.chart-item');
            const showText = toggleBtn.querySelector('.show-text');
            const hideText = toggleBtn.querySelector('.hide-text');
            
            toggleBtn.addEventListener('click', function() {
                const isExpanded = hideText.classList.contains('d-none');
                
                items.forEach((item, index) => {
                    if (index >= 4) {
                        if (isExpanded) {
                            item.style.opacity = '0';
                            item.classList.remove('d-none');
                            setTimeout(() => {
                                item.style.transition = 'opacity 0.3s ease';
                                item.style.opacity = '1';
                            }, 10);
                        } else {
                            item.style.transition = 'opacity 0.3s ease';
                            item.style.opacity = '0';
                            setTimeout(() => {
                                item.classList.add('d-none');
                                item.style.transition = '';
                            }, 300);
                        }
                    }
                });
                
                showText.classList.toggle('d-none');
                hideText.classList.toggle('d-none');
            });
        });
    });
</script>

@endsection

