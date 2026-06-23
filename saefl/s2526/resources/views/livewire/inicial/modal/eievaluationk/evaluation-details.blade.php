@php
    $evaluation = \App\Models\app\Inicial\Eievaluationk::with(['grado', 'seccion', 'lapso', 'profesor'])->find($editingId);
    $positions = $evaluation ? $evaluation->getOrderedEvaluationps() : collect();
@endphp

@if($evaluation)
<div class="container-fluid">
    <!-- Plan Information -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-secondary">
                <div class="card-header alert-secondary text-dark">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-info-circle mr-2"></i>Información General del Plan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong class="text-primary">Grado/Año:</strong>
                                <span class="ml-2">{{ $evaluation->grado->name }}</span>
                            </div>
                            <div class="mb-3">
                                <strong class="text-primary">Sección:</strong>
                                <span class="ml-2">{{ $evaluation->seccion->name }}</span>
                            </div>
                            <div class="mb-3">
                                <strong class="text-primary">Profesor:</strong>
                                <span class="ml-2">{{ $evaluation->profesor->fullname ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong class="text-primary">Fecha Inicial:</strong>
                                <span class="ml-2">{{ \Carbon\Carbon::parse($evaluation->finicial)->format('d/m/Y') }}</span>
                            </div>
                            <div class="mb-3">
                                <strong class="text-primary">Fecha Final:</strong>
                                <span class="ml-2">{{ \Carbon\Carbon::parse($evaluation->ffinal)->format('d/m/Y') }}</span>
                            </div>
                            <div class="mb-3">
                                <strong class="text-primary">Momento:</strong>
                                <span class="ml-2">{{ $evaluation->lapso->name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Observations and Recommendations -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-clipboard-list mr-2"></i>Observaciones y Recomendaciones
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="font-weight-bold text-success">
                            <i class="fas fa-user-graduate mr-1"></i>Observaciones del Docente:
                        </h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-0 text-justify">{{ $evaluation->observaciones }}</p>
                        </div>
                    </div>
                    
                    @if($evaluation->recomendacion)
                        <div class="mb-3">
                            <h6 class="font-weight-bold text-info">
                                <i class="fas fa-lightbulb mr-1"></i>Recomendación del Coordinador de Evaluación:
                            </h6>
                            <div class="bg-light p-3 rounded border-left border-info">
                                <p class="mb-0 text-justify">{{ $evaluation->recomendacion }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <h6 class="font-weight-bold text-warning">
                            <i class="fas fa-users mr-1"></i>Control de Asistencia:
                        </h6>
                        <div class="bg-light p-3 rounded border-left border-warning">
                            <p class="mb-0 text-justify">{{ $evaluation->asistencia }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-info h-100">
                <div class="card-body text-center">
                    <i class="fas fa-list fa-2x text-info mb-2"></i>
                    <h4 class="text-info font-weight-bold">{{ $positions->count() }}</h4>
                    <p class="text-muted mb-0">Posiciones Registradas</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-warning h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-alt fa-2x text-warning mb-2"></i>
                    <h4 class="text-warning font-weight-bold">
                        {{ \Carbon\Carbon::parse($evaluation->finicial)->diffInDays(\Carbon\Carbon::parse($evaluation->ffinal)) + 1 }}
                    </h4>
                    <p class="text-muted mb-0">Días de Evaluación</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Positions -->
    @if($positions->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0 font-weight-bold">
                            <i class="fas fa-list mr-2"></i>Posiciones de Evaluación ({{ $positions->count() }} elementos)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="60">Orden</th>
                                        <th>Área/Componente</th>
                                        <th>Aprendizaje Alcanzado</th>
                                        <th>Niños Evaluados</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($positions as $position)
                                        <tr>
                                            <td>
                                                <span class="badge badge-info">{{ $position->order ?? 'S/N' }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong class="text-primary">{{ $position->pevaluacion->pensum->asignatura->name ?? 'N/A' }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $position->componente ?? 'Sin componente' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <small>{{ $position->aprendizaje_alcanzado ?? 'No especificado' }}</small>
                                            </td>
                                            <td>
                                                <small>{{ $position->nombre_ninos ?? 'No especificado' }}</small>
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
    @endif

    <!-- Actions -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary" 
                            wire:click="openModal('edit', {{ $evaluation->id }})">
                        <i class="fas fa-edit mr-1"></i>Editar Plan
                    </button>
                    <button type="button" class="btn btn-info" 
                            wire:click="openModal('position', {{ $evaluation->id }})">
                        <i class="fas fa-list mr-1"></i>Gestionar Posiciones
                    </button>
                </div>
                
                <div class="btn-group">
                    <a href="#" target="_blank" class="btn btn-dark">
                        <i class="fas fa-file-pdf mr-1"></i>Generar PDF
                    </a>
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">
                        <i class="fas fa-times mr-1"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@else
    <div class="text-center py-4">
        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
        <h5 class="text-muted">Plan no encontrado</h5>
        <p class="text-muted">El plan solicitado no existe o ha sido eliminado.</p>
        <button type="button" class="btn btn-secondary" wire:click="closeModal">
            <i class="fas fa-times mr-1"></i>Cerrar
        </button>
    </div>
@endif
