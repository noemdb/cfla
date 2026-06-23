<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">Top {{ $limit ?? 6 }} Usuarios más Activos</h5>
        </div>
        <div class="d-flex align-items-center mb-3">
            <span class="text-secondary font-bold me-2">
                <i class="fas fa-filter me-1"></i> Filtros:
            </span>
            @if($start_date || $end_date || $selectedArea || $selectedRol)
                <div class="d-flex flex-wrap gap-2">
                    @if($start_date)
                        <span class="text-muted">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Inicio: {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }}
                        </span>
                    @endif
                    @if($end_date)
                        <span class="text-muted">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Fin: {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}
                        </span>
                    @endif
                    @if($selectedArea)
                        <span class="text-muted">
                            <i class="fas fa-building me-1"></i>
                            Unidad: {{ $selectedArea }}
                        </span>
                    @endif
                    @if($selectedRol)
                        <span class="text-muted">
                            <i class="fas fa-user-tag me-1"></i>
                            Perfil: {{ $selectedRol }}
                        </span>
                    @endif
                </div>
            @else
                <span class="text-muted">Sin filtros</span>
            @endif
        </div>
        <div id="topUsersChart"></div>
    </div>
</div>

@section('scripts')
@parent
<script>
let topUsersChart = null;

function initTopUsersChart(topUsersData) {

    const chartElement = document.querySelector("#topUsersChart");
    if (!chartElement) return;

    if (!topUsersData || topUsersData.length === 0) {
        chartElement.innerHTML = '<div class="text-center p-4"><p class="text-muted">No hay datos disponibles</p></div>';
        return;
    }

    const userLabels = topUsersData.map(item => item.username);
    const userValues = topUsersData.map(item => item.count);

    const options = {
        series: [{
            name: 'Actividades',
            data: userValues
        }],
        chart: {
            type: 'bar',
            height: 400,
            toolbar: {
                show: false
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: {
                    enabled: true,
                    delay: 150
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 350
                }
            }
        },
        plotOptions: {
            bar: {
                horizontal: true,
                barHeight: '70%',
                borderRadius: 4,
                borderRadiusApplication: 'end',
                dataLabels: {
                    position: 'top',
                },
            }
        },
        dataLabels: {
            enabled: true,
            offsetX: -12,
            style: {
                fontSize: '12px',
                colors: ['#fff']
            }
        },
        stroke: {
            width: 1,
            colors: ['#fff']
        },
        grid: {
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            },
        },
        xaxis: {
            categories: userLabels,
            title: {
                text: 'Número de Actividades'
            }
        },
        yaxis: {
            title: {
                text: 'Usuarios'
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " actividades"
                }
            }
        },
        colors: ['#2ecc71']
    };

    if (topUsersChart) {
        topUsersChart.destroy();
    }

    chartElement.innerHTML = '';
    topUsersChart = new ApexCharts(chartElement, options);
    topUsersChart.render();
}

// Inicializar el chart cuando el componente se carga
document.addEventListener('DOMContentLoaded', function() {
    initTopUsersChart(@json($stats['topUsers']));
});

// Escuchar el evento de actualización de datos
document.addEventListener('livewire:load', function() {
    Livewire.on('topUsersDataUpdated', (data) => {
        initTopUsersChart(data);
    });
});
</script>
@endsection
