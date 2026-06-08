<div wire:ignore.self class="modal fade" id="showActivityModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header alert-info p-2">
                <h5 class="modal-title font-weight-bolder p-1">
                    <i class="fas fa-eye text-info mr-1"></i>
                    Detalle de Actividad
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-3">
                @if ($activity ?? false)
                    @php
                        $pev = $activity->pevaluacion;
                    @endphp

                    {{-- Contexto --}}
                    <div class="alert alert-secondary p-2 mb-3 small">
                        <div class="d-flex justify-content-between flex-wrap">
                            <span><strong>Asignatura:</strong> {{ $pev->pensum->asignatura->name ?? '' }}</span>
                            <span><strong>Grado/Sección:</strong> {{ $pev->pensum->grado->name ?? '' }} {{ $pev->seccion->name ?? '' }}</span>
                            <span><strong>Lapso:</strong> {{ $pev->lapso->name ?? '' }}</span>
                        </div>
                        <div class="mt-1">
                            <strong>Profesor:</strong> {{ $pev->profesor->fullname ?? $pev->profesor->full_name ?? '' }}
                        </div>
                    </div>

                    {{-- Fechas --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="small font-weight-bold text-muted d-block">Fecha Inicial</label>
                            <span class="badge badge-light border p-2">{{ \Carbon\Carbon::parse($activity->finicial)->format('d/m/Y') }}</span>
                        </div>
                        <div class="col-md-6">
                            <label class="small font-weight-bold text-muted d-block">Fecha Final</label>
                            <span class="badge badge-light border p-2">{{ \Carbon\Carbon::parse($activity->ffinal)->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    {{-- Nombre --}}
                    @if($activity->name)
                    <div class="mb-3">
                        <label class="small font-weight-bold text-muted d-block">Nombre</label>
                        <p class="mb-0">{{ $activity->name }}</p>
                    </div>
                    @endif

                    {{-- Campos --}}
                    @foreach([
                        'topic' => 'Tema Generador y Énfasis',
                        'thematic' => 'Tejido Temático / Tema Indispensable',
                        'references' => 'Referentes Teórico Prácticos y Éticos',
                        'teaching' => 'Enseñanza / Actividad Globalizada',
                        'learning' => 'Aprendizaje',
                        'description' => 'Actividad Evaluativa',
                        'achievement' => 'Indicadores de Logro',
                    ] as $field => $label)
                        @if($activity->$field)
                        <div class="mb-3">
                            <label class="small font-weight-bold text-muted d-block">{{ $label }}</label>
                            <p class="mb-0 p-2 bg-light rounded small">{{ $activity->$field }}</p>
                        </div>
                        @endif
                    @endforeach

                    {{-- Ponderación y Estado --}}
                    <div class="row mb-3">
                        @if($activity->weighting)
                        <div class="col-md-4">
                            <label class="small font-weight-bold text-muted d-block">Ponderación</label>
                            <span class="badge badge-primary p-2">{{ $activity->weighting }}%</span>
                        </div>
                        @endif
                        <div class="col-md-4">
                            <label class="small font-weight-bold text-muted d-block">Estado</label>
                            <span class="badge badge-{{ $activity->status ? 'success' : 'warning' }} p-2">
                                <i class="fa fa-{{ $activity->status ? 'check' : 'clock' }} mr-1"></i>
                                {{ $activity->status ? 'Aprobada' : 'Pendiente' }}
                            </span>
                        </div>
                        <div class="col-md-4">
                            <label class="small font-weight-bold text-muted d-block">Actividad Evaluativa</label>
                            <span class="badge badge-{{ $activity->getStatusResumeAttribute() ? 'success' : 'secondary' }} p-2">
                                <i class="fa fa-{{ $activity->getStatusResumeAttribute() ? 'check-circle' : 'minus-circle' }} mr-1"></i>
                                {{ $activity->getStatusResumeAttribute() ? 'Completa' : 'Sin Descripción' }}
                            </span>
                        </div>
                    </div>

                    {{-- Observaciones --}}
                    @if($activity->observations)
                    <div class="mb-3">
                        <label class="small font-weight-bold text-muted d-block">ODS / Sistematización</label>
                        <p class="mb-0 p-2 bg-light rounded small">{{ $activity->observations }}</p>
                    </div>
                    @endif

                    {{-- Comentarios --}}
                    @if($activity->comments)
                    <div class="mb-3">
                        <label class="small font-weight-bold text-muted d-block">Comentarios</label>
                        <p class="mb-0 p-2 bg-info-light rounded small text-muted font-italic">"{{ $activity->comments }}"</p>
                    </div>
                    @endif
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Cargando datos...</p>
                    </div>
                @endif
            </div>

            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cerrar
                </button>
                @if ($activity ?? false)
                <button type="button" class="btn btn-sm btn-warning" wire:click="setEditAct({{ $activity->id }})" data-dismiss="modal">
                    <i class="fas fa-edit mr-1"></i> Editar
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('open-show-modal', event => {
        $('#showActivityModal').modal('show');
    });
    window.addEventListener('close-show-modal', event => {
        $('#showActivityModal').modal('hide');
    });
</script>
