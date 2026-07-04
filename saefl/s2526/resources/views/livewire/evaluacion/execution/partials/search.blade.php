<div class="card-header p-0 m-0 mb-3">
    <!-- Fila 1: Filtros Principales -->
    <div class="form-row">
        <!-- Grado - Siempre visible -->
        <div class="col-12 col-sm-6 col-md-3 mb-2 mb-md-0">
            <label for="grado_id" class="font-weight-bold m-0 small">Grado</label>
            {!! Form::select('grado_id', $list_grado, 'grado_id', [
                'wire:model' => 'grado_id',
                'class' => 'form-control form-control-sm',
                'id' => 'grado_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>

        <!-- Sección - Visible en sm+ -->
        <div class="col-12 col-sm-6 col-md-3 mb-2 mb-md-0">
            <label for="seccion_id" class="font-weight-bold m-0 small">Sección</label>
            {!! Form::select('seccion_id', $list_seccion, 'seccion_id', [
                'wire:model.defer' => 'seccion_id',
                'class' => 'form-control form-control-sm',
                'id' => 'seccion_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>

        <!-- Momento - Visible en md+ -->
        <div class="col-12 col-sm-6 col-md-3 mb-2 mb-md-0 d-none d-md-block">
            <label for="lapso_id" class="font-weight-bold m-0 small">Momento</label>
            {!! Form::select('lapso_id', $list_lapso, 'lapso_id', [
                'wire:model.defer' => 'lapso_id',
                'class' => 'form-control form-control-sm',
                'id' => 'lapso_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>

        <!-- Asignatura - Visible en md+ -->
        <div class="col-12 col-sm-6 col-md-3 mb-2 mb-md-0 d-none d-md-block">
            <label for="pensum_id" class="font-weight-bold m-0 small">Asignatura</label>
            {!! Form::select('pensum_id', $list_pensum, 'pensum_id', [
                'wire:model.defer' => 'pensum_id',
                'class' => 'form-control form-control-sm',
                'id' => 'pensum_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
    </div>

    <!-- Fila 2: Filtros Secundarios y Acciones -->
    <div class="form-row pt-2">
        <!-- Profesor - Visible en sm+ -->
        <div class="col-12 col-sm-6 col-md-3 mb-2 mb-md-0">
            <label for="profesor_id" class="font-weight-bold m-0 small">Profesor</label>
            {!! Form::select('profesor_id', $list_profesor, 'profesor_id', [
                'wire:model.defer' => 'profesor_id',
                'class' => 'form-control form-control-sm',
                'id' => 'profesor_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>

        <!-- Fecha Inicial - Siempre visible -->
        <div class="col-12 col-sm-6 col-md-3 mb-2 mb-md-0">
            <label for="finicial" class="font-weight-bold m-0 small">Fecha Inicial</label>
            <input type="date" wire:model.defer="finicial" class="form-control form-control-sm" id="finicial">
        </div>

        <!-- Fecha Final - Siempre visible -->
        <div class="col-12 col-sm-6 col-md-3 mb-2 mb-md-0">
            <label for="ffinal" class="font-weight-bold m-0 small">Fecha Final</label>
            <input type="date" wire:model.defer="ffinal" class="form-control form-control-sm" id="ffinal">
        </div>

        <!-- Estado y Botones - Siempre visible -->
        <div class="col-12 col-sm-6 col-md-3">
            <div
                class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-end h-100">
                <!-- Estado - Visible en sm+ -->
                <div class="flex-grow-1 mb-2 mb-sm-0 mr-sm-2 w-100">
                    <label for="status_execution" class="font-weight-bold m-0 small">Estado</label>
                    {!! Form::select(
                        'status_execution',
                        [
                            true => 'Ejecutada',
                            false => 'Pendiente',
                        ],
                        'status_execution',
                        [
                            'wire:model.defer' => 'status_execution',
                            'class' => 'form-control form-control-sm',
                            'id' => 'status_execution',
                            'placeholder' => 'Todos',
                        ],
                    ) !!}
                </div>

                <!-- Botones de Acción - Siempre visible -->
                <div class="d-flex align-items-center w-100 w-sm-auto mt-2 mt-sm-0">
                    <!-- Botón Buscar -->
                    <button class="btn btn-primary btn-sm flex-grow-1 flex-sm-grow-0 mr-1" type="button"
                        wire:click="search()" title="Ejecutar búsqueda con los filtros aplicados">
                        <i class="fas fa-search d-none d-sm-inline mr-1"></i>
                        <span class="d-inline d-sm-none">Buscar</span>
                        <span class="d-none d-sm-inline">Buscar</span>
                    </button>

                    <!-- Botón Reset -->
                    <button class="btn btn-outline-secondary btn-sm flex-grow-1 flex-sm-grow-0 ml-1" type="button"
                        wire:click="resetFilters()" title="Limpiar todos los filtros">
                        <i class="fas fa-redo d-none d-sm-inline mr-1"></i>
                        <span class="d-inline d-sm-none">Reset</span>
                        <span class="d-none d-sm-inline">Reset</span>
                    </button>

                    <!-- Loading indicator -->
                    <span class="ml-2" wire:loading>
                        <i class="fas fa-spinner fa-spin text-primary"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 3: Filtros Móviles Colapsados -->
    <div class="row mt-2 d-block d-md-none">
        <div class="col-12">
            <!-- Acordeón para filtros adicionales en móviles -->
            <div class="accordion" id="filtrosMobile">
                <div class="card">
                    <div class="card-header py-1 px-2" id="headingFiltros">
                        <button class="btn btn-link btn-sm p-0 text-decoration-none" type="button"
                            data-toggle="collapse" data-target="#collapseFiltros" aria-expanded="false"
                            aria-controls="collapseFiltros">
                            <i class="fas fa-sliders-h mr-1"></i>
                            <small>Filtros Adicionales</small>
                            <i class="fas fa-chevron-down ml-1"></i>
                        </button>
                    </div>

                    <div id="collapseFiltros" class="collapse" aria-labelledby="headingFiltros"
                        data-parent="#filtrosMobile">
                        <div class="card-body p-2">
                            <div class="form-row">
                                <!-- Momento - Móvil -->
                                <div class="col-12 mb-2">
                                    <label for="lapso_id_mobile" class="font-weight-bold m-0 small">Momento</label>
                                    {!! Form::select('lapso_id_mobile', $list_lapso, 'lapso_id', [
                                        'wire:model.defer' => 'lapso_id',
                                        'class' => 'form-control form-control-sm',
                                        'id' => 'lapso_id_mobile',
                                        'placeholder' => 'Seleccione',
                                    ]) !!}
                                </div>

                                <!-- Asignatura - Móvil -->
                                <div class="col-12 mb-2">
                                    <label for="pensum_id_mobile" class="font-weight-bold m-0 small">Asignatura</label>
                                    {!! Form::select('pensum_id_mobile', $list_pensum, 'pensum_id', [
                                        'wire:model.defer' => 'pensum_id',
                                        'class' => 'form-control form-control-sm',
                                        'id' => 'pensum_id_mobile',
                                        'placeholder' => 'Seleccione',
                                    ]) !!}
                                </div>

                                <!-- Botones Móviles Adicionales -->
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <button class="btn btn-primary btn-sm flex-grow-1 mr-1" type="button"
                                            wire:click="search()">
                                            <i class="fas fa-search mr-1"></i> Buscar
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm flex-grow-1 ml-1" type="button"
                                            wire:click="resetFilters()">
                                            <i class="fas fa-redo mr-1"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
