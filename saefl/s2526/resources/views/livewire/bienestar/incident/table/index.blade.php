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
        <div class="col-4">
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
        <div class="col-2">
            {!! Form::label('btn_toprint', 'Filtros', ['class' => 'm-0 font-weight-bold text-muted']) !!}
            <div class="btn-group btn-block" role="group" aria-label="Basic example">
                {{-- <button class="btn btn-dark disabled w-75" type="button" id="btn_toprint">
                    <i class="fa fa-print" aria-hidden="true"></i>
                </button> --}}

                <button class="btn btn-{{ $loadFilter ? 'warning' : 'info' }} w-25" type="button" id="btn_filter"
                    wire:click="showFilter()" wire:key="{{ Str::random() }}">
                    <i class="fa fa-filter" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>

    <hr>

    @php $displaModeIndex = (!$modeIndex) ? 'd-none' : null ; @endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default"
        wire:key="table-data-estudiants-{{ Str::random() }}">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>
                <th class="{{ $class_estudiant }} {{ $displaModeIndex }}">N. incidencias registradas</th>
                <th class="{{ $class_action }} text-left">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach ($estudiants as $estudiant)
                @php
                    $status_active = $estudiant->status_active == 'true' ? true : false;
                    $incidents = $estudiant->incidents;
                    $representant = $estudiant->representant;
                    $brothers = $estudiant->BrothersFormaly;
                @endphp
                <tr data-estudiant_id="{{ $estudiant->id ?? '' }}" data-id="{{ $estudiant->id ?? '' }}"
                    class="{{ $estudiant->id == $estudiant_id ? 'table-secondary' : null }}"> {{-- font-weight-bold --}}

                    <td id="td-count" class="{{ $class_N }}">
                        {{ $loop->iteration }}
                    </td>

                    <td class="{{ $class_estudiant ?? '' }}">
                        <div>{{ $estudiant->fullname ?? null }}</div>
                        <div class=" text-sm text-muted">{{ $estudiant->ci_estudiant }} <span class="text-muted">[Edad:
                                {{ $estudiant->age ?? null }}]</span></div>
                        <div class="{{ $estudiant->getInscripcion()->seccion->grado->class_text_color ?? 'default' }}">
                            {{ $estudiant->getInscripcion()->seccion->grado->name ?? '' }}
                            {{ $estudiant->getInscripcion()->seccion->name ?? '' }}
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

                    <td class="{{ $class_estudiant ?? '' }} {{ $displaModeIndex }}">
                        {{ $incidents->count() ?? null }}
                    </td>

                    <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">

                        <div class="d-flex justify-content-start"
                            wire:key="bienestar-action-flex-btn-{{ $estudiant->id }}-{{ Str::random() }}">

                            <a title="Registrar incidente" class="btn btn-primary btn-sm mr-1" href="#"
                                wire:click="create({{ $estudiant->id }})" role="button">
                                <i class="{{ $icon_menus['nuevo'] ?? '' }} fa-1x"></i>
                            </a>

                            @if ($incidents->isNotEmpty())
                                <a title="Línea de tiempo" class="btn btn-info btn-sm mr-1" href="#"
                                    wire:click="tline({{ $estudiant->id }})" role="button">
                                    <i class="{{ $icon_menus['tline'] ?? '' }} fa-1x"></i>
                                </a>

                                <div class="dropdown dropleft">
                                    <button class="btn btn-secondary dropdown-toggle btn-sm" type="button"
                                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <i class="{{ $icon_menus['incident_setting'] ?? '' }} fa-1x"></i>
                                    </button>

                                    <div class="dropdown-menu px-1 mx-1" aria-labelledby="dropdownMenuButton">
                                        @foreach ($incidents as $incident)
                                            <div class="dropdown-item px-1 mx-1" href="#">
                                                <div class="d-flex justify-content-between">
                                                    <div class="small text-dark font-weight-bold">
                                                        <span
                                                            title="{{ $incident->description ?? null }}">{{ $loop->iteration }}.
                                                            {{ Str::limit($incident->description, 15, '...') }}</span>
                                                    </div>
                                                    <div class="btn-group btn-group-sm">
                                                        <a title="Ver vista previa de la notificación"
                                                            class="btn btn-info btn-sm" href="#"
                                                            wire:click="viewMail({{ $incident->id }})" role="button">
                                                            <i class="{{ $icon_menus['eye'] ?? '' }} fa-1x"></i>
                                                        </a>
                                                        @if ($incident->status_notify)
                                                            <a title="Notificación enviada"
                                                                class="btn btn-success btn-sm disabled" href="#"
                                                                role="button">
                                                                <i class="{{ $icon_menus['check'] ?? '' }} fa-1x"></i>
                                                            </a>
                                                        @else
                                                            <a title="Enviar por correo una notificación de la incidencia"
                                                                class="btn btn-success btn-sm {{ $incident->status_notify ? 'disabled' : null }}"
                                                                href="#"
                                                                wire:click="sendMail({{ $incident->id }})"
                                                                role="button">
                                                                <i class="{{ $icon_menus['mail'] ?? '' }} fa-1x"></i>
                                                            </a>
                                                        @endif

                                                        @php $disabled = ($incident->status_notify || $incident->status_close == "true") ? true:false @endphp
                                                        <a title="Editar datos de la incidencia"
                                                            class="btn btn-warning btn-sm {{ $disabled ? 'disabled' : null }} "
                                                            href="#" wire:click="edit({{ $incident->id }})"
                                                            role="button">
                                                            <i class="{{ $icon_menus['editar'] ?? '' }} fa-1x"></i>
                                                        </a>
                                                        <a title="Mostrar datos de la incidencia"
                                                            class="btn btn-info btn-sm " href="#"
                                                            wire:click="show({{ $incident->id }})" role="button">
                                                            <i class="{{ $icon_menus['show'] ?? '' }} fa-1x"></i>
                                                        </a>

                                                        <a title="Generar ficha" target="_BLANK"
                                                            class="btn btn-dark btn-sm"
                                                            href="{{ route('bienestars.incidents.pdf.ficha', $incident->id) }}"
                                                            role="button">
                                                            <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                                                        </a>

                                                        @php $disabled = ($incident->status_close) ? true : false; @endphp
                                                        @php $icon = ($incident->status_close) ? 'lock' : 'unlock'; @endphp
                                                        <a title="Cerrar incidente"
                                                            class="btn btn-warning btn-sm {{ $disabled ? 'disabled' : null }}"
                                                            href="#"
                                                            wire:click="closeIncident({{ $incident->id ?? null }})"
                                                            role="button">
                                                            <i class="{{ $icon_menus[$icon] ?? '' }} fa-1x"></i>
                                                        </a>

                                                        <a title="Eliminar datos de la incidencia"
                                                            class="btn btn-danger btn-sm {{ $incident->status_notify ? 'disabled' : null }}"
                                                            href="#" wire:click="destroy({{ $incident->id }})"
                                                            role="button">
                                                            <i class="{{ $icon_menus['eliminar'] ?? '' }} fa-1x"></i>
                                                        </a>

                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <a title="Generar ficha Estuidiante" target="_BLANK"
                                    class="btn btn-danger btn-sm ml-1"
                                    href="{{ route('bienestars.incidents.pdf.ficha.estudiant', $estudiant->id) }}"
                                    role="button">
                                    <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                                </a>
                            @endif



                        </div>

                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

    {{ $estudiants->links() }}

</div>


@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('#btn_toprint').click(function(e) {
                e.preventDefault();
                var grado_id = $('#grado_id').val();
                var seccion_id = $('#seccion_id').val();
                var dataString = '?grado_id=' + grado_id + '&seccion_id=' + seccion_id;
                var url = "{{ route('bienestars.incidents.pdf.batch') }}" + dataString;
                // window.open(url,'_blank');
            });
        });
    </script>
@endsection
