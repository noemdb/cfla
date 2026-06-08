<div style="font-size: 0.8rem"><strong>CORRESPONDIENTE A:</strong></div>
<table width="100%" cellpadding="0" cellspacing="0" style=" font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;border:1px solid #ccc">
    @forelse ($registropagos as $registropago)
        <tr align="left">
            <th align="left" width="15%" style="padding-left: 0.4rem">CONCEPTO:</th>
            <th>{{$registropago->cuentaxpagar->name ?? ''}}</th>
            <th>{{f_float($registropago->exchange_ammount)}}</th>
        </tr>
    @empty
        {{-- <tr> <td colspan="2">No hay operacionesde vuelto</td> </tr> --}}
    @endforelse

</table>

<div style="margin-bottom:0.2rem;padding-bottom:0.2rem;font-size: 0.8rem !important;border:1px solid #ccc;">
    <strong>Observaciones:</strong>
    @forelse ($ingreso_cashs as $ingreso)
        {{$ingreso->ingreso_observations}},
    @empty
    @endforelse
</div>

<div style="font-size: 0.8rem;text-align:right; border:1px solid #ccc;">
    <b>TOTAL PAGADO: {{f_float($ammount_pagado_exchange)}}</b>
</div>
