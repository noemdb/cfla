{{-- /home/nuser/code/s2526/resources/views/livewire/academico/diagnostic/partials/dashboard.blade.php --}}
<div class="row">
    <!-- Gráficos -->
    <div class="col-md-8">
        <div class="card shadow mb-3">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line mr-2"></i> Progreso de Diagnósticos
                </h6>
            </div>
            <div class="card-body">
                <canvas id="diagnosticsChart" height="150"></canvas>
                <script>
                    document.addEventListener('livewire:load', function() {
                        const ctx = document.getElementById('diagnosticsChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: ['Activos', 'Inactivos', 'Completados', 'En Progreso'],
                                datasets: [{
                                    label: 'Cantidad',
                                    data: [
                                        {{ $stats['active_diagnostics'] }},
                                        {{ $stats['total_diagnostics'] - $stats['active_diagnostics'] }},
                                        {{ $stats['completed_sessions'] }},
                                        {{ $stats['total_sessions'] - $stats['completed_sessions'] }}
                                    ],
                                    backgroundColor: [
                                        'rgba(54, 162, 235, 0.6)',
                                        'rgba(255, 99, 132, 0.6)',
                                        'rgba(75, 192, 192, 0.6)',
                                        'rgba(255, 206, 86, 0.6)'
                                    ],
                                    borderColor: [
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(255, 206, 86, 1)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1
                                        }
                                    }
                                }
                            }
                        });
                    });
                </script>
            </div>
        </div>
    </div>

    <!-- Información Rápida -->
    <div class="col-md-4">
        <div class="card shadow mb-3">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle mr-2"></i> Información Rápida
                </h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item px-0 py-2 border-0">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Tasa de participación:</span>
                            <strong>{{ $stats['completion_rate'] }}%</strong>
                        </div>
                        <div class="progress mt-1" style="height: 5px;">
                            <div class="progress-bar bg-success" style="width: {{ $stats['completion_rate'] }}%"></div>
                        </div>
                    </div>
                    <div class="list-group-item px-0 py-2 border-0">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Promedio por diagnóstico:</span>
                            <strong>{{ $stats['total_diagnostics'] > 0 ? round($stats['total_sessions'] / $stats['total_diagnostics'], 1) : 0 }}</strong>
                        </div>
                    </div>
                    <div class="list-group-item px-0 py-2 border-0">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Preguntas por área:</span>
                            <strong>{{ $stats['total_diagnostics'] > 0 ? round($stats['total_questions'] / $stats['total_diagnostics'], 1) : 0 }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Últimos Diagnósticos -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 font-weight-bold text-primary">
                    <i class="fas fa-history mr-2"></i> Últimos Diagnósticos
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-3 py-2">Diagnóstico</th>
                                <th class="px-3 py-2 text-center">Sesiones</th>
                                <th class="px-3 py-2 text-center">Completadas</th>
                                <th class="px-3 py-2 text-center">Estudiantes</th>
                                <th class="px-3 py-2 text-center">Estado</th>
                                <th class="px-3 py-2 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($diagnostics->take(5) as $diagnostic)
                                <tr>
                                    <td class="px-3 py-2">
                                        <strong>{{ $diagnostic->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($diagnostic->description, 50) }}</small>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <span class="badge bg-primary">{{ $diagnostic->sessions_count ?? 0 }}</span>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <span class="badge bg-success">{{ $diagnostic->completed_sessions_count ?? 0 }}</span>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <span class="badge bg-info">{{ $diagnostic->unique_students_count ?? 0 }}</span>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        @if($diagnostic->active)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <button class="btn btn-sm btn-outline-primary"
                                                wire:click="showDiagnosticDetails({{ $diagnostic->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        No hay diagnósticos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>