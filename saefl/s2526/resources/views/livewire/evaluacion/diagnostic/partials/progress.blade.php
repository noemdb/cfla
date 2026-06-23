<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Progreso por Área de Formación</h6>
                <div class="dropdown no-arrow">
                    <select wire:model="selectedPensum" class="form-control form-control-sm" style="width: auto;">
                        <option value="">Todas las Áreas</option>
                        @foreach($pensums as $pensum)
                            <option value="{{ $pensum->id }}">{{ $pensum->pensum }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-body">
                @if($pensumProgress && $pensumProgress->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Área de Formación</th>
                                    <th>Total Preguntas</th>
                                    <th>Sesiones Iniciadas</th>
                                    <th>Sesiones Completadas</th>
                                    <th>% Finalización</th>
                                    <th>Progreso Visual</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                @foreach($pensumProgress as $progress)
                                <tr>
                                    <td>
                                        <strong>{{ $progress->full_name ?? $progress->pensum }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary badge-pill">{{ $progress->total_questions ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info badge-pill">{{ $progress->total_sessions ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success badge-pill">{{ $progress->completed_sessions ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <!-- Added null safety and proper calculation display -->
                                        @php
                                            $completionPercentage = $progress->completion_percentage ?? 0;
                                            $completionPercentage = is_numeric($completionPercentage) ? $completionPercentage : 0;
                                        @endphp
                                        <strong>{{ number_format($completionPercentage, 1) }}%</strong>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar
                                                @if($completionPercentage >= 80) bg-success
                                                @elseif($completionPercentage >= 60) bg-info
                                                @elseif($completionPercentage >= 40) bg-warning
                                                @else bg-danger
                                                @endif"
                                                role="progressbar"
                                                style="width: {{ $completionPercentage }}%"
                                                aria-valuenow="{{ $completionPercentage }}"
                                                aria-valuemin="0"
                                                aria-valuemax="100">
                                                {{ number_format($completionPercentage, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted">No hay datos de progreso disponibles para mostrar.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Progress Summary Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Áreas Completadas (≥80%)
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <!-- Added null safety for collection operations -->
                            {{ $pensumProgress ? $pensumProgress->where('completion_percentage', '>=', 80)->count() : 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            En Progreso (40-79%)
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <!-- Fixed range calculation for progress cards -->
                            {{ $pensumProgress ? $pensumProgress->filter(function($item) { 
                                return $item->completion_percentage >= 40 && $item->completion_percentage < 80; 
                            })->count() : 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Inicio Lento (<40%)
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $pensumProgress ? $pensumProgress->where('completion_percentage', '<', 40)->count() : 0 }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Promedio General
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <!-- Added proper null safety and calculation for average -->
                            @php
                                $averageCompletion = $pensumProgress && $pensumProgress->count() > 0 
                                    ? $pensumProgress->avg('completion_percentage') 
                                    : 0;
                                $averageCompletion = is_numeric($averageCompletion) ? $averageCompletion : 0;
                            @endphp
                            {{ number_format($averageCompletion, 1) }}%
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
