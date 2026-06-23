<div>
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="card-title mb-0">
                        <strong>Gestión de Planes Educativos</strong>                        
                        <div class="text-muted">Límites Máximos</div>
                        <div class="text-muted"><small>Definición de Objetivos del Período Escolar</small></div>
                    </h4>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-8">
                            <input type="text" 
                                   class="form-control" 
                                   placeholder="Buscar por nombre o descripción..." 
                                   wire:model.debounce.300ms="search">
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" wire:model="perPage">
                                <option value="5">5 por página</option>
                                <option value="10">10 por página</option>
                                <option value="25">25 por página</option>
                                <option value="50">50 por página</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>            
        </div>

        <div class="card-body">
            @if($peducativos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Orden</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Planes Sem.</th>
                                <th>Planes Quin.</th>
                                <th>Proyectos</th>
                                <th>P. Especiales</th>
                                <th>Evaluaciones</th>
                                <th>I. Pedagógicos</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peducativos as $peducativo)
                                <tr>
                                    <td>{{ $peducativo->order }}</td>
                                    <td>{{ $peducativo->name }}</td>
                                    <td>{{ Str::limit($peducativo->description, 50) }}</td>
                                    
                                    @if($editingId === $peducativo->id)
                                        <!-- Editing Mode -->
                                        <td>
                                            <input type="number" 
                                                   class="form-control form-control-sm @error('max_number_eiplanningwks') is-invalid @enderror" 
                                                   wire:model="max_number_eiplanningwks" 
                                                   min="0" max="999">
                                            @error('max_number_eiplanningwks')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" 
                                                   class="form-control form-control-sm @error('max_number_eiplanningbwks') is-invalid @enderror" 
                                                   wire:model="max_number_eiplanningbwks" 
                                                   min="0" max="999">
                                            @error('max_number_eiplanningbwks')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" 
                                                   class="form-control form-control-sm @error('max_number_eiprojectks') is-invalid @enderror" 
                                                   wire:model="max_number_eiprojectks" 
                                                   min="0" max="999">
                                            @error('max_number_eiprojectks')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" 
                                                   class="form-control form-control-sm @error('max_number_eispecialks') is-invalid @enderror" 
                                                   wire:model="max_number_eispecialks" 
                                                   min="0" max="999">
                                            @error('max_number_eispecialks')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" 
                                                   class="form-control form-control-sm @error('max_number_eievaluationks') is-invalid @enderror" 
                                                   wire:model="max_number_eievaluationks" 
                                                   min="0" max="999">
                                            @error('max_number_eievaluationks')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" 
                                                   class="form-control form-control-sm @error('max_number_eifinalks') is-invalid @enderror" 
                                                   wire:model="max_number_eifinalks" 
                                                   min="0" max="999">
                                            @error('max_number_eifinalks')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $peducativo->status_active === 'true' ? 'success' : 'danger' }}">
                                                {{ $peducativo->status_active === 'true' ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" 
                                                        class="btn btn-success btn-sm" 
                                                        wire:click="update"
                                                        wire:loading.attr="disabled">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-secondary btn-sm" 
                                                        wire:click="cancelEdit">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    @else
                                        <!-- Display Mode -->
                                        <td>{{ $peducativo->max_number_eiplanningwks ?? 0 }}</td>
                                        <td>{{ $peducativo->max_number_eiplanningbwks ?? 0 }}</td>
                                        <td>{{ $peducativo->max_number_eiprojectks ?? 0 }}</td>
                                        <td>{{ $peducativo->max_number_eispecialks ?? 0 }}</td>
                                        <td>{{ $peducativo->max_number_eievaluationks ?? 0 }}</td>
                                        <td>{{ $peducativo->max_number_eifinalks ?? 0 }}</td>
                                        <td>
                                            <span class="badge badge-{{ $peducativo->status_active === 'true' ? 'success' : 'danger' }}">
                                                {{ $peducativo->status_active === 'true' ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-primary btn-sm" 
                                                    wire:click="edit({{ $peducativo->id }})"
                                                    title="Editar límites">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <small class="text-muted">
                            Mostrando {{ $peducativos->firstItem() }} a {{ $peducativos->lastItem() }} 
                            de {{ $peducativos->total() }} resultados
                        </small>
                    </div>
                    <div>
                        {{ $peducativos->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No se encontraron Planes Educativos</h5>
                    <p class="text-muted">{{ $search ? 'Intenta con otros términos de búsqueda.' : 'No hay registros disponibles.' }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Sección Informativa -->
    @include('livewire.evaluacion.peducativo.partisals.info')

</div>