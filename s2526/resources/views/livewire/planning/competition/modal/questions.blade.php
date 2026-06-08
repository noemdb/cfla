<div wire:ignore.self class="modal fade" id="questionsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: calc(100% - 8px); margin-left: 4px; margin-right: 4px;">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title font-weight-bold text-white">
                    <i class="fas fa-list-ol mr-1"></i> Banco de Preguntas y Opciones
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-0">
                <div class="row m-0 border-bottom h-100" style="min-height: 500px;">
                    
                    {{-- PANEL IZQUIERDO: LISTA DE PREGUNTAS --}}
                    <div class="col-md-5 p-0 border-right bg-light" style="max-height: 600px; overflow-y: auto;">
                        <div class="p-3 bg-white sticky-top border-bottom shadow-sm d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-dark">Preguntas ({{ count($q_list) }})</h6>
                            <button wire:click="newQuestion" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-plus"></i> Nueva
                            </button>
                        </div>
                        
                        <div class="list-group list-group-flush">
                            @forelse($q_list as $q)
                                <div class="list-group-item list-group-item-action {{ $q_active_question_id == $q['id'] ? 'active shadow-sm z-index-1' : '' }} flex-column align-items-start p-3"
                                     style="cursor: pointer" wire:click="selectQuestion({{ $q['id'] }})">
                                    <div class="d-flex w-100 justify-content-between mb-1">
                                        <small class="{{ $q_active_question_id == $q['id'] ? 'text-white-50' : 'text-muted' }} font-weight-bold">
                                            {{ $q['category'] }}
                                        </small>
                                        <span class="badge badge-pill {{ $q['status_active'] ? 'badge-success' : 'badge-secondary' }}">
                                            {{ $q['status_active'] ? 'Activa' : 'Inactiva' }}
                                        </span>
                                    </div>
                                    <p class="mb-2 text-sm text-truncate" style="max-width: 100%; white-space: normal; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="{{ $q['text'] }}">
                                        {{ $q['text'] }}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <button wire:click.stop="selectQuestion({{ $q['id'] }})" class="btn btn-xs {{ $q_active_question_id == $q['id'] ? 'btn-light text-primary' : 'btn-primary' }} border font-weight-bold" title="Gestionar Opciones">
                                            <i class="fas fa-list-ul"></i> {{ count($q['options'] ?? []) }}/{{ $q['option_max'] }} opc.
                                        </button>
                                        <div>
                                            <button wire:click.stop="editQuestion({{ $q['id'] }})" class="btn btn-xs {{ $q_active_question_id == $q['id'] ? 'btn-light text-primary' : 'btn-outline-primary' }} p-1" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button wire:click.stop="deleteQuestion({{ $q['id'] }})" class="btn btn-xs {{ $q_active_question_id == $q['id'] ? 'btn-light text-danger' : 'btn-outline-danger' }} p-1 ml-1" title="Eliminar" onclick="confirm('¿Eliminar esta pregunta y sus opciones?') || event.stopImmediatePropagation()">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                    <p>No hay preguntas registradas en este plan de estudios.</p>
                                    <button wire:click="newQuestion" class="btn btn-primary mt-2">Crear la primera</button>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- PANEL DERECHO: FORMULARIOS Y OPCIONES --}}
                    <div class="col-md-7 p-0 bg-white" style="max-height: 600px; overflow-y: auto;">
                        
                        @if($q_form_mode)
                            {{-- FORMULARIO DE PREGUNTA --}}
                            <div class="p-4 bg-light border-bottom">
                                <h6 class="font-weight-bold text-primary mb-3">
                                    {{ $q_form_mode === 'create' ? 'Crear Nueva Pregunta' : 'Editar Pregunta' }}
                                </h6>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="small font-weight-bold text-muted mb-1">Texto de la Pregunta *</label>
                                        <textarea wire:model.defer="q_text" class="form-control" rows="3" placeholder="Escriba la pregunta aquí..."></textarea>
                                        @error('q_text') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="small font-weight-bold text-muted mb-1">Categoría *</label>
                                        <select wire:model.defer="q_category_field" class="form-control">
                                            <option value="">Seleccione Categoría</option>
                                            @foreach(\App\Models\app\Educational\DebateQuestion::CATEGORY as $key => $catName)
                                                <option value="{{ $key }}">{{ $catName }}</option>
                                            @endforeach
                                        </select>
                                        @error('q_category_field') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="small font-weight-bold text-muted mb-1">Tiempo (seg) *</label>
                                        <input type="number" wire:model.defer="q_time" class="form-control" min="5">
                                        @error('q_time') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="small font-weight-bold text-muted mb-1">Puntos</label>
                                        <input type="number" wire:model.defer="q_weighting" class="form-control" min="1">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="small font-weight-bold text-muted mb-1">Max Opciones</label>
                                        <select wire:model.defer="q_option_max" class="form-control">
                                            <option value="2">2 Opciones</option>
                                            <option value="3">3 Opciones</option>
                                            <option value="4">4 Opciones</option>
                                            <option value="5">5 Opciones</option>
                                            <option value="6">6 Opciones</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="small font-weight-bold text-muted mb-1">Estado</label>
                                        <div class="custom-control custom-switch mt-1">
                                            <input type="checkbox" wire:model.defer="q_status_active" class="custom-control-input" id="qStatusSwitch">
                                            <label class="custom-control-label" for="qStatusSwitch">Pregunta Activa</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="small font-weight-bold text-muted mb-1">Observación</label>
                                        <input type="text" wire:model.defer="q_observation" class="form-control" placeholder="Opcional">
                                    </div>
                                </div>
                                <div class="text-right mt-2">
                                    <button wire:click="$set('q_form_mode', null)" class="btn btn-light btn-sm mr-2">Cancelar</button>
                                    <button wire:click="saveQuestion" class="btn btn-primary btn-sm">
                                        <i class="fas fa-save"></i> Guardar Pregunta
                                    </button>
                                </div>
                            </div>
                        @elseif($q_active_question_id)
                            {{-- VISTA DE OPCIONES DE LA PREGUNTA SELECCIONADA --}}
                            <div class="p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                                    <h6 class="font-weight-bold text-dark m-0">Opciones de Respuesta</h6>
                                    <div>
                                        @if(count($opt_list) < ($q_list[array_search($q_active_question_id, array_column($q_list, 'id'))]['option_max'] ?? 4))
                                            <button wire:click="newOption" class="btn btn-sm btn-primary">
                                                <i class="fas fa-plus"></i> Añadir Opción
                                            </button>
                                        @else
                                            <span class="badge badge-warning" title="Se alcanzó el límite configurado">Límite alcanzado</span>
                                        @endif
                                        <button wire:click="$set('q_active_question_id', null)" class="btn btn-sm btn-outline-danger ml-2" title="Cerrar panel de opciones">
                                            <i class="fas fa-times mr-1"></i> Cerrar
                                        </button>
                                    </div>
                                </div>

                                @if($opt_form_mode)
                                    {{-- FORMULARIO DE OPCION INLINE --}}
                                    <div class="card bg-light border-primary shadow-sm mb-4">
                                        <div class="card-body p-3">
                                            <h6 class="text-primary font-weight-bold mb-3 small">{{ $opt_form_mode === 'create' ? 'Nueva Opción' : 'Editando Opción' }}</h6>
                                            <div class="form-group mb-2">
                                                <input type="text" wire:model.defer="opt_text" class="form-control form-control-sm" placeholder="Texto de la opción *">
                                                @error('opt_text') <span class="text-danger small">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="form-group mb-3">
                                                <input type="text" wire:model.defer="opt_observation" class="form-control form-control-sm" placeholder="Observación (opcional)">
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" wire:model.defer="opt_status_option_correct" class="custom-control-input" id="optCorrectCheck">
                                                    <label class="custom-control-label text-success font-weight-bold" for="optCorrectCheck">Es la opción correcta</label>
                                                </div>
                                                <div>
                                                    <button wire:click="$set('opt_form_mode', null)" class="btn btn-light btn-sm mr-1">Cancelar</button>
                                                    <button wire:click="saveOption" class="btn btn-primary btn-sm">Guardar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- LISTA DE OPCIONES --}}
                                <div class="list-group">
                                    @forelse($opt_list as $opt)
                                        <div class="list-group-item d-flex justify-content-between align-items-center {{ $opt['status_option_correct'] ? 'border-success' : '' }}">
                                            <div class="d-flex align-items-center">
                                                @if($opt['status_option_correct'])
                                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 24px; height: 24px;">
                                                        <i class="fas fa-check small"></i>
                                                    </div>
                                                @else
                                                    <div class="bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center mr-3 border" style="width: 24px; height: 24px;">
                                                        <i class="fas fa-times small opacity-50"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <p class="m-0 font-weight-bold {{ $opt['status_option_correct'] ? 'text-success' : 'text-dark' }}">
                                                        {{ $opt['text'] }}
                                                    </p>
                                                    @if($opt['observation'])
                                                        <small class="text-muted">{{ $opt['observation'] }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                            <div>
                                                <button wire:click="editOption({{ $opt['id'] }})" class="btn btn-sm btn-link text-primary p-1"><i class="fas fa-edit"></i></button>
                                                <button wire:click="deleteOption({{ $opt['id'] }})" class="btn btn-sm btn-link text-danger p-1" onclick="confirm('¿Eliminar esta opción?') || event.stopImmediatePropagation()"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </div>
                                    @empty
                                        @if(!$opt_form_mode)
                                            <div class="text-center py-4 text-muted border border-dashed rounded bg-light">
                                                <p class="mb-2">Aún no hay opciones para esta pregunta.</p>
                                            </div>
                                        @endif
                                    @endforelse
                                </div>
                            </div>
                        @else
                            {{-- ESTADO VACÍO (NINGUNA PREGUNTA SELECCIONADA NI CREADA) --}}
                            <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted opacity-50 p-5 text-center">
                                <i class="fas fa-mouse-pointer fa-4x mb-3 text-light"></i>
                                <h5>Selecciona una pregunta</h5>
                                <p>Haz clic en una pregunta del panel izquierdo para ver y gestionar sus opciones de respuesta.</p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
            
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('open-questions-modal', event => {
        $('#questionsModal').modal('show');
    });
</script>
