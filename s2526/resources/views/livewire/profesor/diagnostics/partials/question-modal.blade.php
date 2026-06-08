<!-- Corregí la estructura del modal para mejor overlay y z-index -->
@if ($showQuestionModal)
    <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
    <div class="modal fade show d-block" style="z-index: 1050;" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content shadow-lg border-0" style="max-height: 90vh;">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="fas fa-question-circle me-2"></i>
                        {{ $editingQuestion ? 'Editar Pregunta' : 'Nueva Pregunta' }}
                        <span class="badge bg-light text-primary ms-2">Paso {{ $wizardStep }} de 3</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeQuestionModal"></button>
                </div>

                <form wire:submit.prevent="saveQuestion">
                    <div class="modal-body p-4" style="max-height: calc(90vh - 200px); overflow-y: auto;">
                        <!-- Mejoré la barra de progreso con mejor diseño -->
                        <div class="mb-4">
                            <div class="progress progress-lg shadow-sm" style="height: 8px;">
                                <div class="progress-bar bg-gradient-success progress-bar-striped progress-bar-animated"
                                    role="progressbar" style="width: {{ ($wizardStep / 3) * 100 }}%;"
                                    aria-valuenow="{{ ($wizardStep / 3) * 100 }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-muted {{ $wizardStep >= 1 ? 'text-primary fw-bold' : '' }}">
                                    <i
                                        class="fas fa-{{ $wizardStep >= 1 ? 'check-circle text-success' : 'circle' }} me-1"></i>
                                    Configuración
                                </small>
                                <small class="text-muted {{ $wizardStep >= 2 ? 'text-primary fw-bold' : '' }}">
                                    <i
                                        class="fas fa-{{ $wizardStep >= 2 ? 'check-circle text-success' : 'circle' }} me-1"></i>
                                    Contenido
                                </small>
                                <small class="text-muted {{ $wizardStep >= 3 ? 'text-primary fw-bold' : '' }}">
                                    <i
                                        class="fas fa-{{ $wizardStep >= 3 ? 'check-circle text-success' : 'circle' }} me-1"></i>
                                    Finalización
                                </small>
                            </div>
                        </div>

                        <!-- Paso 1: Selección de Pensum y tipo -->
                        @if ($wizardStep === 1)
                            <div class="step-content fade-in">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <!-- Corregí la carga de áreas de formación y agregué wire:model.live -->
                                            <select wire:model.live="pensum_id"
                                                class="form-control @error('pensum_id') is-invalid @enderror"
                                                id="pensum_id">
                                                <option value="">Seleccionar área...</option>
                                                @if ($profesor && $profesor->pensums && $profesor->pensums->count() > 0)
                                                    @foreach ($profesor->pensums as $pensum)
                                                        <option value="{{ $pensum->id }}">
                                                            {{ $pensum->asignatura ? $pensum->full_name : 'Sin asignatura' }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    <option disabled>No hay áreas de formación disponibles</option>
                                                @endif
                                            </select>
                                            <label for="pensum_id">
                                                <i class="fas fa-graduation-cap me-1"></i>
                                                Área de Formación *
                                            </label>
                                            @error('pensum_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Agregué información de debug para áreas de formación -->
                                        @if ($profesor && $profesor->pensums)
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                {{ $profesor->pensums->count() }} área(s) disponible(s)
                                            </small>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <select wire:model.live="tipo_pregunta"
                                                class="form-control @error('tipo_pregunta') is-invalid @enderror"
                                                id="tipo_pregunta">
                                                <option value="">Seleccionar tipo...</option>
                                                <option value="multiple">Opción Múltiple</option>
                                                <option value="open">Respuesta Abierta</option>
                                                <option value="scale">Escala</option>
                                            </select>
                                            <label for="tipo_pregunta">
                                                <i class="fas fa-list me-1"></i>
                                                Tipo de Pregunta *
                                            </label>
                                            @error('tipo_pregunta')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Preview del tipo de pregunta seleccionado -->
                                @if ($tipo_pregunta)
                                    <div class="alert alert-info border-0 shadow-sm">
                                        <div class="d-flex align-items-center">
                                            @if ($tipo_pregunta === 'multiple')
                                                <i class="fas fa-list-ul fa-2x text-primary me-3"></i>
                                                <div>
                                                    <h6 class="mb-1">Pregunta de Opción Múltiple</h6>
                                                    <small class="text-muted">Los estudiantes seleccionarán una
                                                        respuesta de varias opciones.</small>
                                                </div>
                                            @elseif($tipo_pregunta === 'open')
                                                <i class="fas fa-edit fa-2x text-success me-3"></i>
                                                <div>
                                                    <h6 class="mb-1">Pregunta Abierta</h6>
                                                    <small class="text-muted">Los estudiantes escribirán su respuesta
                                                        libremente.</small>
                                                </div>
                                            @else
                                                <i class="fas fa-sliders-h fa-2x text-info me-3"></i>
                                                <div>
                                                    <h6 class="mb-1">Pregunta de Escala</h6>
                                                    <small class="text-muted">Los estudiantes calificarán en una escala
                                                        numérica.</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Paso 2: Texto y opciones -->
                        @if ($wizardStep === 2)
                            <div class="step-content fade-in">
                                <div class="form-floating mb-4">
                                    <textarea wire:model.defer="pregunta" class="form-control @error('pregunta') is-invalid @enderror" id="pregunta"
                                        rows="4" style="height: 120px;" placeholder="Escribe aquí tu pregunta..."></textarea>
                                    <label for="pregunta">
                                        <i class="fas fa-edit me-1"></i>
                                        Texto de la Pregunta *
                                    </label>
                                    @error('pregunta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Mínimo 10 caracteres, máximo 500 caracteres
                                        </small>
                                    </div>
                                </div>

                                @if ($tipo_pregunta === 'multiple')
                                    <!-- Opciones múltiples -->
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Opciones de Respuesta *
                                            </h6>
                                            <small class="text-muted">Selecciona la opción correcta marcando el
                                                círculo</small>
                                        </div>
                                        <div class="card-body">
                                            @foreach ($options as $index => $option)
                                                <div
                                                    class="option-item mb-3 p-3 border rounded {{ $correct_option_index == $index ? 'border-success bg-light' : 'border-light' }}">
                                                    <div class="d-flex align-items-center">
                                                        <div class="form-check me-3">
                                                            <input wire:model.defer="correct_option_index"
                                                                type="radio" value="{{ $index }}"
                                                                name="correct_option" class="form-check-input"
                                                                id="correct_{{ $index }}">
                                                            <label class="form-check-label"
                                                                for="correct_{{ $index }}">
                                                                <span
                                                                    class="badge bg-{{ $correct_option_index == $index ? 'success' : 'secondary' }}">
                                                                    {{ chr(65 + $index) }}
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="flex-grow-1 me-2">
                                                            <input
                                                                wire:model.defer="options.{{ $index }}.opcion"
                                                                type="text"
                                                                class="form-control @error('options.' . $index . '.opcion') is-invalid @enderror"
                                                                placeholder="Opción {{ $index + 1 }}">
                                                            @error('options.' . $index . '.opcion')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="me-2" style="width: 100px;">
                                                            <input
                                                                wire:model.defer="options.{{ $index }}.valor"
                                                                type="number" class="form-control form-control-sm"
                                                                placeholder="Valor" min="0" max="100">
                                                        </div>
                                                        @if (count($options) > 2)
                                                            <button type="button"
                                                                wire:click="removeOption({{ $index }})"
                                                                class="btn btn-outline-danger btn-sm">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach

                                            @if (count($options) < 6)
                                                <div class="text-center">
                                                    <button type="button" wire:click="addOption"
                                                        class="btn btn-outline-primary">
                                                        <i class="fas fa-plus me-1"></i>
                                                        Agregar Opción
                                                    </button>
                                                </div>
                                            @endif

                                            @error('correct_option_index')
                                                <div class="alert alert-danger mt-3">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                @endif

                                @if ($tipo_pregunta === 'scale')
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                <i class="fas fa-sliders-h me-1"></i>
                                                Configuración de Escala
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input wire:model.defer="min_value" type="number"
                                                            min="1" max="10"
                                                            class="form-control @error('min_value') is-invalid @enderror"
                                                            id="min_value">
                                                        <label for="min_value">Valor Mínimo *</label>
                                                        @error('min_value')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3">
                                                        <input wire:model.defer="max_value" type="number"
                                                            min="2" max="10"
                                                            class="form-control @error('max_value') is-invalid @enderror"
                                                            id="max_value">
                                                        <label for="max_value">Valor Máximo *</label>
                                                        @error('max_value')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Preview visual de la escala -->
                                            @if ($min_value && $max_value && $min_value < $max_value)
                                                <div class="alert alert-info border-0">
                                                    <h6>Vista previa de la escala:</h6>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        @for ($i = $min_value; $i <= $max_value; $i++)
                                                            <div class="text-center">
                                                                <div class="btn btn-outline-primary btn-sm mb-1">
                                                                    {{ $i }}</div>
                                                            </div>
                                                        @endfor
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                @if ($tipo_pregunta === 'open')
                                    <div class="alert alert-success border-0 shadow-sm">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-pen fa-2x text-success me-3"></i>
                                            <div>
                                                <h6 class="mb-1">Pregunta Abierta Configurada</h6>
                                                <p class="mb-0">Los estudiantes podrán escribir su respuesta
                                                    libremente en un campo de texto.</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Paso 3: Configuración final -->
                        @if ($wizardStep === 3)
                            <div class="step-content fade-in">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3">
                                            <input wire:model.defer="orden" type="number" min="1"
                                                class="form-control @error('orden') is-invalid @enderror"
                                                id="orden" placeholder="Orden de la pregunta">
                                            <label for="orden">
                                                <i class="fas fa-sort-numeric-up me-1"></i>
                                                Orden
                                            </label>
                                            @error('orden')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3">
                                            <input wire:model.defer="weighing" type="number" min="1"
                                                max="5"
                                                class="form-control @error('weighing') is-invalid @enderror"
                                                id="weighing" placeholder="Peso">
                                            <label for="weighing">
                                                <i class="fas fa-balance-scale me-1"></i>
                                                Ponderación (1-5) *
                                            </label>
                                            @error('weighing')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3">
                                            <select wire:model.defer="difficulty"
                                                class="form-select @error('difficulty') is-invalid @enderror"
                                                id="difficulty">
                                                <option value="">Seleccionar dificultad...</option>
                                                <option value="easy">Fácil</option>
                                                <option value="medium">Media</option>
                                                <option value="hard">Difícil</option>
                                            </select>
                                            <label for="difficulty">
                                                <i class="fas fa-tachometer-alt me-1"></i>
                                                Dificultad *
                                            </label>
                                            @error('difficulty')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-check form-switch mb-4">
                                    <input wire:model.defer="activo" type="checkbox"
                                        class="form-check-input @error('activo') is-invalid @enderror" id="activo">
                                    <label class="form-check-label fw-bold" for="activo">
                                        <i class="fas fa-toggle-on me-1"></i>
                                        Pregunta Activa
                                    </label>
                                    @error('activo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Resumen final de la pregunta -->
                                <div class="card border-0 shadow-sm bg-light">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-eye me-1"></i>
                                            Vista Previa de la Pregunta
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <strong>Pregunta:</strong> {{ $pregunta ?: 'Sin definir' }}
                                        </div>
                                        <div class="mb-2">
                                            <strong>Tipo:</strong>
                                            <span
                                                class="badge bg-{{ $tipo_pregunta === 'multiple' ? 'primary' : ($tipo_pregunta === 'open' ? 'success' : 'info') }}">
                                                {{ $tipo_pregunta === 'multiple' ? 'Opción Múltiple' : ($tipo_pregunta === 'open' ? 'Respuesta Abierta' : 'Escala') }}
                                            </span>
                                        </div>
                                        @if ($tipo_pregunta === 'multiple' && count($options) > 0)
                                            <div class="mb-2">
                                                <strong>Opciones:</strong>
                                                <ul class="list-unstyled ms-3">
                                                    @foreach ($options as $index => $option)
                                                        @if ($option['opcion'])
                                                            <li>
                                                                <span
                                                                    class="badge bg-{{ $correct_option_index == $index ? 'success' : 'secondary' }} me-1">
                                                                    {{ chr(65 + $index) }}
                                                                </span>
                                                                {{ $option['opcion'] }}
                                                                @if ($correct_option_index == $index)
                                                                    <i class="fas fa-check text-success ms-1"></i>
                                                                @endif
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Mejoré la estructura del footer para que siempre sea visible -->
                    <div class="modal-footer bg-light border-top" style="position: sticky; bottom: 0; z-index: 10;">
                        <div class="d-flex justify-content-between w-100">
                            <div>
                                @if ($wizardStep > 1)
                                    <button type="button" wire:click="$set('wizardStep', {{ $wizardStep - 1 }})"
                                        class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        Atrás
                                    </button>
                                @endif
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" wire:click="closeQuestionModal"
                                    class="btn btn-outline-danger">
                                    <i class="fas fa-times me-1"></i>
                                    Cancelar
                                </button>

                                @if ($wizardStep < 3)
                                    <button type="button" wire:click="nextStep" class="btn btn-primary">
                                        Siguiente
                                        <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                        <span wire:loading.remove>
                                            <i class="fas fa-save me-1"></i>
                                            {{ $editingQuestion ? 'Actualizar' : 'Crear' }} Pregunta
                                        </span>
                                        <span wire:loading>
                                            <i class="fas fa-spinner fa-spin me-1"></i>
                                            Guardando...
                                        </span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@section('stylesheet')
    @parent
    <style>
        /* Mejoré los estilos del modal para mejor visualización */
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(2px);
        }

        .modal-xl {
            max-width: 1200px;
        }

        .modal-dialog-scrollable .modal-content {
            max-height: 90vh;
        }

        .modal-footer {
            background-color: #f8f9fa !important;
            border-top: 2px solid #dee2e6 !important;
            padding: 1rem 1.5rem;
        }

        .gap-2 {
            gap: 0.5rem !important;
        }
    </style>
@endsection

@section('scripts')
    @parent
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('show-question-modal', function() {
                document.body.classList.add('modal-open');
            });

            window.addEventListener('hide-question-modal', function() {
                document.body.classList.remove('modal-open');
            });

            document.addEventListener('input', function(e) {
                if (e.target.matches('[wire\\:model*="options"]')) {
                    const optionContainer = e.target.closest('.option-item');
                    if (e.target.value.trim()) {
                        optionContainer.classList.add('border-success');
                        optionContainer.classList.remove('border-light');
                    } else {
                        optionContainer.classList.remove('border-success');
                        optionContainer.classList.add('border-light');
                    }
                }
            });

            Livewire.on('questionModalOpened', () => {
                setTimeout(() => {
                    const firstInput = document.querySelector('#pensum_id');
                    if (firstInput) firstInput.focus();
                }, 100);
            });
        });
    </script>
@endsection
