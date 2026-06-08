<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">Actividades por Día</h5>
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
        <div id="activitiesChart"></div>
    </div>
</div>

@section('scripts')
@parent
<script>
let activitiesChart = null;

function initActivitiesChart(activitiesData) {

    const chartElement = document.querySelector("#activitiesChart");
    if (!chartElement) return;

    if (!activitiesData || activitiesData.length === 0) {
        chartElement.innerHTML = '<div class="text-center p-4"><p class="text-muted">No hay datos disponibles</p></div>';
        return;
    }

    const labels = activitiesData.map(item => item.date);
    const values = activitiesData.map(item => item.count);

    const options = {
        series: [{
            name: 'Actividades',
            data: values
        }],
        chart: {
            height: 350,
            type: 'area',
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
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        grid: {
            borderColor: '#e7e7e7',
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            },
        },
        markers: {
            size: 4
        },
        xaxis: {
            categories: labels,
            labels: {
                rotate: -45,
                style: {
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            opposite: true,
            title: {
                text: 'Número de Actividades'
            },
            min: 0
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " actividades"
                }
            }
        },
        colors: ['#3498db']
    };

    if (activitiesChart) {
        activitiesChart.destroy();
    }

    chartElement.innerHTML = '';
    activitiesChart = new ApexCharts(chartElement, options);
    activitiesChart.render();
}

// Inicializar el chart cuando el componente se carga
document.addEventListener('DOMContentLoaded', function() {
    initActivitiesChart(@json($stats['activitiesByDay']));
});

// Escuchar el evento de actualización de datos
document.addEventListener('livewire:load', function() {
    Livewire.on('activitiesByDayDataUpdated', (data) => {
        initActivitiesChart(data);
    });
});
</script>
@endsection
