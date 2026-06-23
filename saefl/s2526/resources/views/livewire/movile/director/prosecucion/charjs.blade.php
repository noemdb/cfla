<!-- resources/views/livewire/movile/director/prosecucion/index-component.blade.php -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Estadísticas de Prosecución Estudiantil
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Filtros de fecha -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="dateFrom" class="form-label">Fecha Desde:</label>
                            <input type="date" id="dateFrom" class="form-control" wire:model="dateFrom">
                        </div>
                        <div class="col-md-4">
                            <label for="dateTo" class="form-label">Fecha Hasta:</label>
                            <input type="date" id="dateTo" class="form-control" wire:model="dateTo">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-primary me-2" wire:click="updateChartData">
                                <i class="fas fa-search me-1"></i>
                                Actualizar
                            </button>
                            <button class="btn btn-secondary" wire:click="loadProsecucionData">
                                <i class="fas fa-refresh me-1"></i>
                                Resetear
                            </button>
                        </div>
                    </div>

                    <!-- Estadísticas resumidas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Prosecuciones</h6>
                                            <h4 class="mb-0">{{ array_sum($chartSeries) }}</h4>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-graduation-cap fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Días Activos</h6>
                                            <h4 class="mb-0">{{ count($chartCategories) }}</h4>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-calendar-day fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Promedio/Día</h6>
                                            <h4 class="mb-0">
                                                {{ count($chartCategories) > 0 ? round(array_sum($chartSeries) / count($chartCategories), 1) : 0 }}
                                            </h4>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-chart-line fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Pico Máximo</h6>
                                            <h4 class="mb-0">{{ count($chartSeries) > 0 ? max($chartSeries) : 0 }}
                                            </h4>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-arrow-up fa-2x opacity-75"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        Prosecuciones por Fecha y Hora
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div id="prosecucion-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de datos detallada -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-table me-2"></i>
                                        Datos Detallados
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Fecha y Hora</th>
                                                    <th>Cantidad</th>
                                                    <th>Porcentaje</th>
                                                    <th>Progreso</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $total = array_sum($chartSeries);
                                                @endphp
                                                @foreach ($chartData as $datetime => $count)
                                                    @php
                                                        $percentage =
                                                            $total > 0 ? round(($count / $total) * 100, 1) : 0;
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $datetime }}</strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-primary">{{ $count }}</span>
                                                        </td>
                                                        <td>{{ $percentage }}%</td>
                                                        <td>
                                                            <div class="progress" style="height: 20px;">
                                                                <div class="progress-bar" role="progressbar"
                                                                    style="width: {{ $percentage }}%"
                                                                    aria-valuenow="{{ $percentage }}"
                                                                    aria-valuemin="0" aria-valuemax="100">
                                                                    {{ $percentage }}%
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
    @parent
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configuración del gráfico ApexCharts
            const chartOptions = {
                series: [{
                    name: 'Prosecuciones',
                    data: @json(array_values($chartSeries))
                }],
                chart: {
                    type: 'column',
                    height: 400,
                    toolbar: {
                        show: true
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '70%',
                        colors: {
                            ranges: [{
                                from: 0,
                                to: 10,
                                color: '#28a745'
                            }, {
                                from: 11,
                                to: 30,
                                color: '#ffc107'
                            }, {
                                from: 31,
                                to: 1000,
                                color: '#dc3545'
                            }]
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val;
                    },
                    style: {
                        fontSize: '11px',
                        fontWeight: 'bold',
                        colors: ['#304758']
                    }
                },
                xaxis: {
                    categories: @json(array_keys($chartCategories)),
                    labels: {
                        rotate: -45,
                        style: {
                            fontSize: '11px'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Número de Prosecuciones'
                    },
                    labels: {
                        formatter: function(val) {
                            return Math.round(val);
                        }
                    }
                },
                fill: {
                    opacity: 0.8,
                    gradient: {
                        shade: 'light',
                        type: 'vertical',
                        shadeIntensity: 0.5,
                        gradientToColors: ['#007bff'],
                        inverseColors: false,
                        opacityFrom: 1,
                        opacityTo: 0.8,
                        stops: [0, 100]
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + " prosecuciones"
                        }
                    },
                    theme: 'dark'
                },
                colors: ['#007bff'],
                grid: {
                    show: true,
                    borderColor: '#e7e7e7',
                    strokeDashArray: 3
                },
                responsive: [{
                    breakpoint: 768,
                    options: {
                        chart: {
                            height: 300
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: '90%'
                            }
                        },
                        dataLabels: {
                            enabled: false
                        }
                    }
                }]
            };

            // Crear el gráfico
            const chart = new ApexCharts(document.querySelector("#prosecucion-chart"), chartOptions);
            chart.render();

            // Escuchar eventos de Livewire
            window.addEventListener('updateChart', event => {
                chart.updateOptions({
                    series: [{
                        name: 'Prosecuciones',
                        data: event.detail.series
                    }],
                    xaxis: {
                        categories: event.detail.categories
                    }
                });
            });
        });
    </script>
@endsection
