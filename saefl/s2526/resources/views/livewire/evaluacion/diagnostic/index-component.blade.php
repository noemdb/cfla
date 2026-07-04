<div>

    <div class="continer-fluid">
        <div class="row mb-3">

            <div class="col-12">
                <div class="form-group border rounded p-2 bg-light shadow-sm">
                    <div class="row">
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


    {{-- Navigation Tabs --}}
    <div class="row mb-3">
        <div class="col-12">
            <ul class="nav nav-tabs nav-fill" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab == 'dashboard' ? 'active' : '' }} position-relative"
                        wire:click="setActiveTab('dashboard')" href="#" role="tab">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                        {{-- Removed loading spinner from dashboard tab --}}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab == 'questions' ? 'active' : '' }} position-relative"
                        wire:click="setActiveTab('questions')" href="#" role="tab">
                        <i class="fas fa-question-circle"></i> Preguntas
                        {{-- Removed loading spinner from questions tab --}}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab == 'sessions' ? 'active' : '' }} position-relative"
                        wire:click="setActiveTab('sessions')" href="#" role="tab">
                        <i class="fas fa-users"></i> Sesiones
                        {{-- Removed loading spinner from sessions tab --}}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab == 'diagmains' ? 'active' : '' }} position-relative"
                        wire:click="setActiveTab('diagmains')" href="#" role="tab">
                        <i class="fas fa-folder-open"></i> Diagnósticos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab == 'sections' ? 'active' : '' }} position-relative"
                        wire:click="setActiveTab('sections')" href="#" role="tab">
                        <i class="fas fa-layer-group"></i> Secciones
                    </a>
                </li>

            </ul>
        </div>
    </div>

    {{-- Tab Content --}}
    <div class="tab-content position-relative border border-top-0">
        {{-- Dashboard Tab --}}
        @if ($activeTab == 'dashboard')
            <div class="">
                @include('livewire.evaluacion.diagnostic.partials.dashboard')
            </div>
        @endif

        {{-- Questions Tab --}}
        @if ($activeTab == 'questions')
            <div class="p-1">
                @include('livewire.evaluacion.diagnostic.partials.questions')
            </div>
        @endif

        {{-- Sessions Tab --}}
        @if ($activeTab == 'sessions')
            <div class="p-1">
                @include('livewire.evaluacion.diagnostic.partials.sessions')
            </div>
        @endif

        {{-- Diagnosticos Tab --}}
        @if ($activeTab == 'diagmains')
            <div class="p-1">
                @include('livewire.evaluacion.diagnostic.partials.diagmains')
            </div>
        @endif

        {{-- Secciones Tab --}}
        @if ($activeTab == 'sections')
            <div class="p-1">
                @include('livewire.evaluacion.diagnostic.partials.sections')
            </div>
        @endif

    </div>


    <!-- Modales de Reportes -->
    @include('livewire.evaluacion.diagnostic.partials.ai-report-modal')
    @include('livewire.evaluacion.diagnostic.partials.section-report-modal')

    <!-- Indicador de carga global para cualquier acción -->
    <div wire:loading.flex style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
        <div class="alert alert-light shadow-lg">
            <div class="flex items-center font-weight-bold text-dark">
                <div class="spinner-border spinner-border-sm mr-2"></div>
                <span>Procesando...</span>
            </div>
        </div>
    </div>

    @section('scripts')
        @parent
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.addEventListener('swal', event => {
                    Swal.fire(event.detail);
                });

                window.addEventListener('show-notification', event => {
                    Swal.fire({
                        icon: event.detail.type,
                        title: event.detail.type === 'error' ? 'Error' : 'Notificación',
                        text: event.detail.message,
                        timer: 3000,
                        showConfirmButton: false
                    });
                });
            });
        </script>
    @endsection
</div>
