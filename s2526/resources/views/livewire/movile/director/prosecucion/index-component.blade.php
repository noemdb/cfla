<div>
    <!-- Indicadores Generales -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-clipboard-check"></i> Prosecución de Estudiantes
                </h6>
                <button wire:click="refreshData" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Resumen General -->
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-2">
            <div class="card stat-chart border-0 bg-primary text-white">
                <div class="card-body p-2 text-center">
                    <div class="h4 mb-1">{{ $totalStudents }}</div>
                    <small class="opacity-75">Total Estudiantes</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="card stat-chart border-0 bg-success text-white">
                <div class="card-body p-2 text-center">
                    <div class="h4 mb-1">{{ $confirmedStudents }}</div>
                    <small class="opacity-75">Confirmados</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="card stat-chart border-0 bg-warning text-white">
                <div class="card-body p-2 text-center">
                    <div class="h4 mb-1">{{ $pendingStudents }}</div>
                    <small class="opacity-75">Pendientes</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-2">
            <div class="card stat-chart border-0 bg-info text-white">
                <div class="card-body p-2 text-center">
                    <div class="h4 mb-1">{{ $confirmationPercentage }}%</div>
                    <small class="opacity-75">% Confirmación</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de Progreso General -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card stat-chart">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold">Progreso General</span>
                        <span class="badge bg-primary">{{ $confirmationPercentage }}%</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar"
                            style="width: {{ $confirmationPercentage }}%" aria-valuenow="{{ $confirmationPercentage }}"
                            aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-muted">{{ $confirmedStudents }} confirmados</small>
                        <small class="text-muted">{{ $pendingStudents }} pendientes</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs para diferentes vistas -->
    <div class="row">
        <div class="col-12">
            <nav>
                <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                    <button class="nav-link active small" id="nav-pestudio-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-pestudio" type="button" role="tab" aria-controls="nav-pestudio"
                        aria-selected="true">
                        <i class="bi bi-building"></i>
                        {{-- <div class="d-none d-sm-inline ms-1">Nivel</div> --}}
                    </button>
                    <button class="nav-link small" id="nav-grado-tab" data-bs-toggle="tab" data-bs-target="#nav-grado"
                        type="button" role="tab" aria-controls="nav-grado" aria-selected="false">
                        <i class="bi bi-mortarboard"></i>
                        {{-- <div class="d-none d-sm-inline ms-1">Grado</div> --}}
                    </button>
                    <button class="nav-link small" id="nav-seccion-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-seccion" type="button" role="tab" aria-controls="nav-seccion"
                        aria-selected="false">
                        <i class="bi bi-people"></i>
                        {{-- <div class="d-none d-sm-inline ms-1">Sección</div> --}}
                    </button>
                    <button class="nav-link small" id="nav-gender-tab" data-bs-toggle="tab" data-bs-target="#nav-gender"
                        type="button" role="tab" aria-controls="nav-gender" aria-selected="false">
                        <i class="bi bi-gender-ambiguous"></i>
                        {{-- <div class="d-none d-sm-inline ms-1">Género</div> --}}
                    </button>
                    <button class="nav-link small" id="nav-timeline-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-timeline" type="button" role="tab" aria-controls="nav-timeline"
                        aria-selected="false">
                        <i class="bi bi-clock-history"></i>
                        {{-- <div class="d-none d-sm-inline ms-1">Cronología</div> --}}
                    </button>
                </div>
            </nav>

            <div class="tab-content border border-top-0 bg-white" id="nav-tabContent">
                <!-- Tab Nivel Educativo -->
                <div class="tab-pane fade show active p-2" id="nav-pestudio" role="tabpanel"
                    aria-labelledby="nav-pestudio-tab">
                    <div class="card stat-chart">
                        <div class="chart-header">
                            <div class="chart-title">
                                <i class="bi bi-building me-2"></i>Por Nivel Educativo
                            </div>
                        </div>
                        <div class="chart-content">
                            @if ($pestudioTotals && $pestudioTotals->count() > 0)
                                @foreach ($pestudioTotals as $index => $pestudio)
                                    <div class="chart-item">
                                        <div class="bar-container">
                                            <div class="bar-label text-start">
                                                <span>{{ is_object($pestudio) ? $pestudio->name : $pestudio['name'] }}</span>
                                                <strong>{{ is_object($pestudio) ? $pestudio->confirmed : $pestudio['confirmed'] }}/{{ is_object($pestudio) ? $pestudio->total : $pestudio['total'] }}</strong>
                                            </div>
                                            <div class="progress-container">
                                                <div class="progress-bar chart-color-{{ ($index % 10) + 1 }}"
                                                    style="width: {{ is_object($pestudio) ? $pestudio->percentage : $pestudio['percentage'] }}%">
                                                </div>
                                                <div class="value-label"
                                                    style="left: {{ is_object($pestudio) ? $pestudio->percentage : $pestudio['percentage'] }}%">
                                                    {{ is_object($pestudio) ? $pestudio->percentage : $pestudio['percentage'] }}%
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted py-3">
                                    <i class="bi bi-inbox"></i>
                                    <div>No hay datos disponibles</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tab Grados -->
                <div class="tab-pane fade p-2" id="nav-grado" role="tabpanel" aria-labelledby="nav-grado-tab">
                    <div class="card stat-chart">
                        <div class="chart-header">
                            <div class="chart-title">
                                <i class="bi bi-mortarboard me-2"></i>Por Grado
                            </div>
                        </div>
                        <div class="chart-content">
                            @if ($gradoTotals && $gradoTotals->count() > 0)
                                @foreach ($gradoTotals as $index => $grado)
                                    <div class="chart-item">
                                        <div class="bar-container">
                                            <div class="bar-label">
                                                <span>{{ is_object($grado) ? $grado->name : $grado['name'] }}</span>
                                                <strong>{{ is_object($grado) ? $grado->confirmed : $grado['confirmed'] }}/{{ is_object($grado) ? $grado->total : $grado['total'] }}</strong>
                                            </div>
                                            <div class="progress-container">
                                                <div class="progress-bar chart-color-{{ ($index % 10) + 1 }}"
                                                    style="width: {{ is_object($grado) ? $grado->percentage : $grado['percentage'] }}%">
                                                </div>
                                                <div class="value-label"
                                                    style="left: {{ is_object($grado) ? $grado->percentage : $grado['percentage'] }}%">
                                                    {{ is_object($grado) ? $grado->percentage : $grado['percentage'] }}%
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted py-3">
                                    <i class="bi bi-inbox"></i>
                                    <div>No hay datos disponibles</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tab Secciones -->
                <div class="tab-pane fade p-2" id="nav-seccion" role="tabpanel" aria-labelledby="nav-seccion-tab">
                    <div class="card stat-chart">
                        <div class="chart-header">
                            <div class="chart-title">
                                <i class="bi bi-people me-2"></i>Por Sección
                            </div>
                        </div>
                        <div class="chart-content" style="max-height: 400px; overflow-y: auto;">
                            @if ($seccionTotals && $seccionTotals->count() > 0)
                                @foreach ($seccionTotals as $index => $seccion)
                                    <div class="chart-item">
                                        <div class="bar-container">
                                            <div class="bar-label">
                                                <span class="small">
                                                    {{ is_object($seccion) ? $seccion->grado_name : $seccion['grado_name'] }}
                                                    "{{ is_object($seccion) ? $seccion->seccion_name : $seccion['seccion_name'] }}"
                                                </span>
                                                <strong>{{ is_object($seccion) ? $seccion->confirmed : $seccion['confirmed'] }}/{{ is_object($seccion) ? $seccion->total : $seccion['total'] }}</strong>
                                            </div>
                                            <div class="progress-container">
                                                <div class="progress-bar chart-color-{{ ($index % 10) + 1 }}"
                                                    style="width: {{ is_object($seccion) ? $seccion->percentage : $seccion['percentage'] }}%">
                                                </div>
                                                <div class="value-label"
                                                    style="left: {{ is_object($seccion) ? $seccion->percentage : $seccion['percentage'] }}%">
                                                    {{ is_object($seccion) ? $seccion->percentage : $seccion['percentage'] }}%
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted py-3">
                                    <i class="bi bi-inbox"></i>
                                    <div>No hay datos disponibles</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tab Género -->
                <div class="tab-pane fade p-2" id="nav-gender" role="tabpanel" aria-labelledby="nav-gender-tab">
                    <div class="card stat-chart">
                        <div class="chart-header">
                            <div class="chart-title">
                                <i class="bi bi-gender-ambiguous me-2"></i>Por Género
                            </div>
                        </div>
                        <div class="chart-content">
                            @if ($genderTotals && $genderTotals->count() > 0)
                                @foreach ($genderTotals as $index => $gender)
                                    @php
                                        $genderValue = is_object($gender) ? $gender->gender : $gender['gender'];
                                    @endphp
                                    <div class="chart-item">
                                        <div class="bar-container">
                                            <div class="bar-label">
                                                <span>
                                                    @if ($genderValue == 'Masculino')
                                                        <i class="bi bi-gender-male text-primary me-1"></i>
                                                    @elseif($genderValue == 'Femenino')
                                                        <i class="bi bi-gender-female text-danger me-1"></i>
                                                    @else
                                                        <i class="bi bi-gender-ambiguous text-secondary me-1"></i>
                                                    @endif
                                                    {{ $genderValue }}
                                                </span>
                                                <strong>{{ is_object($gender) ? $gender->confirmed : $gender['confirmed'] }}/{{ is_object($gender) ? $gender->total : $gender['total'] }}</strong>
                                            </div>
                                            <div class="progress-container">
                                                <div class="progress-bar chart-color-{{ $index % 2 == 0 ? '4' : '5' }}"
                                                    style="width: {{ is_object($gender) ? $gender->percentage : $gender['percentage'] }}%">
                                                </div>
                                                <div class="value-label"
                                                    style="left: {{ is_object($gender) ? $gender->percentage : $gender['percentage'] }}%">
                                                    {{ is_object($gender) ? $gender->percentage : $gender['percentage'] }}%
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted py-3">
                                    <i class="bi bi-inbox"></i>
                                    <div>No hay datos disponibles</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tab Cronología -->
                <div class="tab-pane fade p-2" id="nav-timeline" role="tabpanel" aria-labelledby="nav-timeline-tab">

                    <!-- Gráfico de líneas por horas -->
                    <div class="card stat-chart mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="card-title mb-0">
                                    <i class="bi bi-graph-up me-2"></i>Confirmaciones por Hora
                                </h6>
                            </div>

                            <div class="d-flex align-items-center mb-3">
                                <span class="text-secondary fw-bold me-2">
                                    <i class="bi bi-funnel me-1"></i> Período:
                                </span>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button"
                                        class="btn {{ $selectedDateRange == 'today' ? 'btn-primary' : 'btn-outline-primary' }}"
                                        wire:click="changeDateRange('today')">
                                        <i class="bi bi-calendar-day"></i>
                                        <span class="d-none d-sm-inline ms-1">Hoy</span>
                                    </button>
                                    <button type="button"
                                        class="btn {{ $selectedDateRange == 'week' ? 'btn-primary' : 'btn-outline-primary' }}"
                                        wire:click="changeDateRange('week')">
                                        <i class="bi bi-calendar-week"></i>
                                        <span class="d-none d-sm-inline ms-1">Semana</span>
                                    </button>
                                    <button type="button"
                                        class="btn {{ $selectedDateRange == 'month' ? 'btn-primary' : 'btn-outline-primary' }}"
                                        wire:click="changeDateRange('month')">
                                        <i class="bi bi-calendar-month"></i>
                                        <span class="d-none d-sm-inline ms-1">Mes</span>
                                    </button>
                                </div>
                            </div>

                            <div id="hourlyConfirmationsChart"></div>

                        </div>
                    </div>

                    <!-- Indicadores de tendencia -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="card stat-chart border-0 bg-light">
                                <div class="card-body p-2 text-center">
                                    <div class="h5 mb-1 text-primary">{{ $confirmationTrend['this_week'] }}</div>
                                    <small class="text-muted">Esta semana</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card stat-chart border-0 bg-light">
                                <div class="card-body p-2 text-center">
                                    <div
                                        class="h5 mb-1 {{ $confirmationTrend['trend'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        @if ($confirmationTrend['trend'] >= 0)
                                            <i class="bi bi-arrow-up"></i>
                                        @else
                                            <i class="bi bi-arrow-down"></i>
                                        @endif
                                        {{ $confirmationTrend['trend'] > 0 ? '+' : '' }}{{ $confirmationTrend['trend'] }}%
                                    </div>
                                    <small class="text-muted">Tendencia</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información adicional -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card stat-chart">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        <small class="text-muted">
                            Última actualización: {{ now('America/Caracas')->format('d/m/Y H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
    @parent

    <script>
        let hourlyConfirmationsChart = null;

        function initHourlyConfirmationsChart(hourlyConfirmationsData, timeInfo = null) {
            const chartElement = document.querySelector("#hourlyConfirmationsChart");
            if (!chartElement) return;

            if (!hourlyConfirmationsData || hourlyConfirmationsData.length === 0) {
                chartElement.innerHTML =
                    '<div class="text-center p-4"><p class="text-muted">No hay datos disponibles</p></div>';
                return;
            }

            // Determinar la hora límite (ahora viene del servidor con zona horaria ajustada)
            const currentHour = new Date().getHours(); // Hora local del navegador
            const maxHour = timeInfo && timeInfo.hour_limit !== null ? timeInfo.hour_limit : currentHour;

            // Crear array de horas hasta la hora límite
            const fullHourData = [];
            for (let i = 0; i <= maxHour; i++) {
                const existingData = hourlyConfirmationsData.find(item => parseInt(item.hour) === i);
                fullHourData.push({
                    hour: i,
                    count: existingData ? existingData.count : 0
                });
            }

            // Crear etiquetas con fecha y hora
            const hourLabels = fullHourData.map(item => {
                const hour = item.hour.toString().padStart(2, '0');
                const today = new Date();
                const dateStr = today.toLocaleDateString('es-VE', {
                    day: '2-digit',
                    month: '2-digit'
                });
                return `${dateStr} ${hour}:00`;
            });
            const hourValues = fullHourData.map(item => item.count);

            const options = {
                series: [{
                    name: 'Confirmaciones',
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
                    formatter: function(val) {
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
                        text: 'Fecha y Hora'
                    },
                    tickAmount: Math.min(hourLabels.length, 8),
                    labels: {
                        rotate: -45,
                        style: {
                            fontSize: '10px'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Número de Confirmaciones'
                    },
                    min: 0,
                    forceNiceScale: true
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + " confirmaciones"
                        }
                    },
                    x: {
                        formatter: function(val, opts) {
                            return hourLabels[opts.dataPointIndex] + " (Hora local Venezuela)";
                        }
                    }
                },
                colors: ['#28a745'],
                annotations: timeInfo && timeInfo.hour_limit < 23 ? {
                    xaxis: [{
                        x: hourLabels.length - 1,
                        borderColor: '#FF4560',
                        label: {
                            borderColor: '#FF4560',
                            style: {
                                color: '#fff',
                                background: '#FF4560',
                            },
                            text: 'Hora actual: ' + (timeInfo.current_time || '')
                        }
                    }]
                } : {}
            };

            if (hourlyConfirmationsChart) {
                hourlyConfirmationsChart.destroy();
            }

            chartElement.innerHTML = '';
            hourlyConfirmationsChart = new ApexCharts(chartElement, options);
            hourlyConfirmationsChart.render();
        }

        // Función para inicializar cuando el DOM esté listo
        function initializeChart() {
            const timeInfo = {
                hour_limit: {{ now('America/Caracas')->hour }},
                timezone: 'America/Caracas',
                current_time: '{{ now('America/Caracas')->format('H:i') }}'
            };
            initHourlyConfirmationsChart(@json($hourlyConfirmations), timeInfo);
        }

        // Inicializar el chart cuando el componente se carga
        document.addEventListener('DOMContentLoaded', function() {
            initializeChart();
        });

        // Para Livewire 2.x
        document.addEventListener('livewire:load', function() {
            initializeChart();

            Livewire.on('hourlyConfirmationsUpdated', (data, timeInfo) => {
                initHourlyConfirmationsChart(data, timeInfo);
            });

            Livewire.on('dataRefreshed', () => {
                console.log('Datos de prosecución actualizados');
            });
        });

        // Para Livewire 3.x (por si acaso)
        document.addEventListener('livewire:navigated', function() {
            initializeChart();
        });

        // Reinicializar cuando se cambie a la pestaña de cronología
        document.addEventListener('shown.bs.tab', function(event) {
            if (event.target.getAttribute('aria-controls') === 'nav-timeline') {
                setTimeout(() => {
                    initializeChart();
                }, 100);
            }
        });
    </script>

@endsection
