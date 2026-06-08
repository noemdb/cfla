@php
    $class_N = 'd-none d-sm-table-cell';
    $class_estudiant = '';
    $class_ci = 'd-none d-md-table-cell';
    $class_planpago = 'd-none d-lg-table-cell text-nowrap';
    $class_grado = 'd-none d-lg-table-cell';
    $class_fecha = 'text-nowrap';
    $class_destino = 'text-nowrap';
    $class_action = '';
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_estudiant }}">Estudiante</th>
            <th class="{{ $class_estudiant }}">Representante</th>
            <th class="{{ $class_planpago }}">Banco</th>
            <th class="{{ $class_planpago }}">Referencia</th>
            <th class="{{ $class_planpago }}">Monto Bs.</th>
            <th class="{{ $class_planpago }}" title="Monto Cambiario">M.Cambiario</th>
            <th class="{{ $class_grado }}">Estado</th>
            <th class="{{ $class_grado }}">A. Matrícula</th>
            <th class="{{ $class_destino }}">Destino</th>
            <th class="{{ $class_grado }}">Fecha</th>
            <th class="{{ $class_action }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @foreach ($abonos as $abono)
            @php
                $estudiant = $abono->estudiant;
                $representant = $abono->representant;
                $exchange_rate = $abono->exchange_rate;
            @endphp

            <tr data-id="{{ $abono->id ?? '' }}"
                class="table-{{ (!empty($abono->deleted_at) or empty($abono->ingreso)) ? 'danger' : '' }}">

                <td id="td-count" class="{{ $class_N }}">
                    {{ $loop->iteration }}
                    [{{ $abono->id ?? '' }}]
                </td>

                <td class="{{ $class_planpago ?? '' }}">
                    <a class="btn-link"
                        href="{{ route('administracion.estudiants.index', ['search' => $estudiant?->ci_estudiant]) }}">
                        <span class=" font-weight-bold">{{ $representant->name ?? '' }}</span>
                    </a>
                    <span class="text-muted text-uppercase">{{ $estudiant?->fullinscripcion ?? '' }}</span><br>
                    <span class="text-muted">[{{ $representant->name ?? '' }}
                        {{ $representant->ci_representant ?? '' }}]</span>

                    @admin
                        [{{ $representant->id ?? '' }}]
                    @endadmin
                </td>

                <td class="{{ $class_planpago ?? '' }} {{ $class_err ?? '' }}">
                    {{ $representant->ci_representant }}
                </td>

                <td class="{{ $class_planpago ?? '' }} {{ $class_err ?? '' }}">
                    {{ $abono->ingreso->banco->name ?? '' }}
                </td>
                <td class="{{ $class_planpago ?? '' }} {{ $class_err ?? '' }}">
                    {{ $abono->ingreso->number_i_pay ?? '' }}
                </td>

                <td class="{{ $class_planpago ?? '' }}">
                    @php $class_exchange_ammount = ($exchange_rate) ? 'font-weight-bold  text-primary':'text-dark'; @endphp
                    <span class="{{ $class_exchange_ammount ?? null }}">
                        {{ f_float($abono->ingreso_ammount) ?? '' }}
                    </span>
                </td>

                <td class="{{ $class_planpago ?? '' }}">
                    @php
                        $symbol = $exchange_rate ? $exchange_rate->currency->symbol : null;
                        $symbol_referential = $exchange_rate ? $exchange_rate->currency_referential->symbol : null;
                        $ammount = $exchange_rate ? $exchange_rate->ammount : null;
                        $date = $exchange_rate ? $exchange_rate->date->format('d-m-Y') : null;
                        $title = $exchange_rate
                            ? 'Tasa de Cambio: ' . $symbol . ' ' . f_float($ammount) . ' - [' . $date . ']'
                            : null;
                    @endphp

                    <span class="{{ $class_exchange_ammount ?? null }}"
                        title="{{ $title ?? 'No hay tasa de cambio' }}">
                        @php $symbol = ($exchange_rate) ? $exchange_rate->currency_referential->symbol : null @endphp
                        {{ $symbol }} {{ f_float($abono->exchange_ammount) }}
                    </span>

                </td>

                <td class="{{ $class_grado ?? '' }} text-uppercase">
                    {{ $abono->deleted_at ? 'APLICADO' : 'NO APLICADO' }}
                </td>

                <td class="{{ $class_grado ?? '' }} text-uppercase">
                    {{ $abono->status_matriculations ? 'SI' : 'NO' }}
                    @php
                        $estudiants = $representant->estudiants_formaly;
                    @endphp
                    <div>{{ $estudiants->isEmpty() ? '-NO-' : '-SI-' }}</div>
                </td>

                <td class="{{ $class_destino ?? '' }}">
                    {{ $ingreso->destino ?? '' }}
                </td>

                <td class="{{ $class_grado ?? '' }} text-uppercase">
                    {{ $abono->created_at ?? null }}
                </td>

                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant?->id }}">
                    <div class="btn-group btn-group-sm">
                        @php $id_modal = 'modal_abono_'.$abono->id; @endphp
                        <a title="Mostrar detalles del registro del abono" class="btn btn-info btn-xs" href="#"
                            data-toggle="modal" data-target="#{{ $id_modal ?? 'id_modal' }}">
                            <i class="fas fa-info"></i>
                        </a>
                        @component('elements.widgets.modal')
                            @slot('classH', 'secondary')
                            @slot('id', $id_modal)
                            @slot('title', 'Detalles del registro del abono')
                            @slot('close', true)
                            @slot('close', true)
                            @slot('body')
                                @include('administracion.abonos.partial.detaill')
                            @endslot
                        @endcomponent

                        <a title="Editar" class="btn btn-warning btn-sm"
                            href="{{ route('administracion.abonos.edit', $abono->id) }}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? '' }} fa-1x"></i>
                        </a>

                        @php $disabled = ($abono->deleted_at) ? 'disabled':null; @endphp
                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm {{ $disabled }}"
                            href="#" id="btn-destroy_id_{{ $abono->id }}">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach

    </tbody>
</table>

{!! Form::open([
    'route' => ['administracion.abonos.destroy', ':ABONO_ID'],
    'method' => 'DELETE',
    'id' => 'form-destroy',
    'role' => 'form',
]) !!}
{!! Form::close() !!}
@section('scripts')
    @parent
    <script src="{{ asset('js/models/abonos/destroy.js') }}"></script>
@endsection

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.exportBootstrap')
