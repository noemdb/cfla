<table width="100%" class="table table-striped table-hover table-sm small p-1">
    <thead>
        <tr>
            <th>N</th>
            <th>
                <button class="btn btn-link p-0 text-dark font-weight-bold" wire:click="sortBy('firstname')">
                    Estudiante
                    @if ($sortField === 'firstname')
                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                    @else
                        <i class="fas fa-sort text-muted"></i>
                    @endif
                </button>
            </th>
            <th>
                <button class="btn btn-link p-0 text-dark font-weight-bold" wire:click="sortBy('grade')">
                    Grado/Año
                    @if ($sortField === 'grade')
                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                    @else
                        <i class="fas fa-sort text-muted"></i>
                    @endif
                </button>
            </th>
            <th>F.Cita</th>
            <th>
                <button class="btn btn-link p-0 text-dark font-weight-bold" wire:click="sortBy('representant_name')">
                    Representante
                    @if ($sortField === 'representant_name')
                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                    @else
                        <i class="fas fa-sort text-muted"></i>
                    @endif
                </button>
            </th>
            <th>Datos</th>
            <th>Procedencia</th>
            <th>Estado</th>
            <th>Entrevista</th>
            <th>Aceptado</th>
            <th>
                <button class="btn btn-link p-0 text-dark font-weight-bold" wire:click="sortBy('created_at')">
                    F.Registro
                    @if ($sortField === 'created_at')
                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                    @else
                        <i class="fas fa-sort text-muted"></i>
                    @endif
                </button>
            </th>
            <th>Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">
        @forelse($catchments as $index => $catchment)
            @php
                $group = $catchment->catchment_group;
                $status_accepted = $catchment->status_accepted;
                $activities = $catchment->activities;
                $grado = $catchment->grado;
                $pestudio = $grado ? $grado->pestudio : null;
                $catchment_interview = $catchment->catchmentInterviews->sortByDesc('created_at')->first();
                $interview_id = $catchment_interview ? $catchment_interview->id : null;
            @endphp

            <tr id="catchment-{{ $catchment->id }}" class="{{ $status_accepted ? 'table-success' : null }}">
                <td>
                    {{ ($catchments->currentPage() - 1) * $catchments->perPage() + $index + 1 }}
                </td>
                <td>
                    {{ $catchment->fullname ?? '' }}
                </td>
                <td>
                    {{ $grado ? $grado->name : null }}
                    <div class="text-muted small">{{ $pestudio ? $pestudio->name : null }}</div>
                </td>
                <td>
                    {{ f_date($catchment->day_appointment) ?? '' }}
                </td>

                <td>
                    {{ $catchment->fullname_representant ?? '' }}
                    <div class="text-muted">
                        <div>{{ $catchment->token ?? '' }}</div>
                        <div>CI: {{ $catchment->representant_ci ?? null }}</div>
                        <div>Email: {{ $catchment->email ?? null }}</div>
                    </div>
                </td>

                <td>
                    Telf.:{{ $catchment->representant_phone ?? null }}
                    @if ($catchment_interview)
                        <div>Datos de la Entrevista [Telef.]</div>
                        <ul class="ml-2 pl-2">
                            <li>{{ $catchment_interview->phone_numbers }}</li>
                            <li>Tutor/Docente: {{ $catchment_interview->tutor_teacher_name }}
                                {{ $catchment_interview->tutor_teacher_phone }}</li>
                            <li>Guarantor: {{ $catchment_interview->person_guarantor_name_phone }}</li>
                            <li>Recommender: {{ $catchment_interview->recommender_name }}
                                {{ $catchment_interview->recommender_affinity }}
                                {{ $catchment_interview->recommender_phone }}</li>
                        </ul>
                    @endif
                </td>

                <td>
                    {{ $catchment->origin ?? null }}
                </td>
                <td>
                    {{ $catchment->status_active ? 'Activo' : 'Desactivo' }}
                </td>

                <td>
                    {{ $catchment->catchmentInterviews->count() ? '[SI]' : '[NO]' }}
                </td>

                <td>
                    {{ $status_accepted ? '[-Aceptado-]' : '-En Espera-' }}
                </td>
                <td>
                    {{ $catchment->created_at ?? null }}
                </td>

                <td>
                    @control
                    <div class="btn-group btn-group-sm">
                        @php
                            $disabled = !$catchment_interview ? 'disabled' : null;
                            $url = env('APP_URL') . '/general/catchments/paper/id/' . $interview_id;
                        @endphp

                        <a class="btn btn-dark btn-sm {{ $disabled }}" {{ $disabled }}
                            href="{{ $url ?? null }}" role="button" target="_BLANK"
                            title="Ver planilla de la entrevista">
                            <i class="{{ $icon_menus['pdf'] ?? 'fas fa-file-pdf' }}"></i>
                        </a>

                        @php $status_delete = (! $catchment_interview) ? true : false; @endphp

                        @if (!$catchment->status_active)
                            <button type="button" class="btn btn-success btn-sm"
                                wire:click="prepareRestore({{ $catchment->id }})" title="Restaurar registro">
                                <i class="fas fa-undo"></i> Restaurar
                            </button>
                            <button type="button" class="btn btn-danger btn-sm"
                                wire:click="prepareForceDelete({{ $catchment->id }})" title="Borrar Definitivamente">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </button>
                        @else
                            @if ($status_delete)
                                <button type="button" class="btn btn-danger btn-sm"
                                    wire:click="prepareDelete({{ $catchment->id }})"
                                    title="Eliminar registro (borrado lógico)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @else
                                <button type="button" class="btn btn-danger btn-sm disabled"
                                    title="No se puede eliminar (tiene entrevistas)" disabled>
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        @endif

                        {{-- Botones de email si hay entrevista --}}
                        @if ($catchment_interview)
                            @if ($catchment_interview->accepted)
                                <button type="button" class="btn btn-success btn-sm"
                                    wire:click="sendAcceptanceEmail({{ $interview_id }})"
                                    title="Enviar correo de aceptación">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            @endif

                            @if ($catchment_interview->status_standby)
                                <button type="button" class="btn btn-warning btn-sm"
                                    wire:click="sendStandbyEmail({{ $interview_id }})"
                                    title="Enviar correo de lista de espera">
                                    <i class="fas fa-clock"></i>
                                </button>
                            @endif
                        @endif
                    </div>
                    @endcontrol
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="12" class="text-center py-4">
                    <div class="text-muted">
                        <i class="fas fa-search fa-2x mb-2"></i>
                        <p>No se encontraron registros</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
