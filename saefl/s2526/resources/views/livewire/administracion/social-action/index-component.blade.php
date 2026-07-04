<div>

    <div>
        <div class="card">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input wire:model.debounce.300ms="search" type="text" class="form-control" placeholder="Buscar...">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select wire:model="perPage" class="form-control">
                            <option value="10">10 por página</option>
                            <option value="25">25 por página</option>
                            <option value="50">50 por página</option>
                            <option value="100">100 por página</option>
                        </select>
                    </div>
                    @if(!$grado_id)
                    <div class="col-md-4">
                        <select wire:model="grado_id" class="form-control">
                            <option value="">Todos los grados</option>
                            @foreach($grados as $grado)
                                <option value="{{ $grado->id }}">{{ $grado->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                @if (session()->has('message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th wire:click="sortBy('title')" style="cursor: pointer;">
                                    Título
                                    @if ($sortField === 'title')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('type')" style="cursor: pointer;">
                                    Tipo
                                    @if ($sortField === 'type')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                @if(!$grado_id)
                                <th wire:click="sortBy('grado_id')" style="cursor: pointer;">
                                    Grado
                                    @if ($sortField === 'grado_id')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                @endif
                                <th wire:click="sortBy('date')" style="cursor: pointer;">
                                    Fecha
                                    @if ($sortField === 'date')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('duration')" style="cursor: pointer;">
                                    Duración
                                    @if ($sortField === 'duration')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('status')" style="cursor: pointer;">
                                    Estado
                                    @if ($sortField === 'status')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($communityActions as $action)
                                <tr>
                                    <td>{{ $action->title }}</td>
                                    <td>{{ $types[$action->type] }}</td>
                                    @if(!$grado_id)
                                    <td>{{ $action->grado->name ?? 'No especificado' }}</td>
                                    @endif
                                    <td>{{ \Carbon\Carbon::parse($action->date)->format('d/m/Y') }}</td>
                                    <td>{{ $action->duration }} horas</td>
                                    <td>
                                        <span class="badge badge-{{ $action->status ? 'success' : 'danger' }}">
                                            {{ $statuses[$action->status] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-info" 
                                                wire:click="openViewModal({{ $action->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            @if($action->statusEdit)
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                wire:click="openEditModal({{ $action->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @endif
                                            
                                            @if($action->statusDelete)
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="confirm('¿Está seguro de eliminar esta acción comunitaria?') || event.stopImmediatePropagation()"
                                                wire:click="delete({{ $action->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $grado_id ? '6' : '7' }}" class="text-center">No hay acciones comunitarias registradas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-12 d-flex justify-content-center">
                        {{ $communityActions->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Crear -->
        <div wire:ignore.self class="modal fade" id="createCommunityActionModal" tabindex="-1" role="dialog" aria-labelledby="createCommunityActionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createCommunityActionModalLabel">Nueva Acción Comunitaria</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="store">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="title">Título <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" wire:model.defer="title">
                                        @error('title') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="grado_id">Grado <span class="text-danger">*</span></label>
                                        <select class="form-control @error('grado_id') is-invalid @enderror" id="grado_id" wire:model.defer="grado_id">
                                            <option value="">Seleccione un grado</option>
                                            @foreach($grados as $grado)
                                                <option value="{{ $grado->id }}">{{ $grado->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('grado_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Descripción <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" rows="3" wire:model.defer="description"></textarea>
                                @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date">Fecha <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" wire:model.defer="date">
                                        @error('date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="duration">Duración (horas) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('duration') is-invalid @enderror" id="duration" wire:model.defer="duration" min="1" step="0.5">
                                        @error('duration') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="type">Tipo <span class="text-danger">*</span></label>
                                        <select class="form-control @error('type') is-invalid @enderror" id="type" wire:model.defer="type">
                                            @foreach($types as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        @error('type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Estado <span class="text-danger">*</span></label>
                                        <select class="form-control @error('status') is-invalid @enderror" id="status" wire:model.defer="status">
                                            @foreach($statuses as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="entity_benefic">Entidad Beneficiada</label>
                                <input type="text" class="form-control @error('entity_benefic') is-invalid @enderror" id="entity_benefic" wire:model.defer="entity_benefic">
                                @error('entity_benefic') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="location">Ubicación</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" wire:model.defer="location">
                                @error('location') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="required">Requisitos</label>
                                <textarea class="form-control @error('required') is-invalid @enderror" id="required" rows="2" wire:model.defer="required"></textarea>
                                @error('required') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="observations">Observaciones</label>
                                <textarea class="form-control @error('observations') is-invalid @enderror" id="observations" rows="2" wire:model.defer="observations"></textarea>
                                @error('observations') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            {{-- 
                            <div class="form-group">
                                <label for="image">Imagen</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('image') is-invalid @enderror" id="image" wire:model="image" accept="image/*">
                                    <label class="custom-file-label" for="image">
                                        {{ $image ? $image->getClientOriginalName() : 'Seleccionar imagen' }}
                                    </label>
                                    @error('image') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                @if ($image)
                                    <div class="mt-2">
                                        <img src="{{ $image->temporaryUrl() }}" class="img-thumbnail" style="max-height: 200px;">
                                    </div>
                                @endif
                            </div> 
                            --}}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Editar -->
        <div wire:ignore.self class="modal fade" id="editCommunityActionModal" tabindex="-1" role="dialog" aria-labelledby="editCommunityActionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCommunityActionModalLabel">Editar Acción Comunitaria</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="update">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_title">Título <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="edit_title" wire:model.defer="title">
                                        @error('title') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_grado_id">Grado <span class="text-danger">*</span></label>
                                        <select class="form-control @error('grado_id') is-invalid @enderror" id="edit_grado_id" wire:model.defer="grado_id">
                                            <option value="">Seleccione un grado</option>
                                            @foreach($grados as $grado)
                                                <option value="{{ $grado->id }}">{{ $grado->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('grado_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="edit_description">Descripción <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="edit_description" rows="3" wire:model.defer="description"></textarea>
                                @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_date">Fecha <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('date') is-invalid @enderror" id="edit_date" wire:model.defer="date">
                                        @error('date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_duration">Duración (horas) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('duration') is-invalid @enderror" id="edit_duration" wire:model.defer="duration" min="1" step="0.5">
                                        @error('duration') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_type">Tipo <span class="text-danger">*</span></label>
                                        <select class="form-control @error('type') is-invalid @enderror" id="edit_type" wire:model.defer="type">
                                            @foreach($types as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        @error('type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="edit_status">Estado <span class="text-danger">*</span></label>
                                        <select class="form-control @error('status') is-invalid @enderror" id="edit_status" wire:model.defer="status">
                                            @foreach($statuses as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="edit_entity_benefic">Entidad Beneficiada</label>
                                <input type="text" class="form-control @error('entity_benefic') is-invalid @enderror" id="edit_entity_benefic" wire:model.defer="entity_benefic">
                                @error('entity_benefic') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="edit_location">Ubicación</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" id="edit_location" wire:model.defer="location">
                                @error('location') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="edit_required">Requisitos</label>
                                <textarea class="form-control @error('required') is-invalid @enderror" id="edit_required" rows="2" wire:model.defer="required"></textarea>
                                @error('required') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="edit_observations">Observaciones</label>
                                <textarea class="form-control @error('observations') is-invalid @enderror" id="edit_observations" rows="2" wire:model.defer="observations"></textarea>
                                @error('observations') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            {{-- <div class="form-group">
                                <label for="edit_image">Imagen</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('image') is-invalid @enderror" id="edit_image" wire:model="image" accept="image/*">
                                    <label class="custom-file-label" for="edit_image">
                                        {{ $image ? $image->getClientOriginalName() : 'Seleccionar nueva imagen' }}
                                    </label>
                                    @error('image') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                
                                @if ($image)
                                    <div class="mt-2">
                                        <img src="{{ $image->temporaryUrl() }}" class="img-thumbnail" style="max-height: 200px;">
                                    </div>
                                @elseif ($currentImage)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/social_accions/' . $currentImage) }}" class="img-thumbnail" style="max-height: 200px;">
                                    </div>
                                @endif
                            </div> --}}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Ver Detalles -->
        <div wire:ignore.self class="modal fade" id="viewCommunityActionModal" tabindex="-1" role="dialog" aria-labelledby="viewCommunityActionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewCommunityActionModalLabel">Detalles de Acción Comunitaria</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if($viewCommunityAction)
                            <div class="row">
                                <div class="col-md-8">
                                    <h4>{{ $viewCommunityAction->title }}</h4>
                                    <p class="text-muted">
                                        <span class="badge badge-{{ $viewCommunityAction->status ? 'success' : 'danger' }}">
                                            {{ $statuses[$viewCommunityAction->status] }}
                                        </span>
                                        <span class="badge badge-info ml-2">{{ $types[$viewCommunityAction->type] }}</span>
                                    </p>
                                </div>
                                <div class="col-md-4 text-right">
                                    <p class="mb-0"><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($viewCommunityAction->date)->format('d/m/Y') }}</p>
                                    <p><strong>Duración:</strong> {{ $viewCommunityAction->duration }} horas</p>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-8">
                                    <h5>Descripción</h5>
                                    <p>{{ $viewCommunityAction->description }}</p>

                                    @if($viewCommunityAction->observations)
                                        <h5>Observaciones</h5>
                                        <p>{{ $viewCommunityAction->observations }}</p>
                                    @endif

                                    @if($viewCommunityAction->required)
                                        <h5>Requisitos</h5>
                                        <p>{{ $viewCommunityAction->required }}</p>
                                    @endif

                                    <h5>Información Adicional</h5>
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr>
                                                <th style="width: 30%">Grado:</th>
                                                <td>{{ $viewCommunityAction->grado->name ?? 'No especificado' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Entidad Beneficiada:</th>
                                                <td>{{ $viewCommunityAction->entity_benefic ?? 'No especificado' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Ubicación:</th>
                                                <td>{{ $viewCommunityAction->location ?? 'No especificado' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Creado por:</th>
                                                <td>{{ $viewCommunityAction->user->username ?? 'Usuario desconocido' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Horas completadas:</th>
                                                <td>{{ $viewCommunityAction->hoursCompleted }} de {{ $viewCommunityAction->duration }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-4">
                                    @if($viewCommunityAction->image)
                                        <div class="card">
                                            <div class="card-body p-1">
                                                <img src="{{ $viewCommunityAction->imageUrl }}" class="img-fluid rounded" alt="{{ $viewCommunityAction->title }}">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                Cargando información...
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('livewireCustomeScripts')
        @parent
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Manejar eventos para abrir modales
                window.addEventListener('open-modal', event => {
                    $('#' + event.detail.modal).modal('show');
                });
                
                // Manejar eventos para cerrar modales
                window.addEventListener('close-modal', event => {
                    $('#' + event.detail.modal).modal('hide');
                });
                
                // Manejar el evento hidden.bs.modal para limpiar el estado cuando se cierra un modal
                $('.modal').on('hidden.bs.modal', function (e) {

                    // Restaurar el foco al elemento que abrió el modal
                    $(document.activeElement).blur();
                    // Emitir evento para que Livewire sepa que el modal se cerró
                    if (e.target.id === 'createCommunityActionModal') {
                        Livewire.emit('modalClosed', 'create');
                    } else if (e.target.id === 'editCommunityActionModal') {
                        Livewire.emit('modalClosed', 'edit');
                    } else if (e.target.id === 'viewCommunityActionModal') {
                        Livewire.emit('modalClosed', 'view');
                    }
                });
            });
        </script>
    @endsection

</div>

