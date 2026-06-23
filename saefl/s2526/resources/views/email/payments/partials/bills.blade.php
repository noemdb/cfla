<div style="text-align: right;">
        @php $exchange_ammount_expire_bill = $representant->exchange_ammount_expire_bill @endphp
        @if ($exchange_ammount_expire_bill > 0)
            <div>
                <div>Para la fecha usted tiene una deuda de: <b style="font-size:1.8rem !important">$ {{f_float($exchange_ammount_expire_bill)}}</b></div>

                {{--
                @if ($late_index > 20) <div style="color: #DC7D7D; font-weight: bold"> Índice de Morosidad: {{$late_index ?? '' }} % </div>
                @else <div style="color: #007BFF; font-weight: bold"> Índice de Cumplimiento de Pago: {{$meet_index ?? '' }} % </div>
                @endif
                --}}

                <hr>

                @php $expire_bills = $representant->ExchangeExpireBillPendientes @endphp

                    <span style="font-size: 0.9rem !important;color:red"><b>Sus cuotas vencidas son:</b></span>

                    <div style="font-size: 0.9rem !important;margin-bottom:0.1rem">
                    @foreach ($expire_bills as $expire_bill)
                        <div style="text-align: right;">{{$loop->iteration}}. {{ucfirst_accents($expire_bill['expire_bill_name'])}}: <b>${{f_float($expire_bill['ammount'])}}</b> - F.Vencimiento: {{f_date($expire_bill['date_expiration'])}}</div>
                    @endforeach
                    </div>

            </div>

        @else
            <div style="color: green; font-weight: bold"><strong>SOLVENTE</strong></div>
        @endif

        @php
            $unexpire_bills = $representant->ExchangeUnexpireBillPendientes;
            $unexpire_bill = ($unexpire_bills->isNotEmpty()) ? $unexpire_bills->first() : null;
        @endphp

        {{-- {{$representant->ExchangeUnexpireBillPendientes ?? 'no hay'}} --}}

        {{-- {{$unexpire_bills ?? 'nada'}} --}}

        @if ($unexpire_bill)
            <hr style="margin-top:0.1rem;margin-bottom:0.1rem">
            <span style="font-size: 0.9rem !important;color: #CE5C00"><b>Su próxima cuota a vencer es:</b></span>

            <div style="font-size: 0.9rem !important">{{ucfirst_accents($unexpire_bill['expire_bill_name'])}}: <b>${{f_float($unexpire_bill['ammount'])}}</b>, F.Vencimiento: {{f_date($unexpire_bill['date_expiration'])}}</div>
        @endif

</div>

