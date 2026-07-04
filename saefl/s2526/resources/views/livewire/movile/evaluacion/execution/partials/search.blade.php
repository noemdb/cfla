<div class="card mb-2 border-0">
    <div class="card-body p-2">
        <!-- Filtro Principal - Grado -->
        <div class="mb-2">
            <label for="grado_id" class="form-label small fw-bold mb-1">Grado</label>
            @php
                $name = 'grado_id';
                $model = $name;
            @endphp
            {!! Form::select($model, $list_grado, old($model), [
                'wire:model' => $model,
                'class' => 'form-select form-select-sm',
                'id' => $model,
                'placeholder' => 'Seleccione Grado',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror

            <!-- Fechas -->
            <div class="row g-2">
                <div class="col-6">
                    <label for="finicial" class="form-label small fw-bold mb-1">Fecha Inicial</label>
                    <input type="date" class="form-control form-control-sm" wire:model="finicial" id="finicial">
                    @error('finicial')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-6">
                    <label for="ffinal" class="form-label small fw-bold mb-1">Fecha Final</label>
                    <input type="date" class="form-control form-control-sm" wire:model="ffinal" id="ffinal">
                    @error('ffinal')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Filtros Secundarios en Acordeón -->
        <div class="accordion accordion-flush" id="filtersAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse"
                        data-bs-target="#filtersCollapse">
                        <i class="fas fa-sliders-h me-2 small"></i>
                        <span class="small">Más Filtros</span>
                    </button>
                </h2>
                <div id="filtersCollapse" class="accordion-collapse collapse" data-bs-parent="#filtersAccordion">
                    <div class="accordion-body p-2">
                        <!-- Sección -->
                        <div class="mb-2">
                            <label for="seccion_id" class="form-label small fw-bold mb-1">Sección</label>
                            @php
                                $name = 'seccion_id';
                                $model = $name;
                            @endphp
                            {!! Form::select($model, $list_seccion, old($model), [
                                'wire:model' => $model,
                                'class' => 'form-select form-select-sm',
                                'id' => $model,
                                'placeholder' => 'Todas las Secciones',
                            ]) !!}
                            @error($model)
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Momento -->
                        <div class="mb-2">
                            <label for="lapso_id" class="form-label small fw-bold mb-1">Momento</label>
                            @php
                                $name = 'lapso_id';
                                $model = $name;
                            @endphp
                            {!! Form::select($model, $list_lapso, old($model), [
                                'wire:model' => $model,
                                'class' => 'form-select form-select-sm',
                                'id' => $model,
                                'placeholder' => 'Todos los Momentos',
                            ]) !!}
                            @error($model)
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Asignatura -->
                        <div class="mb-2">
                            <label for="pensum_id" class="form-label small fw-bold mb-1">Asignatura</label>
                            @php
                                $name = 'pensum_id';
                                $model = $name;
                            @endphp
                            {!! Form::select($model, $list_pensum ?? [], old($model), [
                                'wire:model' => $model,
                                'class' => 'form-select form-select-sm',
                                'id' => $model,
                                'placeholder' => 'Todas las Asignaturas',
                            ]) !!}
                            @error($model)
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Profesor -->
                        <div class="mb-2">
                            <label for="profesor_id" class="form-label small fw-bold mb-1">Profesor</label>
                            @php
                                $name = 'profesor_id';
                                $model = $name;
                            @endphp
                            {!! Form::select($model, $list_profesor ?? [], old($model), [
                                'wire:model' => $model,
                                'class' => 'form-select form-select-sm',
                                'id' => $model,
                                'placeholder' => 'Todos los Profesores',
                            ]) !!}
                            @error($model)
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Estado -->
                        <div class="mb-2">
                            <label for="status_execution" class="form-label small fw-bold mb-1">Estado</label>
                            @php
                                $name = 'status_execution';
                                $model = $name;
                            @endphp
                            {!! Form::select(
                                $model,
                                [
                                    '' => 'Todos los Estados',
                                    '1' => 'Ejecutada',
                                    '0' => 'Pendiente',
                                ],
                                old($model),
                                [
                                    'wire:model' => $model,
                                    'class' => 'form-select form-select-sm',
                                    'id' => $model,
                                ],
                            ) !!}
                            @error($model)
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>


                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="d-flex gap-2 mt-3">
            <button class="btn btn-primary btn-sm flex-fill" wire:click="search" wire:loading.attr="disabled">
                <i class="fas fa-search me-1"></i>
                <span>Buscar</span>
                <span wire:loading wire:target="search" class="spinner-border spinner-border-sm ms-1"></span>
            </button>
            <button class="btn btn-outline-secondary btn-sm" wire:click="resetFilters" wire:loading.attr="disabled">
                <i class="fas fa-redo me-1"></i>
                <span>Reset</span>
            </button>
        </div>
    </div>
</div>
