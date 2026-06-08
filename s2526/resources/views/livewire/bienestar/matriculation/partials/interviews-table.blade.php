<table class="table table-striped table-hover table-sm small">
    <thead>
        <tr>
            <th>N</th>
            <th>
                <button class="btn btn-link p-0 text-dark" wire:click="sortBy('student_full_name')">
                    Estudiante
                    @if($sortField === 'student_full_name')
                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                    @endif
                </button>
            </th>
            <th>Grado/Año</th>
            <th>Actividades</th>
            <th>
                <button class="btn btn-link p-0 text-dark" wire:click="sortBy('full_name')">
                    Representante
                    @if($sortField === 'full_name')
                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                    @endif
                </button>
            </th>
            <th>Email</th>
            <th>CI</th>
            <th>Procedencia</th>
            <th>Aceptado</th>
            <th>En espera</th>
            <th>Notificado</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        @forelse($catchment_interviews as $interview)
            @php
                $catchment = $interview->catchment;
                $group = $catchment?->catchment_group;
                $activities = $group?->activities ?? collect();
                $grado = $interview->grado;
                $pestudio = $grado?->pestudio;
                $accepted = $interview->accepted;
                $status_notified = $interview->status_notified;
                $status_standby = $interview->status_standby;
            @endphp

            <tr class="{{ $accepted ? 'table-success font-weight-bold' : '' }}">
                <td>{{ $loop->iteration + ($catchment_interviews->currentPage() - 1) * $catchment_interviews->perPage() }}</td>
                <td>{{ $interview->student_full_name }}</td>
                <td>
                    {{ $grado?->name }}
                    <div class="text-muted small">{{ $pestudio?->name }}</div>
                </td>
                <td>
                    <div class="text-uppercase">{{ $group?->name }}</div>
                </td>
                <td>
                    {{ $interview->full_name }}
                    <div class="small text-muted">
                        {{ $interview->identification_number }}
                        @if($catchment)
                            <div>{{ $catchment->token }}</div>
                        @endif
                        <div>{{ $interview->email }}</div>
                        @if($interview->token)
                            <div>{{ $interview->token }}</div>
                        @endif

                        <div>Datos de la Entrevista [Telef.]</div>
                        <ul class="ml-2 pl-2">
                            <li>{{ $interview->phone_numbers }}</li>
                            <li>Tutor/Docente: {{ $interview->tutor_teacher_name }} {{ $interview->tutor_teacher_phone }}</li>
                            <li>Guarantor: {{ $interview->person_guarantor_name_phone }}</li>
                            <li>Recommender: {{ $interview->recommender_name }} {{ $interview->recommender_affinity }} {{ $interview->recommender_phone }}</li>
                        </ul>
                    </div>
                </td>
                <td>{{ $interview->email }}</td>
                <td>{{ $interview->identification_number }}</td>
                <td>{{ $catchment?->origin }}</td>
                <td>{{ $accepted ? '-SI-' : '-NO-' }}</td>
                <td>{{ $status_standby ? '{SI}' : '{NO}' }}</td>
                <td>{{ $status_notified ? '[SI]' : '[NO]' }}</td>
                <td>
                    <div class="btn-group btn-group-sm">


                        {{-- <button class="btn btn-warning"
                                wire:click="editInterview({{ $interview->id }})"
                                title="Editar">
                            <i class="fas fa-edit"></i>
                        </button> --}}


                        <a name="" id="edit" class="btn btn-warning" href="{{route('bienestars.matriculations.interviews.edit',$interview->id)}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? '' }} fa-1x"></i>
                        </a>

                        <a class="btn btn-info"
                            href="{{ route('catchments.paper', $interview->identification_number) }}"
                            target="_blank"
                            title="Ver PDF">
                            <i class="fas fa-file-pdf"></i>
                        </a>

                        @if($interview->token)
                            <a class="btn btn-dark"
                                href="{{ route('catchments.accepted', $interview->token) }}"
                                target="_blank"
                                title="Ver aceptación">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                        @endif

                        @if($status_standby)
                            <a class="btn btn-secondary"
                                href="{{ route('catchments.standby', $interview->id) }}"
                                target="_blank"
                                title="Ver lista de espera">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                        @endif

                        @if(!$accepted)
                            <button class="btn btn-danger"
                                    wire:click="confirmDelete({{ $interview->id }}, '{{ $interview->student_full_name }}')"
                                    title="Eliminar entrevista">
                                <i class="fas fa-trash"></i>
                            </button>
                        @else
                            <button class="btn btn-danger disabled"
                                    title="No se puede eliminar una entrevista aceptada"
                                    disabled>
                                <i class="fas fa-trash"></i>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="12" class="text-center">No se encontraron entrevistas</td>
            </tr>
        @endforelse
    </tbody>
</table>
