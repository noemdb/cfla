@php
    $class_N = 'd-none d-sm-table-cell';
    $class_estudiant = '';
    $class_ci = 'd-none d-md-table-cell';
    $class_planpago = 'd-none d-lg-table-cell text-nowrap';
    $class_deuda = 'd-none d-lg-table-cell text-nowrap';
    $class_grado = 'd-none d-lg-table-cell';
    $class_fecha = 'text-nowrap';
    $class_action = '';
@endphp

<div class="px-2">

    {{-- <div class="input-group">
        {!! Form::text('search', $search, ['class' => 'form-control','wire:model.debounce.500ms'=>'search','placeholder'=>'Buscar Nombre o Cédula']); !!}
        <div class="input-group-append" style="z-index: 0;">
            {!! Form::button('Buscar',['wire:click'=>'render','class'=>'btn btn-info my-2 my-sm-0','id'=>'btnSearch']); !!}
        </div>
    </div> --}}

    <div class="form-row">

        <div class="col-6">
            {!! Form::label('fecha', 'Estudiante', ['class' => 'm-0 font-weight-bold text-muted']) !!}
            {!! Form::text('search', $search, [
                'class' => 'form-control',
                'wire:model.debounce.500ms' => 'search',
                'placeholder' => 'Buscar Nombre o Cédula',
            ]) !!}
        </div>
        <div class="col-4">
            {!! Form::label('grado_id', 'Grado', ['class' => 'm-0 font-weight-bold text-muted']) !!}
            {!! Form::select('grado_id', $list_grado, $grado_id, [
                'wire:model' => 'grado_id',
                'class' => 'form-control',
                'id' => 'grado_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-2">
            {!! Form::label('seccion_id', 'Sección', ['class' => 'm-0 font-weight-bold text-muted']) !!}
            {!! Form::select('seccion_id', $list_seccion, $seccion_id, [
                'wire:model' => 'seccion_id',
                'class' => 'form-control',
                'id' => 'seccion_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>

    </div>

    <hr>

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

            @foreach ($incidents as $incident)
                @php
                    $incident_agreements = $incident->incident_agreements;
                    $estudiant = $incident->estudiant;
                    $representant = $estudiant->representant;
                    $brothers = $estudiant->BrothersFormaly;
                @endphp

                <tr data-estudiant_id="{{ $incident->id ?? '' }}" data-id="{{ $incident->id ?? '' }}"
                    class="{{ $incident->id == $estudiant_id ? 'table-secondary' : null }}"> {{-- font-weight-bold --}}

                    <td id="td-count" class="{{ $class_N }}">
                        {{ $loop->iteration }}
                    </td>

                    <td id="td-count" class="{{ $class_estudiant ?? null }} {{ $displaModeIndex }}">
                        <div class=" border-bottom">{{ $incident->description }}</div>
                        <div class="text-muted ml-2">
                            <div>{{ $incident->observations }}</div>
                            <div>{{ $incident->taken_actions }}</div>
                        </div>
                    </td>

                    <td class="{{ $class_estudiant ?? '' }}">
                        <div></div>
                        <div>{{ $estudiant->fullname ?? null }}</div>
                        <div class=" text-sm text-muted">{{ $estudiant->ci_estudiant }} <span class="text-muted">[Edad:
                                {{ $estudiant->age ?? null }}]</span></div>
                        <div class="{{ $estudiant->getInscripcion()->seccion->grado->class_text_color ?? 'default' }}">
                            {{ $estudiant->grado->name ?? '' }} {{ $estudiant->seccion->name ?? '' }}
                        </div>
                        <hr class="my-0 py-0">
                        <div class="ml-2">
                            <div class="text-secondary">
                                <div class="">Representante: {{ $representant->name ?? null }} <span
                                        class="text-muted">{{ $representant->ci_representant ?? null }}</span></div>
                            </div>
                            @if ($brothers->count())
                                <div class=" text-secondary">Hermano(s):</div>
                                @foreach ($brothers as $brother)
                                    <div class="text-muted">-. {{ $brother->fullname ?? null }} <span
                                            class="text-muted">{{ $brother->ci_estudiant ?? null }}</span> <span
                                            class="text-muted">[Edad: {{ $brother->age ?? null }}]</span></div>
                                @endforeach
                            @endif
                        </div>
                    </td>

                    <td id="td-count" class="{{ $class_estudiant }} {{ $displaModeIndex }}">
                        @forelse ($incident_agreements as $incident_agreement)
                            <div title="{{ $incident_agreement->description ?? null }}">
                                <span class="text-dark"> {{ $loop->iteration }}.
                                    {{ Str::limit($incident_agreement->description, 32, '...') }}</span>
                            </div>
                        @empty
                            <span class="text-muted">Sin acuerdos registrados</span>
                        @endforelse
                        {{-- {{$incident_agreements->count()}} --}}
                    </td>

                    <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $incident->id ?? null }}">

                        <div class="d-flex justify-content-start">

                            <div class="btn-group btn-group-sm">

                                @php $disabled = ($incident->status_notify_agreement || $incident->status_close) ? true : false; @endphp
                                <a title="Registrar acuerdo de la incidencia"
                                    class="btn btn-primary btn-sm {{ $disabled ? 'disabled' : null }}" href="#"
                                    wire:click="create({{ $incident->id ?? null }})" role="button">
                                    <i class="{{ $icon_menus['nuevo'] ?? '' }} fa-1x"></i>
                                </a>

                                {{-- @php $disabled = ($incident->status_notify_agreement || $incident->status_close) ? true : false; @endphp                                
                                @php $icon = ($incident->status_close) ? 'lock' : 'unlock'; @endphp
                                <a title="Cerrar incidente" class="btn btn-warning btn-sm {{ ($disabled) ? 'disabled':null }}" href="#" wire:click="closeIncident({{$incident->id ?? null}})" role="button">
                                    <i class="{{ $icon_menus[$icon] ?? '' }} fa-1x"></i>
                                </a> --}}

                                @php $disabled = ($incident->status_notify_agreement && $incident_agreements->isNotEmpty() && !$incident->status_close) ? false : true; @endphp
                                @php $icon = ($incident->status_close) ? 'lock' : 'unlock'; @endphp
                                <a title="Cerrar incidente"
                                    class="btn btn-warning btn-sm {{ $disabled ? 'disabled' : null }}" href="#"
                                    wire:click="closeIncident({{ $incident->id ?? null }})" role="button">
                                    <i class="{{ $icon_menus[$icon] ?? '' }} fa-1x"></i>
                                </a>

                            </div>

                            @if (!$incident_agreements->isEmpty())
                                <div class="btn-group btn-group-sm ml-1">

                                    <a title="Ver vista previa de la notificación" class="btn btn-info btn-sm"
                                        href="#" wire:click="viewMail({{ $incident->id }})" role="button">
                                        <i class="{{ $icon_menus['eye'] ?? '' }} fa-1x"></i>
                                    </a>
                                    @if ($incident->status_notify_agreement)
                                        <a title="Notificación enviada" class="btn btn-info btn-sm disabled"
                                            href="#" role="button">
                                            <i class="{{ $icon_menus['check'] ?? '' }} fa-1x"></i>
                                        </a>
                                    @else
                                        <a title="Enviar por correo una notificación del registro del acuerdo de la incidencia"
                                            class="btn btn-success btn-sm" href="#"
                                            wire:click="sendMail({{ $incident->id }})" role="button">
                                            <i class="{{ $icon_menus['mail'] ?? '' }} fa-1x"></i>
                                        </a>
                                    @endif

                                </div>

                                <div class="dropdown dropleft">
                                    <button class="btn btn-secondary dropdown-toggle btn-sm ml-1" type="button"
                                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <i class="{{ $icon_menus['incident_setting'] ?? '' }} fa-1x"></i>
                                    </button>
                                    <div class="dropdown-menu px-1 mx-1" aria-labelledby="dropdownMenuButton"
                                        wire:key="incident_btn_{{ $incident->id }}">
                                        @foreach ($incident_agreements as $incident_agreement)
                                            <div class="dropdown-item"
                                                title="{{ $incident_agreement->description ?? null }}"
                                                wire:key="incident_agreement_btn_{{ $incident_agreement->id }}">
                                                <div class="d-flex justify-content-between">
                                                    <div title="{{ $incident_agreement->description ?? null }}">
                                                        <span class="small text-dark"> {{ $loop->iteration }}.
                                                            {{ Str::limit($incident_agreement->description, 32, '...') }}</span>
                                                    </div>
                                                    <div class="btn-group btn-group-sm  px-1 mx-1">

                                                        <a title="Editar datos del acuerdo de la incidencia"
                                                            class="btn btn-warning btn-sm {{ $incident->status_notify_agreement ? 'disabled' : null }}"
                                                            href="#"
                                                            wire:click="edit({{ $incident_agreement->id }})"
                                                            role="button">
                                                            <i class="{{ $icon_menus['editar'] ?? '' }} fa-1x"></i>
                                                        </a>

                                                        <a title="Eliminar datos de la incidencia"
                                                            class="btn btn-danger btn-sm {{ $incident->status_notify_agreement ? 'disabled' : null }}"
                                                            href="#"
                                                            wire:click="destroy({{ $incident_agreement->id }})"
                                                            role="button">
                                                            <i class="{{ $icon_menus['eliminar'] ?? '' }} fa-1x"></i>
                                                        </a>

                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- </div> --}}
                        </div>

                    </td>

                </tr>
            @endforeach

        </tbody>
    </table>

    {{ $incidents->links() }}

</div>
