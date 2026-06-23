<div class="diagnostics-system">
    <!-- Header -->
    <div class="bg-white shadow-sm border-bottom mb-4">
        <div class="container-fluid py-3">
            <div class="row">
                <div class="col">
                    <h1 class="h4 mb-0 text-primary">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        {{ $profesor->fullname ?? null }}
                    </h1>
                </div>
            </div>

            <div class="row align-items-center">                
                <div class="col">
                    @if ($profesor && $profesor->pensums->isNotEmpty())
                        <div class="form-group mb-0 mr-3 d-inline-block">
                            <label class="form-label mb-1 text-muted small">Área de Formación:</label>
                            <select wire:model="selectedPensumId" class="form-control form-control-sm">
                                <option value="">Todas las áreas</option>
                                @foreach ($profesor->pensums as $pensum)
                                    <option value="{{ $pensum->id }}">{{ $pensum->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    {{-- <button wire:click="openQuestionModal" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>
                        Nueva Pregunta
                    </button> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="container-fluid mb-3">
        <div class="row">
            <div class="col-12">
                <div class="form-group border rounded p-2 bg-light shadow-sm">
                    <div class="row">
                        <!-- Diagnostic Filter -->
                        <div class="col-md-4">
                            <label for="filterDiagMainId" class="font-weight-bold text-muted small text-uppercase mb-1">
                                <i class="fas fa-filter text-secondary mr-1"></i> Filtrar Diagnóstico
                            </label>
                            <div class="input-group">
                                <select wire:model="filterDiagMainId" id="filterDiagMainId"
                                    class="form-control form-control-sm border-0 bg-transparent font-weight-bold text-dark"
                                    style="box-shadow: none;">
                                    <option value="">Todos los Diagnósticos (Vista General)</option>
                                    @foreach ($diagMains as $main)
                                        <option value="{{ $main->id }}">{{ $main->name }}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    @if ($filterDiagMainId)
                                        <button wire:click="$set('filterDiagMainId', '')"
                                            class="btn btn-sm btn-link text-danger" type="button"
                                            title="Limpiar filtro">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Grado Filter -->
                        <div class="col-md-4 border-left">
                            <label for="filterGradoId" class="font-weight-bold text-muted small text-uppercase mb-1">
                                <i class="fas fa-graduation-cap text-secondary mr-1"></i> Filtrar Grado
                            </label>
                            <div class="input-group">
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
                                <div class="input-group-append">
                                    @if ($filterGradoId)
                                        <button wire:click="$set('filterGradoId', '')"
                                            class="btn btn-sm btn-link text-danger" type="button"
                                            title="Limpiar filtro">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Seccion Filter -->
                        <div class="col-md-4 border-left">
                            <label for="filterSeccionId" class="font-weight-bold text-muted small text-uppercase mb-1">
                                <i class="fas fa-users text-secondary mr-1"></i> Filtrar Sección
                            </label>
                            <div class="input-group">
                                <select wire:model="filterSeccionId" id="filterSeccionId"
                                    class="form-control form-control-sm border-0 bg-transparent font-weight-bold text-dark"
                                    style="box-shadow: none;" {{ empty($list_secciones) ? 'disabled' : '' }}>
                                    <option value="">Todas las secciones</option>
                                    @if (!empty($list_secciones))
                                        @foreach ($list_secciones as $seccion)
                                            <option value="{{ $seccion->id }}">{{ $seccion->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="input-group-append">
                                    @if ($filterSeccionId)
                                        <button wire:click="$set('filterSeccionId', '')"
                                            class="btn btn-sm btn-link text-danger" type="button"
                                            title="Limpiar filtro">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mejorado sistema de pestañas con Bootstrap 4.3, nav-fill y colores morado/fucsia -->
    <div class="container-fluid mb-4">
        <ul class="nav nav-tabs nav-fill" id="diagnosticsTab" role="tablist">
            <li class="nav-item">
                <button wire:click="setActiveTab('dashboard')"
                    class="nav-link {{ $activeTab === 'dashboard' ? 'active' : '' }}" id="dashboard-tab" type="button"
                    role="tab" aria-controls="dashboard"
                    aria-selected="{{ $activeTab === 'dashboard' ? 'true' : 'false' }}">
                    <i class="fas fa-chart-line mr-2"></i>
                    Dashboard
                </button>
            </li>
            <li class="nav-item">
                <button wire:click="setActiveTab('questions')"
                    class="nav-link {{ $activeTab === 'questions' ? 'active' : '' }}" id="questions-tab" type="button"
                    role="tab" aria-controls="questions"
                    aria-selected="{{ $activeTab === 'questions' ? 'true' : 'false' }}">
                    <i class="fas fa-question-circle mr-2"></i>
                    Preguntas
                </button>
            </li>
            <li class="nav-item">
                <button wire:click="setActiveTab('sessions')"
                    class="nav-link {{ $activeTab === 'sessions' ? 'active' : '' }}" id="sessions-tab" type="button"
                    role="tab" aria-controls="sessions"
                    aria-selected="{{ $activeTab === 'sessions' ? 'true' : 'false' }}">
                    <i class="fas fa-users mr-2"></i>
                    Sesiones
                </button>
            </li>
            <li class="nav-item">
                <button wire:click="setActiveTab('analytics')"
                    class="nav-link {{ $activeTab === 'analytics' ? 'active' : '' }}" id="analytics-tab"
                    type="button" role="tab" aria-controls="analytics"
                    aria-selected="{{ $activeTab === 'analytics' ? 'true' : 'false' }}">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Análisis
                </button>
            </li>
        </ul>
    </div>

    <!-- Content Area -->
    <div class="container-fluid">
        @if ($activeTab === 'dashboard')
            @include('livewire.profesor.diagnostics.partials.dashboard')
        @elseif($activeTab === 'questions')
            @include('livewire.profesor.diagnostics.partials.questions')
        @elseif($activeTab === 'sessions')
            @include('livewire.profesor.diagnostics.partials.sessions')
        @elseif($activeTab === 'analytics')
            @include('livewire.profesor.diagnostics.partials.analytics')
        @endif
    </div>

    <!-- Question Modal -->
    @include('livewire.profesor.diagnostics.partials.question-modal')

    <!-- Session Detail Modal -->
    @include('livewire.profesor.diagnostics.partials.session-modal')

    <!-- Loading Overlay -->
    <div wire:loading.flex class="position-fixed"
        style="top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
    </div>

    @section('scripts')
        @parent
        <script>
            document.addEventListener('livewire:load', function() {
                // SweetAlert integration
                Livewire.on('showAlert', function(data) {
                    Swal.fire({
                        icon: data.type,
                        title: data.title,
                        text: data.message,
                        confirmButtonColor: '#6f42c1'
                    });
                });

                Livewire.on('showConfirmation', function(questionId) {
                    console.log(questionId);
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "Esta acción no se puede deshacer",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Livewire.emit('deleteQuestion', questionId);
                        }
                    });
                });
            });
        </script>
    @endsection

    @section('stylesheet')
        @parent
        <!-- Agregados estilos CSS personalizados para pestañas morado/fucsia -->
        <style>
            /* Cambiando a colores sólidos morados y asegurando ancho completo */
            .nav-tabs {
                border-bottom: 2px solid #e9ecef;
                width: 100%;
            }

            .nav-tabs .nav-item {
                margin-bottom: -2px;
                flex: 1;
                /* Asegura que cada pestaña ocupe el mismo ancho */
            }

            .nav-tabs .nav-link {
                color: #6c757d;
                border: 1px solid transparent;
                border-top-left-radius: 0.25rem;
                border-top-right-radius: 0.25rem;
                transition: all 0.3s ease;
                font-weight: 500;
                text-align: center;
                width: 100%;
                /* Ocupa todo el ancho disponible */
            }

            .nav-tabs .nav-link:hover {
                color: #6f42c1;
                border-color: #e9ecef #e9ecef #dee2e6;
                background-color: #f8f9fa;
            }

            .nav-tabs .nav-link.active {
                color: #fff;
                background-color: #6f42c1;
                /* Color sólido morado */
                border-color: #6f42c1;
                box-shadow: 0 2px 4px rgba(111, 66, 193, 0.3);
            }

            .nav-tabs .nav-link.active:hover {
                color: #fff;
                background-color: #5a359a;
                /* Morado más oscuro en hover */
            }
        </style>
    @endsection

    {{-- AI Report Modal --}}
    @if ($SessionModalReport && $selectedReport)
        @include('livewire.profesor.diagnostics.partials.ai-report-modal')
    @endif
</div>
