@foreach ($estudiant->exchange_expire_bills as $expire_bill)

    @php
        $ammount = ($expire_bill->status_exchange) ? $expire_bill->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id) : $expire_bill->TotalMontoConceptosXPagar($estudiant->id) ;
        $ammount = round($ammount,2);
        $ammount_bs = ($exchange_rate_current) ? ($exchange_rate_current->ammount * $ammount) : null ;
        $ammount_bs = round($ammount_bs,2);
    @endphp

    @admin
    <div>
        Cuota: {{$expire_bill->name ?? null}}
        {{$expire_bill->TotalExchangeMontoCuentasXPagarAdeudado($estudiant->id) ?? null}}
    </div>
    @endadmin

    @if ($ammount>0)

        <div class="border-bottom rounded p-1 mb-2">

            <dl class="mb-1">
                <dt>
                    <div class="font-weight-bolder text-upprecase d-block">
                        {{$expire_bill->name}}
                        <span class="float-right">
                            <span class="border border-danger table-light text-danger rounded p-1 mx-1" style="font-size: 0.7rem" title="{{ ($exchange_rate_current) ? 'TDC: '.f_float($exchange_rate_current->ammount,8) : 'STDC' }} ">
                                Bs. {{ ($exchange_rate_current) ? f_float($ammount_bs) : 'STDC' }}
                            </span>
                            <span class="border border-secondary table-light text-secondary rounded p-1 mx-1">
                                <span class="text-upprecase" style="font-size: 0.7rem" title="Deuda Cambiaria"> $ {{f_float($ammount)}} </span>
                            </span>
                        </span>
                    </div>
                </dt>
            </dl>

            {{-- @if (isset($show_concet) && $show_concet=="true")
                <div class=" pl-2 mb-1">
                    @foreach ($expire_bill->conceptopagos as $conceptopago)
                        @php $ammount_full = $conceptopago->MontoConceptoDescuento($estudiant->id) @endphp
                        @php $ammount = $conceptopago->getTotalXPagar($estudiant->id) @endphp
                        @php $descuento = $estudiant->descuento($conceptopago->cuentaxpagar_id) @endphp
                        @php $ammount_partial = ($ammount <> $ammount_full) ? true : false @endphp
                        <dl class="mb-1 pl-2">
                            <dt class="font-weight-normal">
                                <span class="small {{ ($ammount_partial) ? 'text-primary font-italic':null }}">{{$conceptopago->nomconceptopago->name}}</span>
                                <span class="badge badge-light text-{{ ($descuento) ? 'success' : 'dark' }} float-right" title="{{f_float($ammount_full)}}">
                                    <span class="strike">Bs. {{f_float($ammount)}}</span>
                                </span>
                            </dt>
                        </dl>
                    @endforeach
                </div>
            @endif --}}

        </div>
    @endif
    <div class="dropdown-divider mb-0"></div>
@endforeach
