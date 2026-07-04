<div>
    <main role="main">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 p-1">
                    <div class="card card-primary mt-2">
                        <div class="card-header pb-0 mb-0 alert-secondary">
                            {{-- INI Menu rapido --}}
                            <div class="btn-group float-right pt-0 pb-2">
                                @include('bienestars.matriculations.catchments.menus.index')
                            </div>
                            {{-- FIN Menu rapido --}}
                            <h4><u title="Listado especial con botones de acción">Listado</u> de las <span class="font-weight-bolder">manifestaciones de interés</span> registradas</h4>
                        </div>

                        <div class="card-body">
                            {{-- Filtros de búsqueda --}}
                            <div class="py-2 my-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="text"
                                                   class="form-control"
                                                   placeholder="Buscar por Cédula"
                                                   wire:model.defer="representant_ci">
                                            <div class="input-group-append">
                                                <button class="btn btn-info"
                                                        type="button"
                                                        wire:click="searchByCi">
                                                    Buscar
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
                                        <select class="form-control" wire:model="filterStatusActive">
                                            <option value="">Todos</option>
                                            <option value="1">Activos</option>
                                            <option value="0">Eliminados</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-secondary"
                                                wire:click="clearFilters">
                                            Limpiar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Loading indicator --}}
                            <div wire:loading class="text-center py-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                            </div>

                            {{-- Tabla de datos --}}
                            <div wire:loading.remove>
                                @include('livewire.bienestar.matriculation.partials.catchment-table')
                            </div>

                            {{-- Paginación --}}
                            <div class="d-flex justify-content-center mt-3">
                                {{ $catchments->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

@section('sweetalert')
    @parent
    <script>
        // Configuración de SweetAlert2 para versión 8.17.6
        window.addEventListener('swal:confirm-catchment', event => {
            Swal.fire({
                title: event.detail.title,
                text: event.detail.text,
                type: 'warning',  // ✅ Cambiar icon por type
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: event.detail.confirmButtonText || 'Sí',
                cancelButtonText: event.detail.cancelButtonText || 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed || result.value) {  // ✅ En versión antigua usar result.value
                    if (event.detail.action === 'forceDelete') {
                        @this.call('confirmForceDelete');
                    } else if (event.detail.action === 'restore') {
                        @this.call('confirmRestore');
                    } else {
                        @this.call('confirmDelete');
                    }
                } else {
                    if (event.detail.action === 'forceDelete') {
                        @this.call('cancelForceDelete');
                    } else if (event.detail.action === 'restore') {
                        @this.call('cancelRestore');
                    } else {
                        @this.call('cancelDelete');
                    }
                }
            });
        });

        window.addEventListener('swal:success-catchment', event => {
            Swal.fire({
                title: event.detail.title,
                text: event.detail.text,
                type: 'success',  // ✅ Cambiar icon por type
                timer: 3000,
                showConfirmButton: false
            });
        });

        window.addEventListener('swal:error-catchment', event => {
            Swal.fire({
                title: event.detail.title,
                text: event.detail.text,
                type: 'error'  // ✅ Cambiar icon por type
            });
        });
    </script>
@endsection


</div>
