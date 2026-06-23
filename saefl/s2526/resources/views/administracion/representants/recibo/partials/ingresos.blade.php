<table width="100%" cellpadding="0" cellspacing="0"
    style=" font-size:0.6rem;margin-bottom:0.1rem; padding-bottom:0rem;border:1px solid #ccc; margin-top: 0.4rem;">
    <thead>
        <tr align="left">
            <th style="">
                <span style="font-size:0.6rem !important;font-weight: bold;">DESCRIPCIÓN</span>
            </th>
            <th style="">
                <span style="font-size:0.6rem !important;font-weight: bold;">MONTO</span>
            </th>
            <th style="">
                <span style="margin-left: 0.5rem;font-size:0.6rem !important;font-weight: bold; ">OBSERVACIONES</span>
            </th>
        </tr>
    </thead>

    <tr>
        <td>MONTO PAGADO</td>
        <td>
            {!! money($ammount_pagado, $ammount_pagado_exchange) !!}
        </td>
        <td style="margin-left: 0.3rem;">&nbsp;</td>
    </tr>
    <tr>
        <td class="no_wrap">CREDITO A FAVOR GENERADO</td>
        <td>
            <div style="margin-right: 0.2rem;">
                {!! money($ammount_creditos_generados, $ammount_creditos_generados_exchange) !!}
            </div>
        </td>
        <td style="margin-left: 0.1rem;">&nbsp;</td>
    </tr>
    <tr>
        <td>OPERACIONES</td>
        <td>
            <div style="margin-left: 0.5rem;">
                {!! money($ammount_ingresos, $ammount_ingresos_exchange) !!}
            </div>
        </td>
        <td>
            <div style="margin-left: 0.5rem;">
                @foreach ($registro_pago_combinado->ingresos as $ingreso)
                    @php $exchange_rate = $ingreso->exchange_rate; @endphp
                    @if (!empty($ingreso->id))
                        {{ $loop->iteration ?? '' }}.-
                        {{ $ingreso->banco_name ?? '' }},
                        {{ $ingreso->date_transaction->format('d-m-Y') ?? '' }},
                        REF: <i>{{ $ingreso->number_i_pay ?? '' }}</i>,
                        Monto:

                        {!! money($ingreso->ingreso_ammount, $ingreso->exchange_ammount, true) !!}

                        [<span>TDC_BCV: B<span style="text-transform: lowercase">s</span>
                            {{ f_float($ingreso->exchange_rates_ammount) }}</span>],
                        OBS: {{ $ingreso->ingreso_observations ?? '' }}
                        @if (!$loop->last)
                            <br>
                        @endif
                    @endif
                @endforeach
            </div>
        </td>
    </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0"
    style=" font-size:0.6rem;margin-bottom:0.1rem; padding-bottom:0.1rem;border:1px solid #ccc; margin-top: 0.4rem;">
    <tr>
        <td style="border: 0;">
            <div style="font-size: 0.6rem; color:#666;text-align:right; margin-bottom:0.1rem; padding-bottom:0.05rem;">
                <div>Total Tranferencias Electŕonicas y/o otros medios de pago:
                    {!! money($total_ingresos, $total_ingresos_exchange) !!}
                </div>
            </div>
        </td>
    </tr>
</table>
