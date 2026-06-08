@php
    $class_N = '';
    $class_estudiant = '';
    $class_ci = '';
    $class_planpago = 'text-nowrap';
    $class_deuda = 'text-nowrap';
    $class_grado = 'text-nowrap';
    $class_fecha = 'text-nowrap';
    $class_action = '';
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_estudiant }}">Nombres</th>
            <th class="{{ $class_estudiant }}">Apellidos</th>
            <th class="{{ $class_ci }}">Cédula</th>
            <th class="{{ $class_ci }}">Género</th>
            <th class="{{ $class_ci }}">Edad</th>
            @admon
                <th class="{{ $class_ci }}">ID</th>
                <th class="{{ $class_planpago }}">Plan de Pago</th>
            @endadmon
            <th class="{{ $class_grado }}">Grado/Sección</th>
            <th class="{{ $class_action }}">GSEmail</th>
            @admin<th class="{{ $class_action }}">N. Usuario</th>@endadmin
            @admin<th class="{{ $class_action }}">Creado</th>@endadmin
            <th class="{{ $class_action }}">Prosecución</th>
            <th class="{{ $class_action }}">F.Prosec.</th>

            @admon
                <th class="{{ $class_planpago }}">CI Rep.</th>
                <th class="{{ $class_planpago }}">Representante</th>
                <th class="{{ $class_planpago }}">Telefono</th>
                <th class="{{ $class_planpago }}">Email Rep.</th>
            @endadmon

            <th class="{{ $class_action }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @foreach ($estudiants as $estudiant)
            @php
                $ammount_expire_bill = 0;
                $status_active = $estudiant->status_active == 'true' ? true : false;
                $pestudio = $estudiant->pestudio;
                $representant = $estudiant->representant;
            @endphp
            <tr data-estudiant_id="{{ $estudiant->id ?? '' }}" data-id="{{ $estudiant->id ?? '' }}"
                class="{{ $status_active ? '' : 'table->danger' }}">

                <td class="{{ $class_N }}">
                    {{ $loop->iteration }}
                </td>

                <td class="{{ $class_estudiant ?? '' }}">
                    {{ $estudiant->name }}
                </td>

                <td class="{{ $class_estudiant ?? '' }}">
                    {{ $estudiant->lastname }}
                </td>

                <td class="{{ $class_ci ?? '' }}">
                    <a class="btn-link"
                        href="{{ route('administracion.estudiants.index', ['search' => $estudiant->ci_estudiant]) }}">
                        <span class="font-weight-bold text-{{ $ammount_expire_bill > 0 ? 'danger' : 'dark' }}">
                            {{ $estudiant->ci_estudiant }}
                        </span>
                    </a>
                </td>

                <td class="{{ $class_ci ?? '' }}" title="{{ $estudiant->gender ?? 'Sin género registrado' }}">
                    {{ $estudiant->gender ?? '-SGR-' }}
                </td>

                <td class="{{ $class_ci ?? '' }}" title="{{ $estudiant->gender ?? 'Sin género registrado' }}">
                    {{ $estudiant->age ?? 'N/A' }} AÑOS
                </td>

                @admon
                    <td class="{{ $class_ci ?? '' }}" title="{{ $estudiant->gender ?? 'Sin género registrado' }}">
                        {{ $estudiant->id ?? '-SID-' }}
                    </td>

                    <td class="{{ $class_planpago ?? '' }}">
                        @if (empty($estudiant->administrativa->planpago_id))
                            <span class="badge badge-secondary mt-1" title="SIN PLAN DE PAGO ASIGNADO">.NINGUNO.</span>
                        @else
                            {!! $estudiant->administrativa->planpago->badge ?? '' !!}
                        @endif
                    </td>
                @endadmon

                <td id="td-users-is_active-{{ $estudiant->id }}" class="{{ $class_grado ?? '' }}">
                    @if ($estudiant->getInscripcion())
                        <span
                            class="{{ $estudiant->getInscripcion()->seccion->grado->class_text_color ?? 'default' }}">
                            {{ $estudiant->getInscripcion()->seccion->grado->name ?? '' }}
                            {{ $estudiant->getInscripcion()->seccion->name ?? '' }}
                        </span>
                    @else
                        -SIN SECCION-
                    @endif
                </td>

                <td style="white-space: wrap !important">
                    {{ $estudiant->gsemail ?? '' }}</span>
                </td>
                @admin
                    <td style="white-space: wrap !important">
                        {{ $estudiant->user ? $estudiant->user->username : null }}</td>
                @endadmin

                @admin
                    <td style="white-space: wrap !important"> {{ $estudiant->created_at->format('Y-m-d') ?? null }}
                    </td>
                @endadmin

                <td>
                    {{ $estudiant->status_prosecution ? '[SI]' : '[NO]' }}
                </td>

                <td>
                    <div style="white-space: wrap !important">{{ $estudiant->date_prosecution }}</div>
                </td>

                @admon
                    <td class="{{ $class_estudiant ?? '' }}">
                        {{ $representant->ci_representant ?? null }}
                    </td>

                    <td class="{{ $class_estudiant ?? '' }}">
                        {{ $representant->name ?? null }}
                    </td>

                    <td class="{{ $class_estudiant ?? '' }}">
                        {{ $representant->phone ?? null }}
                    </td>
                    <td class="{{ $class_estudiant ?? '' }}">
                        {{ $representant->email ?? null }}
                    </td>
                @endadmon

                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">

                    <div class="btn-group btn-group-sm">

                        <a title="Resumen" class="btn-card btn btn-info bnt-sm" href="#">
                            <i class="{{ $icon_menus['profile'] }} fa-1x"></i>
                        </a>
                        <a title="Editar datos del estudiante" class="btn btn-warning bnt-sm"
                            href="{{ route('administracion.estudiants.edit', ['id' => $estudiant->id]) }}"
                            role="button">
                            <i class="{{ $icon_menus['editar'] ?? '' }} fa-1x"></i>
                        </a>

                        {{-- @admin --}}
                        @php $status_delete = $estudiant->status_delete; @endphp
                        <fieldset {{ $status_delete ? null : 'disabled=disabled' }}>
                            <a title="Eliminar"
                                class="btn-destroy btn btn-danger btn-sm {{ $status_delete ? null : 'disabled' }}"
                                href="#" id="btn-destroy_id_{{ $estudiant->id }}">
                                <i class="fas fa-trash"></i>
                            </a>
                        </fieldset>
                        {{-- @endadmin --}}

                        @php $inscripcion = $estudiant->inscripcion; @endphp
                        @if ($inscripcion)
                            <a title="Editar Inscripción"
                                class="btn btn-info btn-sm {{ !$inscripcion->id ? ' disabled ' : null }}"
                                target="_BLANK"
                                href="{{ route('administracion.inscripciones.edit', ['id' => $inscripcion->id]) }}"
                                role="button">
                                <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                            </a>
                        @else
                            <a title="Editar Inscripción" class="btn btn-info btn-sm disabled" href="#"
                                role="button">
                                <i class="{{ $icon_menus['editar'] }} fa-1x"></i>
                            </a>
                        @endif


                    </div>

                </td>
            </tr>
        @endforeach

    </tbody>
</table>

<div id="container_modal"></div>

{!! Form::open([
    'route' => ['administracion.estudiants.destroy', ':ESTUDIANT_ID'],
    'method' => 'DELETE',
    'id' => 'form-destroy',
    'role' => 'form',
]) !!}
{!! Form::close() !!}
@section('scripts')
    @parent
    <script src="{{ asset('js/models/estudiants/destroy.js') }}"></script>
@endsection

{{-- @include('administracion.datatables.default') --}}
@include('administracion.datatables.exportBootstrap')

@section('scripts')
    @parent
    <script>
        $('.btn-card').click(function(e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('id'); //console.log(id);
            var modal = '#modal_card'; //console.log(modal);
            var container = '#container_modal'; //console.log(container);
            var ajaxurl = '{{ route('administracion.ajax.fill.modal.estudiant_card', '_id_') }}';
            ajaxurl = ajaxurl.replace('_id_', id);
            $.ajax({
                url: ajaxurl,
                type: "GET",
                success: function(data) {
                    $(container).html(data);
                    $(modal).modal('toggle');
                }
            });
        });
    </script>
@endsection
