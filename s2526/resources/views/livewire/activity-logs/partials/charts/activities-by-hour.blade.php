<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">Actividades por Hora (Hoy)</h5>
        </div>
        <div class="d-flex align-items-center mb-3">
            <span class="text-secondary font-bold me-2">
                <i class="fas fa-filter me-1"></i> Filtros:
            </span>
            @if($selectedArea || $selectedRol)
                <div class="d-flex flex-wrap gap-2">
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
        <div id="activitiesByHourChart"></div>
    </div>
</div>

@section('scripts')
@parent
<script>
let activitiesByHourChart = null;

function initActivitiesByHourChart(activitiesByHourData) {
    const chartElement = document.querySelector("#activitiesByHourChart");
    if (!chartElement) return;

    if (!activitiesByHourData || activitiesByHourData.length === 0) {
        chartElement.innerHTML = '<div class="text-center p-4"><p class="text-muted">No hay datos disponibles</p></div>';
        return;
    }

    const hourLabels = activitiesByHourData.map(item => item.hour + ':00');
    const hourValues = activitiesByHourData.map(item => item.count);

    const options = {
        series: [{
            name: 'Actividades',
            data: hourValues
        }],
        chart: {
            height: 350,
            type: 'line',
            toolbar: {
                show: true
            },
            zoom: {
                enabled: false
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
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val > 0 ? val : '';
            }
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        grid: {
            borderColor: '#e7e7e7',
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            },
        },
        markers: {
            size: 5,
            hover: {
                size: 7
            }
        },
        xaxis: {
            categories: hourLabels,
            title: {
                text: 'Hora del día'
            },
            tickAmount: hourLabels.length
        },
        yaxis: {
            title: {
                text: 'Número de Actividades'
            },
            min: 0,
            forceNiceScale: true
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " actividades"
                }
            }
        },
        colors: ['#e74c3c']
    };

    if (activitiesByHourChart) {
        activitiesByHourChart.destroy();
    }

    chartElement.innerHTML = '';
    activitiesByHourChart = new ApexCharts(chartElement, options);
    activitiesByHourChart.render();
}

// Inicializar el chart cuando el componente se carga
document.addEventListener('DOMContentLoaded', function() {
    initActivitiesByHourChart(@json($stats['activitiesByHour']));
});

// Escuchar el evento de actualización de datos
document.addEventListener('livewire:load', function() {
    Livewire.on('activitiesByHourDataUpdated', (data) => {
        initActivitiesByHourChart(data);
    });
});
</script>
@endsection
