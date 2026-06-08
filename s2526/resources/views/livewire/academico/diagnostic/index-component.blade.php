{{-- /home/nuser/code/s2526/resources/views/livewire/academico/diagnostic/index-component.blade.php --}}
<div>
    <div class="container-fluid py-3">
        <!-- Loading Overlay -->
        @if($isLoading)
            <div class="overlay d-flex align-items-center justify-content-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        @endif

        <!-- Filtros -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white py-2">
                <h6 class="mb-0">
                    <i class="fas fa-filter"></i> Filtros de Diagnóstico
                </h6>
            </div>
            <div class="card-body py-2">
                <div class="row g-2">

                    <!-- Filtro de Diagnóstico -->
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Diagnóstico</label>
                        <select wire:model="filterDiagMainId" class="form-control form-control-sm border-0 bg-transparent font-weight-bold text-dark">
                            <option value="">Todos los diagnósticos</option>
                            @foreach($diagMains as $diagMain)
                                <option value="{{ $diagMain->id }}">{{ $diagMain->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtro de Grado -->
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Grado</label>
                        <select wire:model="filterGradoId" id="filterGradoId"
                            class="form-control form-control-sm border-0 bg-transparent font-weight-bold text-dark"
                            style="box-shadow: none;">
                            <option value="">Todos los grados</option>
                            @foreach ($list_grados as $pestudio => $grados)
                                <optgroup label="{{ $pestudio }}">
                                    @foreach ($grados as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div> 

                    <!-- Botones -->
                    <div class="col-md-2 d-flex align-items-end">
                        <button wire:click="resetFilters" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="fas fa-redo"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas Generales -->
        @if($activeTab === 'dashboard')
            @include('livewire.academico.diagnostic.partials.stats-cards')
        @endif

        <!-- Pestañas -->
        <div class="card shadow-sm">
            <div class="card-header bg-light py-2">
                <ul class="nav nav-tabs nav-fill card-header-tabs border-0">
                    <li class="nav-item">
                        <button class="nav-link {{ $activeTab === 'dashboard' ? 'active' : '' }} py-2" 
                                wire:click="setActiveTab('dashboard')">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link {{ $activeTab === 'diagmains' ? 'active' : '' }} py-2" 
                                wire:click="setActiveTab('diagmains')">
                            <i class="fas fa-clipboard-list"></i> Diagnósticos
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link {{ $activeTab === 'sessions' ? 'active' : '' }} py-2" 
                                wire:click="setActiveTab('sessions')">
                            <i class="fas fa-users"></i> Sesiones
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link {{ $activeTab === 'progress' ? 'active' : '' }} py-2" 
                                wire:click="setActiveTab('progress')">
                            <i class="fas fa-chart-line"></i> Progreso
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-2">
                <!-- Dashboard -->
                @if($activeTab === 'dashboard')
                    @include('livewire.academico.diagnostic.partials.dashboard')
                
                <!-- Diagnósticos -->
                @elseif($activeTab === 'diagmains')
                    @include('livewire.academico.diagnostic.partials.diagmains')
                
                <!-- Sesiones -->
                @elseif($activeTab === 'sessions')
                    @include('livewire.academico.diagnostic.partials.sessions')
                
                <!-- Progreso -->
                @elseif($activeTab === 'progress')
                    @include('livewire.academico.diagnostic.partials.progress')
                @endif
            </div>
        </div>
    </div>

    <!-- Modal de Detalles del Diagnóstico -->
    @if($showDiagnosticDetails && $selectedDiagnostic)
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white py-2">
                        <h5 class="modal-title">
                            {{ $selectedDiagnostic->name }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" 
                                wire:click="closeDiagnosticDetails"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-primary">Información General</h6>
                                <ul class="list-group list-group-flush small">
                                    <li class="list-group-item px-0 py-1">
                                        <strong>Descripción:</strong> {{ $selectedDiagnostic->description }}
                                    </li>
                                    <li class="list-group-item px-0 py-1">
                                        <strong>Token:</strong> <code>{{ $selectedDiagnostic->token }}</code>
                                    </li>
                                    <li class="list-group-item px-0 py-1">
                                        <strong>Estado:</strong>
                                        @if($selectedDiagnostic->active)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary">Inactivo</span>
                                        @endif
                                    </li>
                                    @if($selectedDiagnostic->referent)
                                        <li class="list-group-item px-0 py-1">
                                            <strong>Referente:</strong> {{ $selectedDiagnostic->referent->name }}
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="text-primary">Estadísticas</h6>
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <div class="card bg-light">
                                            <div class="card-body p-2 text-center">
                                                <h4 class="text-primary mb-0">{{ $selectedDiagnosticStats['total_sessions'] }}</h4>
                                                <small class="text-muted">Sesiones</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <div class="card bg-light">
                                            <div class="card-body p-2 text-center">
                                                <h4 class="text-success mb-0">{{ $selectedDiagnosticStats['completed_sessions'] }}</h4>
                                                <small class="text-muted">Completadas</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <div class="card bg-light">
                                            <div class="card-body p-2 text-center">
                                                <h4 class="text-info mb-0">{{ $selectedDiagnosticStats['unique_students'] }}</h4>
                                                <small class="text-muted">Estudiantes</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <div class="card bg-light">
                                            <div class="card-body p-2 text-center">
                                                <h4 class="text-warning mb-0">{{ $selectedDiagnosticStats['total_questions'] }}</h4>
                                                <small class="text-muted">Preguntas</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progreso -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Tasa de finalización:</span>
                                <strong>{{ $selectedDiagnosticStats['completion_rate'] }}%</strong>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" 
                                     style="width: {{ $selectedDiagnosticStats['completion_rate'] }}%"></div>
                            </div>
                        </div>

                        <!-- Áreas Evaluadas -->
                        @if($selectedDiagnostic->questions->count() > 0)
                            <div class="mt-3">
                                <h6 class="text-primary mb-2">Áreas Evaluadas</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Área</th>
                                                <th class="text-center">Preguntas</th>
                                                <th class="text-center">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($selectedDiagnostic->questions->groupBy('pensum_id') as $pensumId => $questions)
                                                @php
                                                    $pensum = $questions->first()->pensum ?? null;
                                                @endphp
                                                @if($pensum)
                                                    <tr>
                                                        <td>{{ $pensum->name }}</td>
                                                        <td class="text-center">{{ $questions->count() }}</td>
                                                        <td class="text-center">
                                                            @if($pensum->status_active)
                                                                <span class="badge bg-success">Activa</span>
                                                            @else
                                                                <span class="badge bg-secondary">Inactiva</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm" 
                                wire:click="closeDiagnosticDetails">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>

<style>
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8);
        z-index: 9999;
    }
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
    }
    .nav-tabs .nav-link.active {
        border-bottom: 3px solid #007bff;
        color: #007bff;
    }
    .progress {
        border-radius: 5px;
    }
</style>