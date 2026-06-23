<div>
    <!-- Encabezado -->
    <div class="card card-primary border-0 m-0 p-0">
        
        <div class="card-body">
            <!-- Panel de Filtros -->
            <div class="card mb-4">
                
                <div class="card-body">
                    <div class="row">
                        <!-- Búsqueda por texto -->
                        <div class="col-md-4">
                            <label for="search" class="form-label font-weight-bold text-muted">Buscar (Cédula o Nombre)</label>
                            <input 
                                type="text" 
                                id="search"
                                class="form-control" 
                                placeholder="Buscar por cédula o nombre..."
                                wire:model.debounce.300ms="search"
                            >
                        </div>

                        <!-- Filtro por Estado -->
                        <div class="col-md-3">
                            <label for="status_active" class="form-label font-weight-bold text-muted">Estado</label>
                            <select 
                                id="status_active"
                                class="form-control" 
                                wire:model="status_active"
                            >
                                <option value="">Todos los estados</option>
                                <option value="true">Activo</option>
                                <option value="false">Inactivo</option>
                            </select>
                        </div>

                        <!-- Filtro por Lista Negra -->
                        <div class="col-md-3">
                            <label for="status_blacklist" class="form-label font-weight-bold text-muted">Lista Negra</label>
                            <select 
                                id="status_blacklist"
                                class="form-control" 
                                wire:model="status_blacklist"
                            >
                                <option value="">Todos</option>
                                <option value="true">En Lista Negra</option>
                                <option value="false">Fuera de Lista Negra</option>
                            </select>
                        </div>

                        <!-- Botones de acción -->
                        <div class="col-md-2">
                            <label class="form-label d-block">&nbsp;</label>
                            <button 
                                wire:click="resetFilters"
                                class="btn btn-outline-secondary btn-block"
                                title="Limpiar todos los filtros"
                            >
                                <i class="fas fa-redo"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Resumen de filtros activos -->
                    @if($search || $status_active !== '' || $status_blacklist !== '')
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info py-2">
                                <small>
                                    <strong>Filtros aplicados:</strong>
                                    @if($search)
                                        <span class="badge badge-primary">Búsqueda: "{{ $search }}"</span>
                                    @endif
                                    @if($status_active !== '')
                                        <span class="badge badge-info">Estado: {{ $status_active == 'true' ? 'Activo' : 'Inactivo' }}</span>
                                    @endif
                                    @if($status_blacklist !== '')
                                        <span class="badge badge-{{ $status_blacklist == 'true' ? 'danger' : 'success' }}">
                                            Lista Negra: {{ $status_blacklist == 'true' ? 'Sí' : 'No' }}
                                        </span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Estadísticas Rápidas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card alert-primary text-dark">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-0">Total</h6>
                                    <small>Representantes</small>
                                </div>
                                <div class="text-right">
                                    <h4 class="mb-0">{{ $total_representants }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card alert-success text-dark">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-0">Activos</h6>
                                    <small>En sistema</small>
                                </div>
                                <div class="text-right">
                                    <h4 class="mb-0">{{ $active_representants }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card alert-danger text-dark">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-0">Lista Negra</h6>
                                    <small>Actual</small>
                                </div>
                                <div class="text-right">
                                    <h4 class="mb-0">{{ $blacklist_representants }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card alert-warning text-dark">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-0">Mostrando</h6>
                                    <small>Resultados</small>
                                </div>
                                <div class="text-right">
                                    <h4 class="mb-0">{{ $current_count }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de representantes -->
            <div class="table-responsive">
                <table class="table table-striped table-hover table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th width="5%">N</th>
                            <th width="15%">Cédula</th>
                            <th width="20%">Nombre</th>
                            <th width="10%" class="text-center">Estudiantes</th>
                            <th width="10%" class="text-center">Estado</th>
                            <th width="15%" class="text-center">Lista Negra</th>
                            <th width="25%" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($representants as $representant)
                            <tr class="{{ $representant->status_blacklist == 'true' ? 'table-danger' : '' }} {{ $representant->status_active == 'false' ? 'table-warning' : '' }}">
                                <td class="text-center">{{ ($representants->currentPage() - 1) * $representants->perPage() + $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $representant->ci_representant }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <strong>{{ $representant->name }}</strong>
                                            @if($representant->email)
                                                <br><small class="text-muted">{{ $representant->email }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-light" title="{{ $representant->estudiants_count }} estudiantes">
                                        {{ $representant->estudiants_count }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $representant->status_active == 'true' ? 'success' : 'danger' }}">
                                        {{ $representant->status_active == 'true' ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $representant->status_blacklist == 'true' ? 'danger' : 'secondary' }}">
                                        <i class="fas {{ $representant->status_blacklist == 'true' ? 'fa-exclamation-triangle' : 'fa-check-circle' }}"></i>
                                        {{ $representant->status_blacklist == 'true' ? 'En Lista Negra' : 'Normal' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">

                                        <!-- Botón para agregar/retirar de lista negra -->
                                        @if($representant->status_blacklist == 'true')
                                            <button 
                                                wire:click="removeFromBlacklist({{ $representant->id }})"
                                                class="btn btn-success"
                                                title="Retirar de lista negra"
                                            >
                                                <i class="fas fa-check-circle"></i> Retirar
                                            </button>
                                        @else
                                            <button 
                                                wire:click="addToBlacklist({{ $representant->id }})"
                                                class="btn btn-warning"
                                                title="Agregar a lista negra"
                                            >
                                                <i class="fas fa-exclamation-triangle"></i> Agregar
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-search fa-2x mb-2"></i>
                                        <br>
                                        No se encontraron representantes con los filtros aplicados
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación y Controles -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="d-flex align-items-center">
                    <span class="text-muted mr-2">Mostrar:</span>
                    <select wire:model="paginate" class="form-control form-control-sm" style="width: auto;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-muted ml-2">registros por página</span>
                </div>
                <div>
                    {{ $representants->links() }}
                </div>
            </div>
        </div>
    </div>

    @section('sweetalert')
    @parent        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Listener para confirmar agregar a lista negra
                window.addEventListener('show-confirm-add', event => {
                    Swal.fire({
                        title: '¿Agregar a lista negra?',
                        text: 'Está a punto de agregar al representante: ' + event.detail.name + ' a la lista negra. ¿Está seguro?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, agregar',
                        cancelButtonText: 'Cancelar',
                        focusCancel: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Llamar al método del componente Livewire
                            Livewire.emit('confirmAddToBlacklist', event.detail.id);
                        }
                    });
                });

                // Listener para confirmar retirar de lista negra
                window.addEventListener('show-confirm-remove', event => {
                    Swal.fire({
                        title: '¿Retirar de lista negra?',
                        text: 'Está a punto de retirar al representante: ' + event.detail.name + ' de la lista negra. ¿Está seguro?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, retirar',
                        cancelButtonText: 'Cancelar',
                        focusCancel: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Llamar al método del componente Livewire
                            Livewire.emit('confirmRemoveFromBlacklist', event.detail.id);
                        }
                    });
                });

                // Listener para mensajes generales de SweetAlert
                window.addEventListener('swal', event => {
                    Swal.fire({
                        title: event.detail.title,
                        html: event.detail.html,
                        timer: event.detail.timer,
                        icon: event.detail.icon,
                        toast: event.detail.toast,
                        position: event.detail.position,
                        showConfirmButton: event.detail.showConfirmButton,
                        confirmButtonText: event.detail.confirmButtonText
                    });
                });
            });
        </script>
    @endsection

</div>