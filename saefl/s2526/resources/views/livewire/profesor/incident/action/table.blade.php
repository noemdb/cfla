@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="d-none d-md-table-cell";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_deuda="d-none d-lg-table-cell text-nowrap";
    $class_grado="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp

<div class="px-2">

    @php $displaModeIndex = (!$modeIndex) ? 'd-none' : null ; @endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }} {{ $displaModeIndex }}">Incidencia</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>
                <th class="{{ $class_estudiant }}">Acuerdos registrados</th>
                <th class="{{ $class_action }} text-center">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach($incidents as $incident)

                @php
                    $incident_actions = $incident->incident_actions;
                    $estudiant = $incident->estudiant;
                    $representant = $estudiant->representant;
                    $brothers = $estudiant->BrothersFormaly;
                    $status_profesor_guia = $estudiant->isProfesorGuia($profesor->id);
                @endphp

                <tr data-estudiant_id="{{$incident->id ?? ''}}" data-id="{{$incident->id ?? ''}}" class="{{($incident->id == $estudiant_id ) ? 'table-secondary':null}}"> {{-- font-weight-bold --}}

                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>

                    <td id="td-count" class="{{ $class_estudiant ?? null }} {{ $displaModeIndex }}">
                        <div class=" border-bottom">{{$incident->description}}</div>
                        <div class="text-muted ml-2">
                            <div>{{$incident->observations}}</div>
                            <div>{{$incident->taken_actions}}</div>
                            <div>{{ $incident->created_at->format('d-m-Y h:i')}}</div>
                        </div>
                    </td>

                    <td class="{{ $class_estudiant ?? '' }}">
                        <div></div>
                        <div>{{$estudiant->fullname ?? null}}</div>
                        <div class=" text-sm text-muted">{{$estudiant->ci_estudiant}} <span class="text-muted">[Edad: {{$estudiant->age ?? null}}]</span></div>
                        <div class="{{$estudiant->getInscripcion()->seccion->grado->class_text_color ?? 'default'}}">
                            {{$estudiant->grado->name ?? ''}} {{$estudiant->seccion->name ?? ''}}
                        </div>
                        <hr class="my-0 py-0">
                        <div class="ml-2">
                            <div class="text-secondary">
                                <div class="">Representante: {{$representant->name ?? null}} <span class="text-muted">{{$representant->ci_representant ?? null}}</span></div>
                            </div>
                            @if ($brothers->count())
                                <div class=" text-secondary">Hermano(s):</div>
                                @foreach($brothers as $brother)
                                    <div class="text-muted">-. {{$brother->fullname ?? null}} <span class="text-muted">{{$brother->ci_estudiant ?? null}}</span> <span class="text-muted">[Edad: {{$brother->age ?? null}}]</span></div>
                                @endforeach
                            @endif
                        </div>
                    </td>

                    <td id="td-count" class="{{ $class_estudiant }} {{ $displaModeIndex }}">
                        @forelse ($incident_actions as $incident_action)
                            @php $description = ($incident_action) ? $incident_action->incident_corrective->description : null @endphp
                            <div title="{{$description ?? null}}">
                                <span class="text-dark"> {{ Str::limit($description,32,'...')}}</span>
                            </div>
                        @empty
                            <span class="text-muted">Sin Correctivos Pedagógicos registrados</span>
                        @endforelse
                    </td>

                    <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $incident->id ?? null }}">

                        @if ($status_profesor_guia)

                            <div class="d-flex justify-content-start">

                                <div class="btn-group btn-group-sm px-1" >
                                    @php
                                        $status_notify = ( $incident->status_notify && isset($incident->date_notify_email)) ? true : false;
                                        $disabled = ( $incident->status_notify_agreement ) ? true : false;
                                    @endphp
                                    <a title="Registrar Correctivo Pedagógico" class="btn btn-primary btn-sm {{ ($disabled) ? 'disabled':null }}" href="#" wire:click="create({{$incident->id ?? null}})" role="button">
                                        <i class="{{ $icon_menus['nuevo'] ?? '' }} fa-1x"></i>
                                    </a>
                                </div>

                            </div>
                        @endif

                    </td>

                </tr>

            @endforeach

        </tbody>
    </table>

    {{ $incidents->links() }}

</div>
