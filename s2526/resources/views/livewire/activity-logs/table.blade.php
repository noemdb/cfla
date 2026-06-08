<div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="text-start col-sm-6 col-md-2">
                    <label for="start_date" class="form-label">Fecha inicial</label>
                    <input type="date" wire:model="start_date" class="form-control" id="start_date">
                </div>
                <div class="text-start col-sm-6 col-md-2">
                    <label for="end_date" class="form-label">Fecha final</label>
                    <input type="date" wire:model="end_date" class="form-control" id="end_date">
                </div>
                <div class="text-start col-sm-6 col-md-2">
                    <label for="url" class="form-label">URL</label>
                    <input type="text" wire:model="url" class="form-control" id="url" placeholder="Filtrar por URL">
                </div>
                <div class="text-start col-sm-6 col-md-2">
                    <label for="role_status" class="form-label">Estado del Rol</label>
                    <select wire:model="roleStatus" class="form-select" id="role_status">
                        <option value="">Todos</option>
                        <option value="with_roles">Con Roles</option>
                    </select>
                </div>

                <div class="text-start col-sm-6 col-md-2">
                    <label for="user_id" class="form-label">Usuario</label>
                    <select wire:model="user_id" class="form-select select2" id="user_id" data-placeholder="Buscar usuario...">
                        <option value="">Todos los usuarios</option>
                        @foreach($filteredUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->username }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="text-start col-sm-6 col-md-2">
                    <label for="role_search" class="form-label">Áreas | Rol</label>
                    <div class="input-group">
                        <select wire:model="selectedArea" class="form-select" id="area">
                            <option value="">Todas las áreas</option>
                            @foreach($areas as $area)
                                <option value="{{ $area }}">{{ $area }}</option>
                            @endforeach
                        </select>
                        <select wire:model="selectedRol" class="form-select" id="rol">
                            <option value="">Todos los roles</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol }}">{{ $rol }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3 py-2 cursor-pointer" wire:click="sortBy('created_at')" style="cursor: pointer;">
                                Fecha
                                @if($sortField === 'created_at')
                                    @if($sortDirection === 'asc')
                                        <i class="fas fa-sort-up ms-1"></i>
                                    @else
                                        <i class="fas fa-sort-down ms-1"></i>
                                    @endif
                                @endif
                            </th>
                            <th class="px-3 py-2 cursor-pointer" wire:click="sortBy('causer_id')" style="cursor: pointer;">
                                Usuario
                                @if($sortField === 'causer_id')
                                    @if($sortDirection === 'asc')
                                        <i class="fas fa-sort-up ms-1"></i>
                                    @else
                                        <i class="fas fa-sort-down ms-1"></i>
                                    @endif
                                @endif
                            </th>
                            <th class="px-3 py-2 cursor-pointer" wire:click="sortBy('role')" style="cursor: pointer;">
                                Rol
                                @if($sortField === 'role')
                                    @if($sortDirection === 'asc')
                                        <i class="fas fa-sort-up ms-1"></i>
                                    @else
                                        <i class="fas fa-sort-down ms-1"></i>
                                    @endif
                                @endif
                            </th>
                            <th class="px-3 py-2 cursor-pointer" wire:click="sortBy('description')" style="cursor: pointer;">
                                Acción
                                @if($sortField === 'description')
                                    @if($sortDirection === 'asc')
                                        <i class="fas fa-sort-up ms-1"></i>
                                    @else
                                        <i class="fas fa-sort-down ms-1"></i>
                                    @endif
                                @endif
                            </th>
                            <th class="px-3 py-2 cursor-pointer" wire:click="sortBy('properties->route')" style="cursor: pointer;">
                                URL
                                @if($sortField === 'properties->route')
                                    @if($sortDirection === 'asc')
                                        <i class="fas fa-sort-up ms-1"></i>
                                    @else
                                        <i class="fas fa-sort-down ms-1"></i>
                                    @endif
                                @endif
                            </th>
                            <th class="px-3 py-2 cursor-pointer" wire:click="sortBy('properties->ip_address')" style="cursor: pointer;">
                                IP
                                @if($sortField === 'properties->ip_address')
                                    @if($sortDirection === 'asc')
                                        <i class="fas fa-sort-up ms-1"></i>
                                    @else
                                        <i class="fas fa-sort-down ms-1"></i>
                                    @endif
                                @endif
                            </th>
                            <th class="px-3 py-2 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $item)
                            <tr>
                                <td class="px-3 py-2">
                                    {{ $item->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ $item->causer?->username ?? 'Sistema' }}
                                </td>
                                <td class="px-3 py-2">
                                    @if($item->causer?->rols->first())
                                        {{ $item->causer->rols->first()->area }} - {{ $item->causer->rols->first()->rol }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    {{ $item->description }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ $item->properties->get('route') }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ $this->getIpAddress($item) }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <button wire:click="showDetails({{ $item->id }})" class="btn btn-sm btn-link text-primary">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    No se encontraron actividades
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-center">
                {{ $activities->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    @if($showDetails && $selectedActivity)
        @include('livewire.activity-logs.details')
    @endif

    @push('scripts')
    <script>
        document.addEventListener('livewire:load', function () {
            // Inicializar Select2 para el filtro de usuarios
            $('#user_id').select2({
                theme: 'bootstrap-5',
                width: '100%',
                allowClear: true,
                language: {
                    noResults: function() {
                        return "No se encontraron resultados";
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                }
            }).on('change', function() {
                @this.set('user_id', $(this).val());
            });

            // Reiniciar filtros cuando se actualiza el componente
            Livewire.on('resetFilters', () => {
                $('#user_id').val('').trigger('change');
                document.getElementById('area').value = '';
                document.getElementById('rol').value = '';
            });
        });
    </script>
    @endpush
</div>
