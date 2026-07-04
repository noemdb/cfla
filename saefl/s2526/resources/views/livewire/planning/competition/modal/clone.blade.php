<div wire:ignore.self class="modal fade" id="cloneModal" tabindex="-1" role="dialog" aria-labelledby="cloneModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="cloneModalLabel">
                    <i class="fas fa-magic mr-2 text-primary"></i>Asistente de Clonación
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <!-- Stepper -->
                <div class="d-flex justify-content-between mb-4">
                    @for ($i = 1; $i <= 4; $i++)
                        <div class="text-center" style="width: 25%">
                            <div class="rounded-circle mx-auto mb-1 d-flex align-items-center justify-content-center {{ $step == $i ? 'bg-primary text-white' : ($step > $i ? 'bg-success text-white' : 'bg-secondary text-white') }}" 
                                 style="width: 30px; height: 30px; font-weight: bold; font-size: 0.9rem;">
                                @if($step > $i) <i class="fas fa-check"></i> @else {{ $i }} @endif
                            </div>
                            <small class="text-muted d-none d-sm-block" style="font-size: 0.7rem;">
                                @if($i == 1) Origen @elseif($i == 2) Destino @elseif($i == 3) Detalles @else Confirmar @endif
                            </small>
                        </div>
                    @endfor
                </div>

                <hr>

                @if ($step == 1)
                    <!-- STEP 1: ORIGEN -->
                    <div class="alert alert-info py-2">
                        <i class="fas fa-info-circle mr-2"></i>
                        Confirmación del origen de datos.
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Grado Origen:</label>
                        <div class="form-control-plaintext border-bottom pb-1 mb-2">
                            {{ \App\Models\app\Pescolar\Grado::find($clone_source_grado)->name ?? 'No definido' }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="clone_source_category" class="font-weight-bold">Categoría Origen:</label>
                        <select class="form-control" wire:model="clone_source_category" id="clone_source_category">
                            <option value="">Seleccione la categoría origen</option>
                            @foreach($list_category as $groupCode => $options)
                                <optgroup label="Sección {{ $groupCode }}">
                                    @foreach($options as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('clone_source_category') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    @if($clone_source_category)
                        <div class="mt-3 text-center">
                            <span class="badge badge-pill {{ $source_questions_count > 0 ? 'badge-success' : 'badge-danger' }} p-2">
                                <i class="fas fa-question-circle mr-1"></i>
                                Preguntas encontradas: {{ $source_questions_count }}
                            </span>
                        </div>
                    @endif

                @elseif ($step == 2)
                    <!-- STEP 2: DESTINO -->
                    <div class="form-group">
                        <label for="clone_target_grado" class="font-weight-bold">Grado Destino:</label>
                        <select class="form-control" wire:model="clone_target_grado" id="clone_target_grado">
                            <option value="">Seleccione el grado destino</option>
                            @foreach($list_target_grados as $groupName => $options)
                                <optgroup label="{{ $groupName }}">
                                    @foreach($options as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('clone_target_grado') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="clone_target_pensum_id" class="font-weight-bold">Pensum Destino:</label>
                        <select class="form-control" wire:model.defer="clone_target_pensum_id" id="clone_target_pensum_id" {{ empty($list_target_pensums) ? 'disabled' : '' }}>
                            <option value="">Seleccione el pensum destino</option>
                            @foreach($list_target_pensums as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('clone_target_pensum_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                @elseif ($step == 3)
                    <!-- STEP 3: DETALLES -->
                    <div class="form-group">
                        <label for="clone_target_debate_id" class="font-weight-bold">Debate Destino:</label>
                        <select class="form-control" wire:model.defer="clone_target_debate_id" id="clone_target_debate_id" {{ empty($list_target_debates) ? 'disabled' : '' }}>
                            <option value="">Seleccione el debate destino</option>
                            @foreach($list_target_debates as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('clone_target_debate_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="clone_target_category" class="font-weight-bold">Categoría Destino:</label>
                        <select class="form-control" wire:model.defer="clone_target_category" id="clone_target_category">
                            <option value="">Seleccione la categoría destino</option>
                            @foreach($list_category as $groupCode => $options)
                                <optgroup label="Sección {{ $groupCode }}">
                                    @foreach($options as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('clone_target_category') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                @elseif ($step == 4)
                    <!-- STEP 4: CONFIRMACIÓN -->
                    <div class="card border-primary mb-3">
                        <div class="card-header bg-primary text-white py-1">Resumen de Operación</div>
                        <div class="card-body py-2">
                            <div class="row small mb-2">
                                <div class="col-4 font-weight-bold">Origen:</div>
                                <div class="col-8">
                                    {{ \App\Models\app\Pescolar\Grado::find($clone_source_grado)->name }}<br>
                                    <span class="text-muted">{{ $clone_source_category }}</span>
                                </div>
                            </div>
                            <div class="row small mb-2">
                                <div class="col-4 font-weight-bold">Destino:</div>
                                <div class="col-8">
                                    {{ \App\Models\app\Pescolar\Pensum::find($clone_target_pensum_id)->name ?? 'N/A' }}<br>
                                    <span class="text-muted">{{ $clone_target_category }}</span>
                                </div>
                            </div>
                            <div class="row small">
                                <div class="col-4 font-weight-bold">Total:</div>
                                <div class="col-8 text-primary font-weight-bold">{{ $source_questions_count }} preguntas</div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-warning small py-2 mb-0">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Esta acción replicará todas las preguntas y sus opciones. ¿Desea continuar?
                    </div>
                @endif
            </div>

            <div class="modal-footer bg-light">
                @if ($step > 1)
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="previousStep">
                        <i class="fas fa-chevron-left mr-1"></i>Atrás
                    </button>
                @else
                    <button type="button" class="btn btn-light btn-sm text-danger" data-dismiss="modal">Cancelar</button>
                @endif

                @if ($step < 4)
                    <button type="button" class="btn btn-primary btn-sm" wire:click="nextStep">
                        Siguiente<i class="fas fa-chevron-right ml-1"></i>
                    </button>
                @else
                    <button type="button" class="btn btn-success btn-sm" wire:click="cloneQuestions" wire:loading.attr="disabled">
                        <span wire:loading wire:target="cloneQuestions" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <i class="fas fa-check mr-1" wire:loading.remove wire:target="cloneQuestions"></i>Finalizar y Clonar
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
