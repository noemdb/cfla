<div class="modal fade" id="referentDetailModal" tabindex="-1" role="dialog" aria-labelledby="referentDetailModalLabel"
    aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            @if ($currentReferent)
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="referentDetailModalLabel">
                        <i class="fas fa-book mr-2"></i>Detalle del Referente: {{ $currentReferent->name }}
                    </h5>
                    <button type="button" class="close text-white" wire:click="closeDetailModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <!-- Resumen del Referente -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Código:</strong> <span
                                        class="badge badge-secondary">{{ $currentReferent->code }}</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Versión:</strong> <span
                                        class="badge badge-info">{{ $currentReferent->version }}</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Estado:</strong>
                                    @if ($currentReferent->active)
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-secondary">Inactivo</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-2">
                                <strong>Descripción:</strong>
                                <p class="mb-0 text-muted">{{ $currentReferent->description }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros: Plan de Estudio y Grado -->
                    <!-- Info Plan y Filtro Grado -->
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-6">
                            <label class="small font-weight-bold text-muted d-block">Plan de Estudio:</label>
                            <span class="h6 text-primary">
                                <i
                                    class="fas fa-university mr-2"></i>{{ $currentReferent->pestudio->name ?? 'No asignado' }}
                            </span>
                        </div>
                        <!-- Filtro de Grado -->
                        @if ($this->detailGrados->count() > 0)
                            <div class="col-md-3">
                                <label for="detailFilterGrado" class="small font-weight-bold text-muted">Filtrar por
                                    Grado:</label>
                                <select wire:model="detailFilterGradoId" id="detailFilterGrado"
                                    class="form-control form-control-sm">
                                    <option value="">Todos los grados</option>
                                    @foreach ($this->detailGrados as $grado)
                                        <option value="{{ $grado->id }}">{{ $grado->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Filtro de Pensum (Asignatura) -->
                        @if ($this->detailPensums->count() > 0)
                            <div class="col-md-3">
                                <label for="detailFilterPensum" class="small font-weight-bold text-muted">Filtrar por
                                    Asignatura:</label>
                                <select wire:model="detailFilterPensumId" id="detailFilterPensum"
                                    class="form-control form-control-sm">
                                    <option value="">Todas las asignaturas</option>
                                    @foreach ($this->detailPensums as $pensum)
                                        <option value="{{ $pensum->id }}">{{ $pensum->fullname }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    <!-- Listado de Competencias -->
                    <h5 class="mb-3 text-secondary">
                        <i class="fas fa-tasks mr-2"></i>Competencias e Indicadores
                        <small class="text-muted ml-2" style="font-size: 0.8rem">
                            (Total: {{ $this->detailCompetencies->total() }})
                        </small>
                    </h5>

                    @if ($this->detailCompetencies->count() > 0)
                        <div class="accordion" id="accordionCompetencies">
                            @foreach ($this->detailCompetencies as $index => $competency)
                                <div class="card mb-1 border-info">
                                    <div class="card-header bg-white p-2" id="heading{{ $competency->id }}">
                                        <h2 class="mb-0">
                                            <button
                                                class="btn btn-link btn-block text-left text-dark font-weight-bold d-flex justify-content-between align-items-center"
                                                type="button" data-toggle="collapse"
                                                data-target="#collapse{{ $competency->id }}" aria-expanded="true"
                                                aria-controls="collapse{{ $competency->id }}">
                                                <div class="d-flex flex-column">
                                                    <span>
                                                        <span
                                                            class="badge badge-primary mr-2">{{ $this->detailCompetencies->firstItem() + $index }}</span>
                                                        {{ $competency->name }}
                                                    </span>
                                                    @if ($competency->pensum)
                                                        <small class="text-muted ml-4 mt-1">
                                                            <i class="fas fa-layer-group text-info mr-1"></i>
                                                            {{ $competency->pensum->grado->name ?? 'Grado Desconocido' }}
                                                            <span class="text-secondary mx-1">|</span>
                                                            {{ $competency->pensum->pestudio->name ?? 'Plan General' }}
                                                        </small>
                                                    @endif
                                                </div>
                                                <span class="badge badge-light border">
                                                    {{ $competency->indicators->count() }} indicadores
                                                </span>
                                            </button>
                                        </h2>
                                    </div>

                                    <div id="collapse{{ $competency->id }}" class="collapse"
                                        aria-labelledby="heading{{ $competency->id }}"
                                        data-parent="#accordionCompetencies">
                                        <div class="card-body bg-white">
                                            <p class="small text-muted mb-3 font-italic">{{ $competency->description }}
                                            </p>

                                            <!-- Indicadores -->
                                            @if ($competency->indicators->count() > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered table-hover mb-0">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th width="15%" class="text-center">Código</th>
                                                                <th width="70%">Indicador</th>
                                                                <th width="15%" class="text-center">Nivel Esperado
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($competency->indicators as $indicator)
                                                                @php $levelInfo = $this->getLevelLabel($indicator->expected_level) @endphp
                                                                <tr>
                                                                    <td class="text-center text-muted font-weight-bold">
                                                                        {{ $indicator->code }}</td>
                                                                    <td>{{ $indicator->description }}</td>
                                                                    <td class="text-center">
                                                                        <span
                                                                            class="badge badge-{{ $levelInfo['class'] }}">
                                                                            {{ $levelInfo['label'] }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="alert alert-warning small mb-0 p-2">
                                                    No hay indicadores registrados para esta competencia.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-2 text-right">
                            {{ $this->detailCompetencies->links() }}
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            Este referente no tiene competencias registradas.
                        </div>
                    @endif

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeDetailModal">Cerrar</button>
                    <button type="button" class="btn btn-primary"
                        wire:click="editReferent({{ $currentReferent->id }})">
                        <i class="fas fa-edit mr-1"></i> Editar Referente
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    window.addEventListener('open-referent-detail-modal', event => {
        $('#referentDetailModal').modal('show');
    });

    window.addEventListener('close-referent-detail-modal', event => {
        $('#referentDetailModal').modal('hide');
    });
</script>
