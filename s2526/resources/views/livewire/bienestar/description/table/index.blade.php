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

    @php $displaModeIndex = (!$modeIndex) ? 'd-none' : null ; @endphp

    <div class="form-row">
        <div class="col-10">
            {!! Form::label('search', 'Descripción', ['class' => 'm-0 font-weight-bold text-muted']) !!}
            {!! Form::text('search', $search, [
                'class' => 'form-control',
                'wire:model.debounce.500ms' => 'search',
                'placeholder' => 'Buscar Descripción',
            ]) !!}
        </div>

        <div class="col-2">
            {!! Form::label('create', '.', ['class' => 'm-0 font-weight-bold text-muted text-right']) !!}
            {!! Form::button('Nueva', ['class' => 'form-control btn btn-primary', 'wire:click' => 'create()']) !!}
        </div>

    </div>

    <hr>

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default"
        wire:key="table-data-estudiants-{{ Str::random() }}">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Descripción</th>
                <th class="{{ $class_estudiant }}">Tipo/Ámbito</th>
                <th class="{{ $class_estudiant }}">Motivo</th>
                <th class="{{ $class_estudiant }}">Incidencias</th>
                <th class="{{ $class_action }} text-left">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach ($incident_descriptions as $incident_description)
                @php
                    $status_active = $incident_description->status_active;
                @endphp
                <tr data-id="{{ $incident_description->id ?? '' }}"
                    class="{{ $incident_description->id == $incident_description_id ? 'table-secondary' : null }} {{ $status_active ? 'text-success font-weight-bold' : null }}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{ $loop->iteration }}
                    </td>

                    <td class="{{ $class_estudiant ?? '' }}">
                        <div>{{ $incident_description->name ?? null }}</div>
                    </td>

                    <td class="{{ $class_estudiant ?? '' }}">
                        <div>{{ $incident_description->ambit ?? null }}</div>
                    </td>

                    <td class="{{ $class_estudiant ?? '' }}">
                        <div>
                            {{ $incident_description->incident_reason ? $incident_description->incident_reason->name : null }}
                        </div>
                    </td>
                    <td class="{{ $class_estudiant ?? '' }}">
                        {{ $incident_description->incidents->count() ?? null }}
                    </td>

                    <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $incident_description->id }}">

                        <div class="d-flex justify-content-start"
                            wire:key="bienestar-action-flex-btn-{{ $incident_description->id }}-{{ Str::random() }}">

                            @php $disabled = ($incident_description->status_active) ? "disabled":null ; @endphp
                            <a title="Editar nueva descripción" class="btn btn-warning btn-sm mr-1 {{ $disabled }}"
                                href="#" wire:click="edit({{ $incident_description->id }})" role="button">
                                <i class="{{ $icon_menus['edit'] ?? '' }} fa-1x"></i>
                            </a>

                            <a title="Eliminar datos de la descripción"
                                class="btn btn-danger btn-sm {{ $disabled ? 'disabled' : null }}" href="#"
                                wire:click="destroy({{ $incident_description->id }})" role="button">
                                <i class="{{ $icon_menus['eliminar'] ?? '' }} fa-1x"></i>
                            </a>

                        </div>

                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

    {{ $incident_descriptions->links() }}

</div>
