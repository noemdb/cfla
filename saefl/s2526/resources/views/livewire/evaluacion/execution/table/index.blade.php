<div class="table-responsive">
    <table width="100%" class="table table-striped table-hover table-sm small" id="table-data-default">
        <thead>
            <tr>
                <th class="d-none d-sm-table-cell">N</th>
                <th>Descripción</th>
                <th class="d-none d-md-table-cell">Profesor</th>
                <th>Asignatura</th>
                <th class="d-none d-lg-table-cell">Grado/Sección/Lapso</th>
                <th class="d-none d-lg-table-cell">Fecha</th>
                <th class="text-center">Notas</th>
                <th class="text-center">Promedio</th>
                <th class="text-center">Estado</th>
                <th class="nosort text-center">Acciones</th>
            </tr>
        </thead>

        <tbody>
        @foreach($evaluaciones as $evaluacion)

            @php 
                $pevaluacion = $evaluacion->pevaluacion; 
                $pensum = $pevaluacion->pensum ?? null; 
                $asignatura = $pensum->asignatura ?? null; 
                $profesor = $pevaluacion->profesor ?? null; 
                $grado = $pensum->grado ?? null; 
                $seccion = $pevaluacion->seccion ?? null; 
                $lapso = $pevaluacion->lapso ?? null; 
                $status_active = $profesor->status_active ?? null; 
                $notas_count = (!empty($evaluacion->notas_count)) ? $evaluacion->notas_count:null;
                $promedio = $evaluacion->promedio; 
            @endphp

            <tr class="table-{{($notas_count == 0) ? 'danger':'default'}} text-{{ ($evaluacion->status_execution) ? 'success font-weight-bold':'default' }}">
                <!-- Columna N - Visible en sm+ -->
                <td class="d-none d-sm-table-cell">
                    {{ (($evaluaciones->currentPage() - 1) * $evaluaciones->perPage()) + $loop->iteration }}
                    @admin [{{$pevaluacion->id ?? ''}}] @endadmin
                </td>

                <!-- Columna Descripción - Siempre visible -->
                <td>
                    <div class="d-block d-sm-none font-weight-bold small mb-1">
                        #{{ (($evaluaciones->currentPage() - 1) * $evaluaciones->perPage()) + $loop->iteration }}
                    </div>
                    <span class="text-uppercase" title="{{ $evaluacion->description ?? ''}}">
                        {{ Str::limit($evaluacion->description,20,'...') ?? ''}}
                    </span>
                    
                    <!-- Información móvil compacta -->
                    <div class="d-block d-sm-none mt-1">
                        <div class="row small text-muted">
                            <div class="col-6">
                                <strong>Asig:</strong> {{ ($asignatura) ? Str::limit($asignatura->code,8,'...') : 'N/A'}}
                            </div>
                            <div class="col-6">
                                <strong>Notas:</strong> {{ $notas_count }}
                            </div>
                        </div>
                        <div class="row small text-muted">
                            <div class="col-6">
                                <strong>Prom:</strong> {{ number_format($promedio, 1) }}
                            </div>
                            <div class="col-6">
                                <strong>Estado:</strong> 
                                @if($evaluacion->status_execution)
                                    <span class="text-success">✓</span>
                                @else
                                    <span class="text-warning">!</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </td>

                <!-- Columna Profesor - Visible en md+ -->
                <td class="d-none d-md-table-cell text-{{ ($status_active == 'false') ? 'secondary':'dark' }}" title="{{$profesor->fullname ?? ''}}">
                    {{ ($profesor->fullname ?? false) ? Str::limit($profesor->fullname,20,'...') : 'N/A' }}
                </td>

                <!-- Columna Asignatura - Siempre visible -->
                <td>
                    <div class="d-none d-sm-block" title="{{$asignatura->name ?? '' }}">
                        {{ ($asignatura) ? Str::limit($asignatura->code,15,'...') : 'N/A'}}
                        <div class="text-muted small">
                            {{ ($asignatura) ? Str::limit($asignatura->name,25,'...') : 'N/A'}}
                        </div>
                    </div>
                </td>

                <!-- Columna Grado/Sección/Lapso - Visible en lg+ -->
                <td class="d-none d-lg-table-cell">
                    {{ $grado->name ?? 'N/A'}}
                    {{ $seccion->name ?? ''}} ||
                    {{ $lapso->name ?? 'N/A'}}
                </td>

                <!-- Columna Fecha - Visible en lg+ -->
                <td class="d-none d-lg-table-cell text-nowrap">
                    {{ f_date($evaluacion->fecha) ?? ''}}
                </td>

                <!-- Columna Notas - Siempre visible -->
                <td class="text-center">
                    <span class="badge badge-{{ $notas_count == 0 ? 'danger' : 'success' }}">
                        {{ $notas_count }}
                    </span>
                </td>

                <!-- Columna Promedio - Siempre visible -->
                <td class="text-center">
                    <span class="font-weight-bold {{ $promedio == 0 ? 'text-muted' : 'text-dark' }}">
                        {{ number_format($promedio, 1) }}
                    </span>
                </td>

                <!-- Columna Estado - Siempre visible -->
                <td class="text-center">
                    @if($evaluacion->status_execution)
                        <span class="badge badge-success d-none d-sm-inline">Ejecutada</span>
                        <span class="badge badge-success d-inline d-sm-none">✓</span>
                    @else
                        <span class="badge badge-warning d-none d-sm-inline">Pendiente</span>
                        <span class="badge badge-warning d-inline d-sm-none">!</span>
                    @endif
                </td>

                <!-- Columna Acciones - Siempre visible -->
                <td class="text-center">
                    @if ($evaluacion->status_execution)
                    <a title="Marcar como Pendiente" class="btn btn-success btn-sm" href="#" wire:click="change({{$evaluacion->id}},false)" role="button">
                        <i class="fas fa-check d-none d-sm-inline"></i>
                        <i class="fas fa-undo d-inline d-sm-none"></i>
                    </a>
                    @else
                    <a title="Marcar como Ejecutada" class="btn btn-warning btn-sm" href="#" wire:click="change({{$evaluacion->id}},true)" role="button">
                        <i class="fas fa-exclamation d-none d-sm-inline"></i>
                        <i class="fas fa-check d-inline d-sm-none"></i>
                    </a>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<!-- Mensaje cuando no hay resultados -->
@if($evaluaciones->count() == 0)
<div class="text-center py-4">
    <i class="fas fa-search fa-2x text-muted mb-2"></i>
    <p class="text-muted">No se encontraron evaluaciones con los filtros aplicados.</p>
    <button class="btn btn-outline-primary btn-sm" wire:click="$set('grado_id', null)">
        <i class="fas fa-redo mr-1"></i> Limpiar Filtros
    </button>
</div>
@endif