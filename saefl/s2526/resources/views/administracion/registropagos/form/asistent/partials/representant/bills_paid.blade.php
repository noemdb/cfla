<div class="text-dark font-weight-bold text-uppercase pb-1 mb-1">Cuotas pagadas</div>
<table class="table table-striped table-sm small" id="table-data-registropago">
    <thead class="thead-inverse">
        <tr class=" alert-info">
            <th class="">N</th>
            <th class="">Cuotas</th>
            {{-- <th class="" title="Fecha de Registro">Fecha Reg.</th> --}}
            <th class="text-center" title="Monto pagado">Monto</th>
        </tr>
        </thead>
        <tbody id="tdatos">
            @php
                $registro_pagos = $representant->getRegistroPagos();
            @endphp
            @foreach ($registro_pagos as $registro_pago)
                <tr data-id="{{$registro_pago->id}}" class="{{ ($registro_pago->status_unexpired) ? 'table-warning': null}}">
                    {{-- <td class="nosort">{{$loop->count - $loop->iteration + 1}}</td> --}}
                    <td class="nosort">{{$loop->remaining + 1}}</td>
                    <td class="p-0">
                        <span class="font-weight-bold">{{ $registro_pago->cuentaxpagar->name ?? '' }}</span>
                    </td>
                    <td class="align-middle text-center">
                        {{ ($registro_pago->total_exchange_ammount) ? '$ '.f_float($registro_pago->total_exchange_ammount) : 'Bs '.f_float($registro_pago->total_pagos_ammount) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
</table>
