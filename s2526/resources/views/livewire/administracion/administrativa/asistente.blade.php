<div class="container-fluid my-3">
    <h5 class="mb-3 font-weight-bold text-primary alert alert-secondary">
        Asistente: Inscripción Administrativa Extemporánea & Recargos.
    </h5>

    {{-- Step indicator --}}
    <div class="mb-3">
        <div class="btn-group btn-group-sm" role="group">
            <button class="btn btn-outline-secondary {{ $step == 1 ? 'active' : '' }}">1. Buscar</button>
            <button class="btn btn-outline-secondary {{ $step == 2 ? 'active' : '' }}">2. Seleccionar Plan</button>
            <button class="btn btn-outline-secondary {{ $step == 3 ? 'active' : '' }}">3. Seleccionar Cuotas</button>
            <button class="btn btn-outline-secondary {{ $step == 4 ? 'active' : '' }}">4. Resumen</button>
        </div>
    </div>

    {{-- STEP 1: búsqueda --}}
    @if ($step == 1)
        <div class="card mb-3">
            <div class="card-body">
                <label class="small text-muted">Buscar estudiante (nombre, apellido, cédula)</label>
                <input wire:model.debounce.400ms="search" class="form-control form-control-sm"
                    placeholder="Mínimo 2 caracteres...">

                @if (!empty($students))
                    <ul class="list-group list-group-flush mt-2">
                        @foreach ($students as $s)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                <div>
                                    <div class="font-weight-bold small">{{ $s['name'] ?? $s->name }}
                                        {{ $s['lastname'] ?? $s->lastname }}</div>
                                    <div class="small text-muted">CI: {{ $s['ci_estudiant'] ?? $s->ci_estudiant }} • ID:
                                        {{ $s['id'] ?? $s->id }}</div>
                                </div>
                                <div class="text-right">
                                    {{-- Estado académico --}}
                                    @if (!empty($s['is_academic_inscribed']) && $s['is_academic_inscribed'])
                                        <span class="badge badge-success mr-1">Académico</span>
                                    @else
                                        <span class="badge badge-secondary mr-1">No académico</span>
                                    @endif

                                    {{-- Estado de solvencia --}}
                                    @if (!empty($s['is_solvent']) && $s['is_solvent'])
                                        <span class="badge badge-success mr-2">Solvente</span>
                                    @else
                                        <span class="badge badge-danger mr-2">No solvente</span>
                                    @endif

                                    {{-- Estado administrativo --}}
                                    @if (!empty($s['is_admin_inscribed']) && $s['is_admin_inscribed'])
                                        <span class="badge badge-info mr-2">Administrativo (afecta)</span>
                                        <button class="btn btn-sm btn-primary" disabled>Seleccionar</button>
                                    @elseif(!empty($s['has_admin_non_affecting']) && $s['has_admin_non_affecting'])
                                        <span class="badge badge-secondary mr-2">Administrativo (no afecta)</span>
                                        <button wire:click="selectStudent({{ $s['id'] ?? $s->id }})"
                                            class="btn btn-sm btn-primary">Seleccionar</button>
                                    @else
                                        <span class="badge badge-warning mr-2">No administrativo</span>
                                        <button wire:click="selectStudent({{ $s['id'] ?? $s->id }})"
                                            class="btn btn-sm btn-primary">Seleccionar</button>
                                    @endif

                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif

            </div>
        </div>
    @endif

    {{-- STEP 2: plan --}}
    @if ($step == 2)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $selectedEstudiant->name }} {{ $selectedEstudiant->lastname }}</strong>
                    <div class="small text-muted">CI: {{ $selectedEstudiant->ci_estudiant }} • ID:
                        {{ $selectedEstudiant->id }}</div>
                </div>
                <div>
                    <button wire:click="backToStep(1)" class="btn btn-sm btn-outline-secondary mr-2">Volver</button>
                </div>
            </div>
            <div class="card-body">
                <label class="small text-muted">Seleccionar Plan de Pago</label>
                <select wire:model="selectedPlanId" class="form-control form-control-sm">
                    <option value="">-- Seleccione plan --</option>
                    @if (is_array($plans) || $plans instanceof \Illuminate\Support\Collection)
                        @foreach ($plans as $k => $v)
                            @if (is_numeric($k))
                                <option value="{{ $k }}">{{ $v }}</option>
                            @else
                                {{-- Si $plans viene como colección de modelos --}}
                                <option value="{{ $v->id }}">{{ $v->name ?? $v }}</option>
                            @endif
                        @endforeach
                    @endif
                </select>

                @if ($selectedPlanId)
                    <div class="p-2 m-2 border rounded">
                        <div class="small font-weight-bold mb-2">Cuotas:</div>
                        <div class="text-muted mb-3">Todas las cuotas definidas para el plan de pago seleccionado.</div>

                        <div class="row">
                            @forelse($planQuotas as $pq)
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-2">
                                    <div class="border rounded p-2 h-100 small text-center">
                                        <strong>{{ $pq['name'] }}</strong>
                                        <div>USD {{ number_format($pq['monto'], 2) }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-secondary small mb-0">
                                        No hay cuotas definidas para este plan.
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif


                <div class="mt-3">
                    <button wire:click="backToStep(1)" class="btn btn-sm btn-light">Cancelar</button>
                    <button wire:click="$set('step',3)" class="btn btn-sm btn-primary ml-2"
                        @if (!$selectedPlanId) disabled @endif>Siguiente</button>
                </div>
            </div>
        </div>
    @endif

    {{-- STEP 3: cuotas --}}
    @if ($step == 3)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <div>
                    <strong>{{ $selectedEstudiant->name }} {{ $selectedEstudiant->lastname }}</strong>
                    <div class="small text-muted">Plan seleccionado: {{ $selectedPlanName }}</div>
                </div>
                <div>
                    <button wire:click="backToStep(2)" class="btn btn-sm btn-outline-secondary">Volver</button>
                </div>
            </div>
            <div class="card-body">
                @if ($loadingQuotas)
                    <div class="text-center py-3 small text-muted">Cargando cuotas...</div>
                @else
                    {{-- Texto explicativo global --}}
                    <div class="text-muted mb-2 small">Cuotas habilitadas para cargos por morosidad.</div>

                    <div class="text-muted mb-2 small">
                        {{ count(collect($planQuotas)->where('expired', true)) }} de {{ count($planQuotas) }} cuotas
                        están vencidas y habilitadas para recargo.
                    </div>

                    <div class="row">
                        @forelse($planQuotas as $pq)
                            <div class="col-6 col-md-4 col-lg-3 mb-2">
                                <div class="card h-100 shadow-sm border-0 compact-card">
                                    <div class="card-body p-2 d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong class="small">{{ $pq['name'] }}</strong>
                                            <span title="Habilitada para recargo" class="badge badge-success">✓</span>
                                        </div>
                                        <div class="text-muted small mb-1">
                                            Venc: {{ $pq['date_expiration'] ?? '—' }}
                                        </div>
                                        <div class="small mb-2">
                                            USD <strong>{{ number_format($pq['monto'], 2) }}</strong>
                                        </div>
                                        <div class="mt-auto d-flex justify-content-between align-items-center small">
                                            @if ($pq['expired'])
                                                <div class="form-check form-check-sm m-0">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="chk_{{ $pq['id'] }}"
                                                        wire:click="toggleQuotaSelection({{ $pq['id'] }})"
                                                        @if (isset($selectedQuotas[$pq['id']])) checked @endif>
                                                    <label class="form-check-label"
                                                        for="chk_{{ $pq['id'] }}">Recargo</label>
                                                </div>
                                                <span class="badge badge-danger">VENC</span>
                                            @else
                                                <span class="text-muted">Activa</span>
                                                <span class="badge badge-secondary">OK</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-secondary small mb-0">
                                    No hay cuotas habilitadas para recargos en este plan.
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-3 d-flex justify-content-between">
                        <div>
                            <button wire:click="backToStep(2)" class="btn btn-sm btn-light">Volver</button>
                        </div>
                        <div>
                            <button wire:click="goToSummary" class="btn btn-sm btn-primary">Siguiente: Resumen</button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @section('stylesheet')
        @parent
            <style>
                /* Compacta las cards de cuotas */
                .compact-card {
                    font-size: 0.8rem;
                }

                .compact-card .card-body {
                    padding: 0.5rem !important;
                }

                .form-check-sm .form-check-input {
                    width: 0.9em;
                    height: 0.9em;
                }

                .form-check-sm .form-check-label {
                    font-size: 0.75rem;
                }
            </style>
        @endsection
    @endif



    {{-- STEP 4: resumen --}}
    @if ($step == 4)
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 font-weight-bold text-primary">Resumen de Inscripción</h6>
                    <small class="text-muted">
                        Revise los datos antes de confirmar.
                    </small>
                </div>
                <button wire:click="backToStep(3)" class="btn btn-sm btn-outline-secondary">Volver</button>
            </div>

            <div class="card-body">
                {{-- Datos del estudiante --}}
                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <div class="border rounded p-2 small">
                            <div class="text-muted">Estudiante</div>
                            <strong>{{ $selectedEstudiant->name }} {{ $selectedEstudiant->lastname }}</strong>
                            <div class="text-muted">CI: {{ $selectedEstudiant->ci_estudiant }} • ID:
                                {{ $selectedEstudiant->id }}</div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="border rounded p-2 small">
                            <div class="text-muted">Plan Seleccionado</div>
                            <strong>{{ $selectedPlanName }}</strong>
                            <div class="text-muted">Total de cuotas en el plan: {{ count($planQuotas) }}</div>
                        </div>
                    </div>
                </div>

                {{-- Resumen de cuotas CON recargo --}}
                @if (!empty($selectedQuotas))
                    <h6 class="font-weight-bold small text-primary mb-2">Cuotas con recargo por morosidad</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-sm table-hover align-middle">
                            <thead class="thead-light">
                                <tr class="small text-muted">
                                    <th>Cuota</th>
                                    <th class="text-center">Meses de mora</th>
                                    <th class="text-right">Monto original [USD]</th>
                                    <th class="text-right text-danger">Recargo [USD]</th>
                                    <th class="text-right font-weight-bold">Total [USD]</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($selectedQuotas as $sq)
                                    <tr>
                                        <td class="small">{{ $sq['name'] }}</td>
                                        <td class="text-center small">{{ $sq['meses'] }}</td>
                                        <td class="text-right small">{{ number_format($sq['monto'], 2) }}</td>
                                        <td class="text-right small text-danger">
                                            {{ number_format($sq['recargo'], 2) }}</td>
                                        <td class="text-right small font-weight-bold">
                                            {{ number_format($sq['monto'] + $sq['recargo'], 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-warning small mb-4">
                        <i class="fas fa-info-circle mr-1"></i>
                        No se seleccionaron cuotas con recargo. Se procederá solo con la inscripción administrativa.
                    </div>
                @endif

                {{-- Listado COMPLETO de todas las cuotas del plan --}}
                <h6 class="font-weight-bold small text-muted mb-2">Todas las cuotas del plan</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="thead-light">
                            <tr class="small text-muted">
                                <th>Nombre</th>
                                <th class="text-center">Vencimiento</th>
                                <th class="text-right">Monto [USD]</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Recargo aplicado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($planQuotas as $pq)
                                <tr class="small">
                                    <td>{{ $pq['name'] }}</td>
                                    <td class="text-center">
                                        {{ $pq['date_expiration'] ? \Carbon\Carbon::parse($pq['date_expiration'])->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="text-right">{{ number_format($pq['monto'], 2) }}</td>
                                    <td class="text-center">
                                        @if ($pq['expired'])
                                            <span class="badge badge-danger badge-sm">Vencida</span>
                                        @else
                                            <span class="badge badge-success badge-sm">Activa</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if (isset($selectedQuotas[$pq['id']]))
                                            <span class="badge badge-warning badge-sm">Sí</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted small">No hay cuotas definidas en
                                        este plan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Totales (solo si hay recargos) --}}
                @if (!empty($selectedQuotas))
                    <div class="d-flex justify-content-end mt-3">
                        <div class="border rounded p-2 bg-light small">
                            <div>Cuotas con recargo: <strong>{{ count($selectedQuotas) }}</strong></div>
                            <div>Monto original:
                                <strong>{{ number_format(collect($selectedQuotas)->sum('monto'), 2) }}</strong>
                            </div>
                            <div class="text-danger">Total recargos:
                                <strong>{{ number_format(collect($selectedQuotas)->sum('recargo'), 2) }}</strong>
                            </div>
                            <div class="border-top mt-1 pt-1 font-weight-bold">
                                Total a pagar:
                                {{ number_format(collect($selectedQuotas)->sum(fn($q) => $q['monto'] + $q['recargo']), 2) }}
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Botones de acción --}}
                <div class="mt-4 d-flex justify-content-end">
                    <button wire:click="backToStep(3)" class="btn btn-sm btn-light mr-2">Volver</button>
                    <button wire:click="askSave" class="btn btn-sm btn-success">
                        <i class="fas fa-check-circle mr-1"></i> Confirmar y Guardar
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
