@php
    $class_N = 'd-none d-sm-table-cell';
    $class_estudiant = '';
    $class_ci = '';
    $class_grado = '';
    $class_action = '';
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_estudiant }}">Nombre.</th>
            <th class="{{ $class_ci }}">Plan de Pago</th>
            <th class="{{ $class_ci }}">Concepto</th>
            <th class="{{ $class_ci }}">Tipo</th>
            <th class="{{ $class_grado }}">F. Vencimiento</th>
            <th class="{{ $class_ci }}">Monto</th>
            <th class="{{ $class_ci }}">M.Cambairio</th>
            <th class="{{ $class_grado }}">Descripción</th>
            <th class="{{ $class_action }}">Acción</th>
        </tr>
    </thead>

    <tbody id="tdatos">
        @foreach ($concepto_pagos as $concepto_pago)
            @php
                $cuentaxpagar = $concepto_pago->cuentaxpagar;
            @endphp

            <tr data-concepto_pago="{{ $concepto_pago->id }}" data-concepto_pago="{{ $concepto_pago->id ?? '' }}"
                class="table-{{ empty($concepto_pago->administrativa->id) ? 'default' : 'success' }}">
                <td id="td-count" class="{{ $class_N }}">
                    {{ $loop->iteration }}
                </td>
                <td class="{{ $class_email ?? '' }}">
                    {{ $concepto_pago->nomconceptopago->name ?? '' }}
                </td>
                <td class="{{ $class_user ?? '' }}">
                    {{ $cuentaxpagar ? $cuentaxpagar->planpago->name : null }}
                </td>
                <td class="{{ $class_user ?? '' }}">
                    {{ $cuentaxpagar ? $cuentaxpagar->name : null }}
                </td>

                <td class="{{ $class_state ?? '' }}">
                    {{ $cuentaxpagar ? $cuentaxpagar->type : null }}
                </td>
                <td class="{{ $class_state ?? '' }}">
                    {{ $cuentaxpagar ? $cuentaxpagar->date_expiration : null }}
                </td>
                <td class="{{ $class_state ?? '' }}">
                    {{ f_float($concepto_pago->concepto_ammount) }}
                </td>
                <td class="{{ $class_state ?? '' }}">
                    {{ f_float($concepto_pago->exchange_ammount) }}
                </td>
                <td class="{{ $class_state ?? '' }}">
                    {{ $concepto_pago->description ?? '' }}
                </td>

                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $concepto_pago->id }}">
                    <div class="btn-group btn-group-sm">
                        <a title="Editar" class="btn btn-warning btn-sm"
                            href="{{ route('administracion.configuraciones.concepto_pagos.edit', $concepto_pago->id) }}"
                            role="button">
                            <i class="{{ $icon_menus['editar'] ?? '' }} fa-1x"></i>
                        </a>
                        <a title="Eliminar"
                            class="btn-destroy btn btn-danger btn-xs {{ empty($concepto_pago->conceptocancelados->count()) ? '' : ' disabled ' }}"
                            href="#" id="btn-destroy_id_{{ $concepto_pago->id }}">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </td>

            </tr>
        @endforeach

    </tbody>

</table>

@section('scripts')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {
            $('.crt_checkboxes').click(function(e) {
                var div = $(this).parents('div'); //console.log(div); //fila contentiva de la data
                var name = div.data('name');
                console.log(name);
                var checked = $(this).prop('checked');
                console.log(checked);
                $('#' + name).val(checked);
                console.log($('#'.name).val());
            });
        });
    </script>
@endsection

{{-- </div> --}}

@section('stylesheet')
    @parent
    <link rel="stylesheet" href="{{ asset('vendor/datatables/1.10.20/datatables/css/dataTables.bootstrap4.css') }}">
@endsection

@section('scripts')
    @parent
    <script src="{{ asset('vendor/datatables/1.10.20/datatables/js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('vendor/datatables/1.10.20/datatables/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('js/models/datatable/default.js') }}"></script>
@endsection
