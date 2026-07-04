<div class="row">
    <!-- Response Trends Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Tendencia de Respuestas</h6>
                <div class="dropdown no-arrow">
                    <select wire:model="dateRange" class="form-control form-control-sm" style="width: auto;">
                        <option value="7">7 días</option>
                        <option value="30">30 días</option>
                        <option value="90">90 días</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="responseTrendsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Responses by Area -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Respuestas por Área</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="responsesByAreaChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    @foreach($responseStats['responses_by_pensum'] as $index => $area)
                        <span class="mr-2">
                            <i class="fas fa-circle" style="color: {{ ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'][$index % 5] }}"></i>
                            {{ $area->pensum }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Analytics Table -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Análisis Detallado por Área de Formación</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Área de Formación</th>
                                <th>Preguntas Activas</th>
                                <th>Respuestas Totales</th>
                                <th>Promedio por Pregunta</th>
                                <th>Sesiones Completadas</th>
                                <th>Tasa de Abandono</th>
                                <th>Rendimiento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pensumProgress as $progress)
                            @php
                                $abandonRate = $progress->total_sessions > 0 ?
                                    (($progress->total_sessions - $progress->completed_sessions) / $progress->total_sessions) * 100 : 0;
                                $avgResponsesPerQuestion = $progress->total_questions > 0 ?
                                    ($responseStats['responses_by_pensum']->where('pensum', $progress->pensum)->first()->count ?? 0) / $progress->total_questions : 0;
                            @endphp
                            <tr>
                                <td><strong>{{ $progress->full_name }}</strong></td>
                                <td>
                                    <span class="badge badge-primary">{{ $progress->total_questions }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $responseStats['responses_by_pensum']->where('pensum', $progress->pensum)->first()->count ?? 0 }}
                                    </span>
                                </td>
                                <td>{{ number_format($avgResponsesPerQuestion, 1) }}</td>
                                <td>
                                    <span class="badge badge-success">{{ $progress->completed_sessions }}</span>
                                </td>
                                <td>
                                    <span class="badge
                                        @if($abandonRate <= 20) badge-success
                                        @elseif($abandonRate <= 40) badge-warning
                                        @else badge-danger
                                        @endif">
                                        {{ number_format($abandonRate, 1) }}%
                                    </span>
                                </td>
                                <td>
                                    @if($progress->completion_percentage >= 80)
                                        <span class="badge badge-success">Excelente</span>
                                    @elseif($progress->completion_percentage >= 60)
                                        <span class="badge badge-info">Bueno</span>
                                    @elseif($progress->completion_percentage >= 40)
                                        <span class="badge badge-warning">Regular</span>
                                    @else
                                        <span class="badge badge-danger">Necesita Atención</span>
                                    @endif
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

@section('scripts')
@parent
<script>
let responseTrendsChart = null;
let responsesByAreaChart = null;

function initializeAnalyticsCharts() {
    // Destroy existing charts if they exist
    if (responseTrendsChart) {
        responseTrendsChart.destroy();
    }
    if (responsesByAreaChart) {
        responsesByAreaChart.destroy();
    }

    // Response Trends Chart
    const ctx3 = document.getElementById("responseTrendsChart");
    if (ctx3) {
        responseTrendsChart = new Chart(ctx3, {
            type: 'line',
            data: {
                labels: [@foreach($responseStats['daily_responses'] as $day) "{{ \Carbon\Carbon::parse($day->date)->format('d/m') }}", @endforeach],
                datasets: [{
                    label: "Respuestas",
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: [@foreach($responseStats['daily_responses'] as $day) {{ $day->count }}, @endforeach],
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    xAxes: [{
                        time: {
                            unit: 'date'
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }],
                },
                legend: {
                    display: false
                },
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10,
                }
            }
        });
    }

    // Responses by Area Chart
    const ctx4 = document.getElementById("responsesByAreaChart");
    if (ctx4) {
        responsesByAreaChart = new Chart(ctx4, {
            type: 'doughnut',
            data: {
                labels: [@foreach($responseStats['responses_by_pensum'] as $area) "{{ $area->pensum }}", @endforeach],
                datasets: [{
                    data: [@foreach($responseStats['responses_by_pensum'] as $area) {{ $area->count }}, @endforeach],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#f4b619', '#c0392b'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: false
                },
                cutoutPercentage: 80,
            },
        });
    }
}

// Initialize charts on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeAnalyticsCharts();
});

// Reinitialize charts after Livewire updates
document.addEventListener('livewire:load', function() {
    initializeAnalyticsCharts();
});

document.addEventListener('livewire:update', function() {
    // Small delay to ensure DOM is updated
    setTimeout(function() {
        initializeAnalyticsCharts();
    }, 100);
});
</script>
@endsection
