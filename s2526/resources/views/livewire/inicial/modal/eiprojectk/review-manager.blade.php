<div>
    <!-- Review List -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0 font-weight-bold">
                <i class="fas fa-search mr-2"></i>Revisiones Registradas
            </h6>
            <button type="button" class="btn btn-success btn-sm" 
                    wire:click="openModal('edit-review')">
                <i class="fas fa-plus mr-1"></i>Agregar Revisión
            </button>
        </div>

        @php
            $reviews = \App\Models\app\Inicial\Eiprojectk::find($eiprojectk_id)?->getOrderedViews() ?? collect();
        @endphp

        @if($reviews->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th width="60">Orden</th>
                            <th>Tema Elegido</th>
                            <th>Detalles de la Revisión</th>
                            <th width="120">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reviews as $review)
                            <tr>
                                <td>
                                    <span class="badge badge-info">{{ $review->order ?? 'S/N' }}</span>
                                </td>
                                <td>
                                    <strong class="text-primary">{{ $review->eleccion_tema_nombre }}</strong>
                                    <br>
                                    <small class="text-muted">{{ Str::limit($review->posibles_temas_interes, 50) }}</small>
                                </td>
                                <td>
                                    <div class="small">
                                        <div class="mb-1">
                                            <strong>Saben:</strong> {{ Str::limit($review->que_sabe, 100) }}
                                        </div>
                                        <div class="mb-1">
                                            <strong>Quieren aprender:</strong> {{ Str::limit($review->que_desean_aprender, 100) }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" 
                                                wire:click="openModal('edit-review', {{ $review->id }})"
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="confirm('¿Eliminar esta revisión?') || event.stopImmediatePropagation()" 
                                                wire:click="deleteReview({{ $review->id }})"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h6 class="text-muted">No hay revisiones registradas</h6>
                <p class="text-muted small">Agrega la primera revisión para planificar el desarrollo del proyecto.</p>
                <button type="button" class="btn btn-success" 
                        wire:click="openModal('edit-review')">
                    <i class="fas fa-plus mr-1"></i>Agregar Primera Revisión
                </button>
            </div>
        @endif
    </div>

    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary" wire:click="closeModal">
            <i class="fas fa-times mr-1"></i>Cerrar
        </button>
    </div>
</div>
