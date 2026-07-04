{{-- /home/nuser/code/s2526/resources/views/livewire/academico/diagnostic/partials/progress.blade.php --}}
<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card border-left-primary shadow h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Tasa de Finalización
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $stats['completion_rate'] }}%
                        </div>
                        <div class="progress mt-2" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: {{ $stats['completion_rate'] }}%"></div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card border-left-success shadow h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Sesiones Activas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $stats['total_sessions'] - $stats['completed_sessions'] }}
                        </div>
                        <div class="text-xs text-muted">
                            De {{ $stats['total_sessions'] }} totales
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-play-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card border-left-info shadow h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Participación
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $stats['unique_students'] }} estudiantes
                        </div>
                        <div class="text-xs text-muted">
                            {{ $stats['total_diagnostics'] > 0 ? round($stats['unique_students'] / $stats['total_diagnostics'], 1) : 0 }} por diagnóstico
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-2">
        <h5 class="mb-0 font-weight-bold text-primary">
            <i class="fas fa-chart-bar me-2"></i> Distribución por Estado
        </h5>
    </div>
    <div class="card-body">
        <canvas id="progressChart" height="100"></canvas>
        <script>
            document.addEventListener('livewire:load', function() {
                const ctx = document.getElementById('progressChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Completadas', 'En progreso', 'Sin iniciar'],
                        datasets: [{
                            data: [
                                {{ $stats['completed_sessions'] }},
                                {{ $stats['total_sessions'] - $stats['completed_sessions'] }},
                                {{ max(0, ($stats['unique_students'] * $stats['total_diagnostics']) - $stats['total_sessions']) }}
                            ],
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.6)',
                                'rgba(255, 206, 86, 0.6)',
                                'rgba(255, 99, 132, 0.6)'
                            ],
                            borderColor: [
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(255, 99, 132, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        }
                    }
                });
            });
        </script>
    </div>
</div>