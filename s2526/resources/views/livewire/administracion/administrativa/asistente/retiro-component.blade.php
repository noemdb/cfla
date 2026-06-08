<div>
    <div class="card card-primary mt-2">

        <div class="p-2 text-muted">Listado de estudiantes activos</div>
        
        <div class="card-body">
            {{-- Buscador --}}
            <div class="card-header p-0 m-0 mb-3 border-0">
                <form wire:submit.prevent="$refresh" class="form-inline">
                    <div class="input-group w-100">
                        <input 
                            type="text" 
                            wire:model.debounce.500ms="search"
                            class="form-control" 
                            placeholder="Buscar estudiante por nombre o cédula..."
                            aria-label="Buscar"
                        >
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
                
                <div class="mt-2 form-inline">
                    <label for="per_page" class="mr-2">Mostrar:</label>
                    <select wire:model="per_page" class="form-control form-control-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="ml-2">registros</span>
                </div>
            </div>

            {{-- Tabla de Estudiantes --}}
            <div class="border rounded p-1">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm small">
                        <thead>
                            <tr>
                                <th class="d-none d-sm-table-cell">N</th>
                                <th>Estudiante</th>
                                <th class="d-none d-md-table-cell">Cédula</th>
                                <th class="d-none d-lg-table-cell text-nowrap">Plan de Pago</th>
                                <th class="d-none d-lg-table-cell text-nowrap">Deuda <span class="small">[USD]</span></th>
                                <th class="d-none d-lg-table-cell">Grado/Sección</th>
                                <th class="d-none d-lg-table-cell" title="Retiro Académico">R.Académico</th>
                                <th class="d-none d-lg-table-cell" title="Retiro Administrativo">R.Administrativo</th>
                                <th class="d-none d-lg-table-cell" title="Fecha del retiro">Fecha</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($estudiants as $estudiant)
                                @php 
                                    $exchange_ammount_expire_bill = $estudiant->exchange_ammount_expire_bill ?? 0; 
                                @endphp
                                <tr>
                                    <td class="d-none d-sm-table-cell">
                                        {{ ($estudiants->currentPage() - 1) * $estudiants->perPage() + $loop->iteration }}
                                    </td>
                                    
                                    <td>
                                        <span class="font-weight-bold text-{{ ($exchange_ammount_expire_bill > 0) ? 'danger' : 'dark' }}">
                                            {{ $estudiant->fullname }}
                                        </span>
                                    </td>
                                    
                                    <td class="d-none d-md-table-cell">
                                        {{ $estudiant->ci_estudiant ?? '' }}
                                    </td>
                                    
                                    <td class="d-none d-lg-table-cell text-nowrap">
                                        @if (empty($estudiant->administrativa->planpago_id))
                                            <span class="badge badge-secondary mt-1" title="SIN PLAN DE PAGO ASIGNADO">
                                                .NINGUNO.
                                            </span>
                                        @else
                                            {!! $estudiant->administrativa->planpago->badge ?? '' !!}
                                        @endif
                                    </td>
                                    
                                    <td class="d-none d-lg-table-cell">
                                        {{ number_format($exchange_ammount_expire_bill, 2) }}
                                    </td>
                                    
                                    <td class="d-none d-lg-table-cell">
                                        <span class="{{ $estudiant->getInscripcion()->seccion->grado->class_text_color ?? 'text-dark' }}">
                                            {{ $estudiant->getInscripcion()->seccion->grado->name ?? '' }} 
                                            {{ $estudiant->getInscripcion()->seccion->name ?? '' }}
                                        </span>
                                    </td>
                                    
                                    <td class="d-none d-lg-table-cell">
                                        <span class="badge badge-{{ $estudiant->rControl ? 'warning' : 'secondary' }}">
                                            {{ $estudiant->rControl ? 'SI' : 'NO' }}
                                        </span>
                                    </td>
                                    
                                    <td class="d-none d-lg-table-cell">
                                        <span class="badge badge-{{ $estudiant->rAdmon ? 'danger' : 'secondary' }}">
                                            {{ $estudiant->rAdmon ? 'SI' : 'NO' }}
                                        </span>
                                    </td>
                                    
                                    <td class="d-none d-lg-table-cell">
                                        @if ($estudiant->rAdmon || $estudiant->rControl)
                                            <div class="text-dark small">
                                                {{ $estudiant->retiro->created_at->format('d/m/Y H:i') }}
                                            </div>
                                        @endif
                                    </td>
                                    
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @php 
                                                $disabledControl = ($estudiant->rControl) ? true : false;
                                                $disabledAdmon = ($estudiant->rAdmon) ? true : false;
                                                $isDisabled = $disabledControl && $disabledAdmon;
                                            @endphp
                                            
                                            <button 
                                                wire:click="confirmarRetiro({{ $estudiant->id }})"
                                                class="btn btn-{{ $isDisabled ? 'secondary' : 'danger' }}"
                                                {{ $isDisabled ? 'disabled' : '' }}
                                                title="{{ $isDisabled ? 'Estudiante ya retirado' : 'Retirar estudiante' }}"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                        <p class="text-muted">No se encontraron estudiantes que coincidan con la búsqueda.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="mt-3">
                    {{ $estudiants->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para Retiro Administrativo con Observaciones --}}
    @if($showModalRetiro && $selectedEstudiante)
    <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Gestión - Retiro Administrativo
                    </h5>
                    <button type="button" class="close text-white" wire:click="cerrarModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle mr-2"></i>
                        Proceso de retiro del estudiante: 
                        <strong>{{ $selectedEstudiante->fullname }}</strong>
                    </div>

                    @if($selectedEstudiante->rControl || $selectedEstudiante->rAdmon)
                    <div class="alert alert-info">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Este estudiante ya tiene un retiro 
                        {{ $selectedEstudiante->rControl ? 'Académico' : 'Administrativo' }} 
                        registrado.
                    </div>
                    @endif

                    <div class="form-group">
                        <label for="observations" class="font-weight-bold">
                            Observaciones Obligatorias <span class="text-danger">*</span>
                        </label>
                        <textarea 
                            wire:model="observations"
                            id="observations" 
                            class="form-control @error('observations') is-invalid @enderror" 
                            rows="5" 
                            placeholder="Ingrese el razonamiento y justificación del retiro administrativo (mínimo 10 caracteres)..."
                            maxlength="500"
                            {{ $selectedEstudiante->rAdmon ? 'disabled' : '' }}
                        ></textarea>
                        @error('observations')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Mínimo 10 caracteres, máximo 500 caracteres. 
                            <span class="font-weight-bold 
                                @if(strlen($observations) >= 10 && strlen($observations) <= 500) text-success @else text-danger @endif">
                                {{ strlen($observations) }}/500
                            </span>
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="cerrarModal">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" wire:click="procesarRetiroDesdeModal"
                        {{ $selectedEstudiante->rAdmon ? 'disabled' : '' }}
                        @if(!$selectedEstudiante->rAdmon && (strlen($observations) < 10 || strlen($observations) > 500)) disabled @endif>
                        <i class="fas fa-check mr-1"></i> Confirmar Retiro
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>