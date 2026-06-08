<div>
    {{-- Encabezado --}}
    <div class="card card-primary mt-2">
        <div class="card-header pb-0 mb-0 alert-secondary">
            <div class="btn-group float-right pt-0 pb-2">
                @include('bienestars.matriculations.interviews.menus.index')

                <a href="{{ route('catchment.paper.blank') }}"
                   target="_blank"
                   class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-file-pdf"></i> Planilla vacía
                </a>
            </div>

            <h4>
                <u title="Listado especial con botones de acción">Listado</u> de las
                <span class="font-weight-bolder">Entrevistas</span> registradas
            </h4>

        </div>

        <div class="card-body">
            {{-- Estadísticas --}}
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <div class="row text-center">
                            <div class="col-md-2">
                                <button class="btn btn-link p-0 text-dark" wire:click="clearFilters">
                                    <h5 class="mb-0">{{ $statistics['total'] }}</h5>
                                    <small>Total</small>
                                </button>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-link p-0 text-success" wire:click="filterByAccepted">
                                    <h5 class="mb-0">{{ $statistics['accepted'] }}</h5>
                                    <small>Aceptados</small>
                                </button>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-link p-0 text-warning" wire:click="filterByStandby">
                                    <h5 class="mb-0">{{ $statistics['standby'] }}</h5>
                                    <small>En Espera</small>
                                </button>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-link p-0 text-info" wire:click="filterByNotified">
                                    <h5 class="mb-0">{{ $statistics['notified'] }}</h5>
                                    <small>Notificados</small>
                                </button>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-link p-0 text-secondary" wire:click="filterByPending">
                                    <h5 class="mb-0">{{ $statistics['pending'] }}</h5>
                                    <small>Pendientes</small>
                                </button>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-secondary btn-sm" wire:click="clearFilters">
                                    <i class="fas fa-sync-alt"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filtros y búsqueda --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text"
                               class="form-control"
                               placeholder="Buscar por cédula"
                               wire:model.debounce.500ms="representant_ci">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary"
                                    type="button"
                                    wire:click="clearFilters">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <input type="text"
                           class="form-control"
                           placeholder="Búsqueda general..."
                           wire:model.debounce.500ms="search">
                </div>
                <div class="col-md-2">
                    <select class="form-control" wire:model="filterAccepted">
                        <option value="">Todos los estados</option>
                        <option value="1">✅ Aceptados</option>
                        <option value="0">❌ No Aceptados</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" wire:model="filterStatusStandby">
                        <option value="">Estado de espera</option>
                        <option value="1">🟡 En Espera</option>
                        <option value="0">⚪ No en Espera</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" wire:model="filterStatusNotified">
                        <option value="">Estado notificación</option>
                        <option value="1">📧 Notificados</option>
                        <option value="0">📪 No Notificados</option>
                    </select>
                </div>
            </div>

            {{-- Filtros adicionales --}}
            <div class="row mb-3">
                <div class="col-md-2">
                    <select class="form-control" wire:model="perPage">
                        <option value="10">10 por página</option>
                        <option value="15">15 por página</option>
                        <option value="25">25 por página</option>
                        <option value="50">50 por página</option>
                        <option value="100">100 por página</option>
                    </select>
                </div>
                <div class="col-md-8">
                    {{-- Filtros activos --}}
                    @if($representant_ci || $search || $filterAccepted !== '' || $filterStatusNotified !== '' || $filterStatusStandby !== '')
                        <div class="alert alert-light py-2">
                            <small><strong>Filtros activos:</strong></small>
                            @if($representant_ci)
                                <span class="badge badge-info">Cédula: {{ $representant_ci }}</span>
                            @endif
                            @if($search)
                                <span class="badge badge-info">Búsqueda: {{ $search }}</span>
                            @endif
                            @if($filterAccepted !== '')
                                <span class="badge badge-{{ $filterAccepted ? 'success' : 'danger' }}">
                                    {{ $filterAccepted ? 'Aceptados' : 'No Aceptados' }}
                                </span>
                            @endif
                            @if($filterStatusStandby !== '')
                                <span class="badge badge-{{ $filterStatusStandby ? 'warning' : 'secondary' }}">
                                    {{ $filterStatusStandby ? 'En Espera' : 'No en Espera' }}
                                </span>
                            @endif
                            @if($filterStatusNotified !== '')
                                <span class="badge badge-{{ $filterStatusNotified ? 'info' : 'dark' }}">
                                    {{ $filterStatusNotified ? 'Notificados' : 'No Notificados' }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="col-md-2">
                    <button class="btn btn-info btn-block" wire:click="$refresh">
                        <i class="fas fa-sync-alt"></i> Actualizar
                    </button>
                </div>
            </div>

            {{-- Loading indicator --}}
            <div wire:loading class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </div>

            {{-- Tabla de entrevistas --}}
            <div wire:loading.remove>
                @include('livewire.bienestar.matriculation.partials.interviews-table')
            </div>

            {{-- Paginación --}}
            <div class="d-flex justify-content-center">
                {{ $catchment_interviews->links() }}
            </div>
        </div>
    </div>

    {{-- Modal de edición --}}
    @if($showEditModal)
        @include('livewire.bienestar.matriculation.partials.edit-modal')
    @endif

    @section('sweetalert')
        @parent
        <script>
            // Listener para mostrar alertas - Compatible con SweetAlert2 v8.17.6
            window.addEventListener('showAlert', event => {
                const data = event.detail;
                const type = data.type;
                const message = data.message;

                // Configuración compatible con SweetAlert2 v8.17.6
                const config = {
                    type: type === 'success' ? 'success' : 'error', // Usar 'type' en lugar de 'icon'
                    title: type === 'success' ? '¡Éxito!' : '¡Error!',
                    text: message,
                    timer: 4000,
                    showConfirmButton: true,
                    confirmButtonText: 'OK'
                };

                Swal.fire(config);
            });

            // Listener para confirmar eliminación - Compatible con SweetAlert2 v8.17.6
            window.addEventListener('confirmDelete', event => {
                const data = event.detail;
                const id = data.id;
                const name = data.name;

                // Configuración compatible con SweetAlert2 v8.17.6
                const config = {
                    title: '¿Está seguro?',
                    text: `¿Desea eliminar la entrevista de ${name}?`,
                    type: 'warning', // Usar 'type' en lugar de 'icon'
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                };

                Swal.fire(config).then((result) => {
                    if (result.value) { // En v8.17.6 se usa 'result.value' en lugar de 'result.isConfirmed'
                        @this.call('deleteInterview');
                    }
                });
            });

            // Listener para refrescar después de operaciones
            window.addEventListener('refreshComponent', event => {
                @this.call('$refresh');
            });
        </script>
    @endsection
</div>


