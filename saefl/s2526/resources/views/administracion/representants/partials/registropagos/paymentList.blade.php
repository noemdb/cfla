@php $registropagos = $representant->getRegistroPagosGr();@endphp
<div class="alert alert-success rounded mb-0">
    Cuotas pagadas por el <span class=" font-weight-bold">Representante</span> aceptadas por la <b>Dirección de Administración</b>
</div>

<table class="table table-striped table-sm small">
    <thead class="">
        <tr class="alert-secondary">
            <th class="">N</th>
            <th class="">Cuotas</th>
            <th class="text-center" title="Monto pagado">Monto</th>
        </tr>
    </thead>
    <tbody id="tdatos">
        @foreach ($registropagos as $registropago)
            <tr data-id="{{$registropago->id}}">
                <td class="nosort">{{$loop->remaining + 1}}</td>
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
