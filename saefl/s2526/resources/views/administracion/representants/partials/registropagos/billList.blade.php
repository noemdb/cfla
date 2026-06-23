@php $registropagos = $representant->getRegistroPagosGr();@endphp
<div class="alert alert-success rounded py-1">
    Cuotas pagadas por el <span class=" font-weight-bold">Representante</span>
</div>

<table class="table table-striped table-sm small" id="table-data-registropago">
    <thead class="thead-inverse">
        <tr class=" alert-info">
            <th class="">N</th>
            <th class="">Cuotas</th>
            <th class="text-center" title="Monto pagado">Monto</th>
        </tr>
        </thead>
        <tbody id="tdatos">
            @foreach ($registropagos as $registropago)
                <tr data-id="{{$registropago->id}}">
                    <td class="nosort">{{$loop->iteration}}</td>
                    <td class="p-0">
                        <span class="font-weight-bold">{{ $registropago->cuentaxpagar->name ?? '' }}</span>
                    </td>
                    <td class="align-middle text-center">
                        {{ ($registropago->total_exchange_ammount) ? '$ '.f_float($registropago->total_exchange_ammount) : 'Bs '.f_float($registropago->total_pagos_ammount) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
</table>
