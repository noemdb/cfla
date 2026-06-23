
<table width="100%" cellpadding="0" cellspacing="0" style=" font-size:0.7rem;margin-bottom:0.2rem; padding-bottom:0.2rem;border:1px solid #ccc">
    <tr>
        <th align="left"><div style="font-size: 0.8rem"><strong>CORRESPONDIENTE A:</strong></div></th>
    </tr>
    @forelse ($registropagos as $registropago)
        <tr align="left">
            <th align="left" style="padding-left: 0.4rem">CONCEPTO: {{$registropago->cuentaxpagar->name ?? ''}}</th>
            <th align="right">
                {!! money($registropago->ammount,$registropago->exchange_ammount)!!}
            </th>
        </tr>
    @empty

    @endforelse
    <tr style="background-color: #ccc;">
        <th colspan="1" align="left" style="padding-left: 0.4rem;padding-top: 0.2rem;padding-bottom: 0.2rem">Total recibido:</th>
        <th align="right" style="padding-top: 0.2rem;padding-bottom: 0.2rem">{!! money($ammount_pagado,$ammount_pagado_exchange)!!}</th>
    </tr>

</table>

